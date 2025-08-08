<?php
date_default_timezone_set('Asia/Jakarta');

require_once(__DIR__."/vendor/autoload.php");
require_once(__DIR__."/../html/api/const.php");
require_once(__DIR__."/db.php");
require_once(__DIR__."/fn.db.php");

define("DBMEM",2);
define("DBDISK",2);
define("DBPG",0);

//$url = "ws://localhost:9090/sppa-fet/admin/ws?id=GUI";
$url = getenv('WSC_URL') ?? "ws://sppafet-admin-net:80/sppa-fet/admin/ws?id=GUI";
$active = true;

$sql = "insert into wsc_log(msg,tgl) values (?,?)";
$ins = "insert into wsc_box(appId,totalCpu,userPercent,systemPercent,idlePercent,totalMemory,userMemory,systemMemory,idleMemory,lastUpdate)
    values (:appId,:totalCpu,:userPercent,:systemPercent,:idlePercent,:totalMemory,:userMemory,:systemMemory,:idleMemory,:lastUpdate)";
$upd = "update wsc_box set 
    totalCpu=:totalCpu,
    userPercent=:userPercent,
    systemPercent=:systemPercent,
    idlePercent=:idlePercent,
    totalMemory=:totalMemory,
    userMemory=:userMemory,
    systemMemory=:systemMemory,
    idleMemory=:idleMemory,
    lastUpdate=:lastUpdate
    where appId=:appId";
$istat = "insert into wsc_stat(appId,rfoRequest,approved,rejected,trade,error,send,lastUpdate)
    values (:appId,:rfoRequest,:approved,:rejected,:trade,:error,:send,:lastUpdate)";
$ustat = "update wsc_stat set 
    rfoRequest=:rfoRequest,
    approved=:approved,
    rejected=:rejected,
    trade=:trade,
    error=:error,
    send=:send,
    lastUpdate=:lastUpdate
    where appId=:appId";
$ilog = "insert into wsc_login(appId, login,lastUpdate) values (:appId, :login, :tgl)";
$ulog = "update wsc_login set login=:login, lastUpdate=:tgl where appId=:appId";
$err = "insert into wsc_err(msg,sql,prm,tgl) values (?,?,?,?)";

function logger($log,$app,$id,$data) {
    global $sql;

    $data = array(
        "logType" => $log,
        "appType" => $app,
        "appId" => $id,
        "data" => $data
    );
    DBX(DBMEM)->run($sql,array(json_encode($data),date("Y-m-d H:i:s")));
}

$lastTgl = date("Y-m-d");
function cekTgl() {
    global $lastTgl;

    if ($lastTgl != date("Y-m-d")) {
        $cm1 = "delete from wsc_log";
        $cm2 = "update wsc_stat set rfoRequest=0, approved=0, rejected=0, trade=0, error=0, send=0, lastUpdate='".date("Y-m-d H:i:s")."'";
        $cm3 = "update wsc_login set login=0, lastUpdate='".date("Y-m-d H:i:s")."'";
        try {
            DBX(DBMEM)->run($cm1);
        } catch (Exception $e) {
            echo date("Y-m-d H:i:s")." WSC_LOG ".$e->getMessage()."\n";
        }
        try {
            DBX(DBMEM)->run($cm2);
        } catch (Exception $e) {
            echo date("Y-m-d H:i:s")." WSC_STAT ".$e->getMessage()."\n";
        }
        try {
            DBX(DBMEM)->run($cm3);
        } catch (Exception $e) {
            echo date("Y-m-d H:i:s")." WSC_LOGIN ".$e->getMessage()."\n";
        }
        try {
            DBX(DBMEM)->exec("VACUUM");
        } catch (Exception $e) {
            echo date("Y-m-d H:i:s")." VACUUM ".$e->getMessage()."\n";
        }
        $lastTgl = date("Y-m-d");
        return true;
    }
    return false;
}

initdb();

try {
    echo "Loading data...\n";
    $xd = array(
        "STAT" => array(),
        "EVNT" => array()
    );
    $c1 = "select * from logging where inserted_at > ?";
    $cp = array(date("Y-m-d"));
    $st = DBX(DBPG)->run($c1, $cp);
    while ($row = $st->fetch()) {
        $tbh = false;
        $dat = json_decode($row["data"]);
        if ($row["log_type"] == "STAT") $tbh = true;
        if (($row["log_type"] == "EVNT") && (($dat->description=="FIX Client logon") || ($dat->description=="FIX Client logout"))) $tbh = true;

        if ($tbh) $xd[$row["log_type"]][$row["app_id"]] = $row["data"];
    }
    print_r($xd);
    if (count($xd["STAT"])>0) {
        foreach ($xd["STAT"] as $key => $val) {
            $parm = (array) json_decode($val);
            $parm["appId"] = $key;
            $parm["lastUpdate"] = date("Y-m-d H:i:s");
            //print_r($parm);
            $st = DBX(DBDISK)->run($ustat,$parm);
            if ($st->rowCount() == 0) {
                DBX(DBDISK)->run($istat,$parm);
            }
        }
    }
    if (count($xd["EVNT"])>0) {
        foreach ($xd["EVNT"] as $key => $val) {
            $o = (array) json_decode($val);
            $parm = array();
            $parm["appId"] = $key;
            $parm["login"] = $o["description"] == "FIX Client logon" ? 1 : 0;
            $parm["tgl"] = date("Y-m-d H:i:s");
            $st = DBX(DBDISK)->run($ulog,$parm);
            if ($st->rowCount() == 0) {
                DBX(DBDISK)->run($ilog,$parm);
            }
        }
    }
} catch (Exception $e) {
    echo date("Y-m-d H:i:s")." [ERROR:PREV_DATA] ".$e->getMessage()."\n".$e->getTraceAsString();
}

//die();

logger("WSC","STARTUP","LOGGER",array("msg"=>"Starting"));

while ($active) {
    $ctr = 0;
    $reconnect = false;
    $client = null;
    $pmsg = "";
    echo date("Y-m-d H:i:s")." Connecting to $url ...\n";
    try {
        $client = new WebSocket\Client($url);
    } catch (\WebSocket\ConnectionException $e) {
        print_r($e);
        die();
    }
    while (!$reconnect) {
        $arr = array();
        $parm = array();
        $cek = false;
        $stat = false;
        $lgin = false;
        $lout = false;
        try {
            //$client->text("FET-B|LOG|BMRI|INFO|Database transaction connected|FET-E|");
            $msg = $client->receive();
            // store to db;
            $tgl = date("Y-m-d H:i:s");
            $prm = array($msg, $tgl);
            DBX(DBMEM)->run($sql,$prm);
            echo $tgl." ". $msg . "\n";
            //sleep(1);
            $arr = json_decode($msg,true);
            $cek = $arr["logType"] == "METR";
            $stat = $arr["logType"] == "STAT";

            $de = $arr["data"];
            $lgin = $arr["logType"] == 'EVNT' && $arr["appType"] == 'FIX' && $de["description"] == "FIX Client logon";
            $lout = $arr["logType"] == 'EVNT' && $arr["appType"] == 'FIX' && $de["description"] == "FIX Client logout";
        } catch (\WebSocket\ConnectionException $e) {
            $tgl = date("Y-m-d H:i:s");
            $msg = $e->getMessage();
            //$prm = array($msg, $tgl);
            //DBX(1)->run($sql,$prm);
            if ($pmsg != $msg) {
                echo $tgl." ". $msg . "\n";
                $pmsg = $msg;
            }

            $reconnect = !$client->isConnected();
        }
        if ($cek) {
            try {
                $parm = $arr["data"];
                $parm["appId"] = $arr["appId"];
                $parm["lastUpdate"] = date("Y-m-d H:i:s");
                $st = DBX(DBDISK)->run($upd,$parm);
                if ($st->rowCount() == 0) {
                    DBX(DBDISK)->run($ins,$parm);
                }
            } catch (Exception $e) {
                DBX(DBDISK)->run($err, array($e->getMessage(), $upd, json_encode($parm), date("Y-m-d H:i:s")));
            }
        }
        if ($stat) {
            try {
                $parm = $arr["data"];
                $parm["appId"] = $arr["appId"];
                $parm["lastUpdate"] = date("Y-m-d H:i:s");
                $st = DBX(DBDISK)->run($ustat,$parm);
                if ($st->rowCount() == 0) {
                    DBX(DBDISK)->run($istat,$parm);
                }
            } catch (Exception $e) {
                DBX(DBDISK)->run($err, array($e->getMessage(), $ustat, json_encode($parm), date("Y-m-d H:i:s")));
            }
        }
        if ($lgin) {
            try {
                //$parm = $arr["data"];
                $parm["appId"] = $arr["appId"];
                $parm["login"] = 1;
                $parm["tgl"] = date("Y-m-d H:i:s");
                $st = DBX(DBDISK)->run($ulog,$parm);
                if ($st->rowCount() == 0) {
                    DBX(DBDISK)->run($ilog,$parm);
                }
            } catch (Exception $e) {
                DBX(DBDISK)->run($err, array($e->getMessage(), $ustat, json_encode($parm), date("Y-m-d H:i:s")));
            }
        }
        if ($lout) {
            try {
                //$parm = $arr["data"];
                $parm["appId"] = $arr["appId"];
                $parm["login"] = 0;
                $parm["tgl"] = date("Y-m-d H:i:s");
                $st = DBX(DBDISK)->run($ulog,$parm);
                if ($st->rowCount() == 0) {
                    DBX(DBDISK)->run($ilog,$parm);
                }
            } catch (Exception $e) {
                DBX(DBDISK)->run($err, array($e->getMessage(), $ustat, json_encode($parm), date("Y-m-d H:i:s")));
            }
        }
        $ctr++;
        if ($ctr > 10) {
            cekTgl();
            $ctr = 0;
        }
    }
    $client->close();
    logger("WSC","ERROR","LOGGER",array("msg"=>"Reconnecting"));
    //usleep(500000); // sleep for 0.5 second
    sleep(3); // sleep 3s
    echo "\n";
}
