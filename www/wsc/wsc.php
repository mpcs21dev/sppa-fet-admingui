<?php
date_default_timezone_set('Asia/Jakarta');

require_once("vendor/autoload.php");
require_once("db.php");
require_once("fn.db.php");

$url = "ws://sppafet-admin-net:9090/sppa-fet/admin/ws?id=GUI";
//$url = "wss://echo.websocket.org/";
$active = true;
$sql = "insert into wsc_log(msg,tgl) values (?,?)";
$ins = "insert into public.wsc_box(appId,totalCpu,userPercent,systemPercent,idlePercent,totalMemory,userMemory,systemMemory,idleMemory,lastUpdate)
    values (:appId,:totalCpu,:userPercent,:systemPercent,:idlePercent,:totalMemory,:userMemory,:systemMemory,:idleMemory,:lastUpdate)";
$upd = "update public.wsc_box set 
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
$istat = "insert into public.wsc_stat(appId,rfoRequest,approved,rejected,trade,error,send,lastUpdate)
    values (:appId,:rfoRequest,:approved,:rejected,:trade,:error,:send,:lastUpdate)";
$ustat = "update public.wsc_stat set 
    rfoRequest=:froRequest,
    approved=:approved,
    rejected=:rejected,
    trade=:trade,
    error=:error,
    send=:send,
    lastUpdate=:lastUpdate
    where appId=:appId";
$ilog = "insert into public.wsc_login(appId, login,lastUpdate) values (:appId, :login, :tgl)";
$ulog = "update public.wsc_login set login=:login, lastUpdate=:tgl where appId=:appId";
$err = "insert into public.wsc_err(msg,sql,prm,tgl) values (?,?,?,?)";

function logger($log,$app,$id,$data) {
    global $sql;

    $data = array(
        "logType" => $log,
        "appType" => $app,
        "appId" => $id,
        "data" => $data
    );
    DBX(1)->run($sql,array(json_encode($data),date("Y-m-d H:i:s")));
}

initdb();
logger("WSC","STARTUP","LOGGER",array("msg"=>"Starting"));

while ($active) {
    $reconnect = false;
    $client = null;
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
            DBX(1)->run($sql,$prm);
            echo $tgl." ". $msg . "\n";
            //sleep(1);
            $arr = json_decode($msg,true);
            $cek = $arr["logType"] == "METR";
            $stat = $arr["logType"] == "STAT";

            $de = json_decode($arr["data"]);
            $lgin = $arr["logType"] == 'EVNT' && $arr["appType"] == 'FIX' && $de["description"] == "FIX Client logon";
            $lout = $arr["logType"] == 'EVNT' && $arr["appType"] == 'FIX' && $de["description"] == "FIX Client logout";
        } catch (\WebSocket\ConnectionException $e) {
            $tgl = date("Y-m-d H:i:s");
            $msg = $e->getMessage();
            //$prm = array($msg, $tgl);
            //DBX(1)->run($sql,$prm);
            if (trim($msg) != "Client read timeout") echo $tgl." ". $msg . "\n";

            $reconnect = !$client->isConnected();
        }
        if ($cek) {
            try {
                $parm = $arr["data"];
                $parm["appId"] = $arr["appId"];
                $parm["lastUpdate"] = date("Y-m-d H:i:s");
                $st = DBX(0)->run($upd,$parm);
                if ($st->rowCount() == 0) {
                    DBX(0)->run($ins,$parm);
                }
            } catch (Exception $e) {
                DBX(0)->run($err, array($e->getMessage(), $upd, json_encode($parm), date("Y-m-d H:i:s")));
            }
        }
        if ($stat) {
            try {
                $parm = $arr["data"];
                $parm["appId"] = $arr["appId"];
                $parm["lastUpdate"] = date("Y-m-d H:i:s");
                $st = DBX(0)->run($ustat,$parm);
                if ($st->rowCount() == 0) {
                    DBX(0)->run($istat,$parm);
                }
            } catch (Exception $e) {
                DBX(0)->run($err, array($e->getMessage(), $ustat, json_encode($parm), date("Y-m-d H:i:s")));
            }
        }
        if ($lgin) {
            try {
                //$parm = $arr["data"];
                $parm["appId"] = $arr["appId"];
                $parm["login"] = 1;
                $parm["tgl"] = date("Y-m-d H:i:s");
                $st = DBX(0)->run($ulog,$parm);
                if ($st->rowCount() == 0) {
                    DBX(0)->run($ilog,$parm);
                }
            } catch (Exception $e) {
                DBX(0)->run($err, array($e->getMessage(), $ustat, json_encode($parm), date("Y-m-d H:i:s")));
            }
        }
        if ($lout) {
            try {
                //$parm = $arr["data"];
                $parm["appId"] = $arr["appId"];
                $parm["login"] = 0;
                $parm["tgl"] = date("Y-m-d H:i:s");
                $st = DBX(0)->run($ulog,$parm);
                if ($st->rowCount() == 0) {
                    DBX(0)->run($ilog,$parm);
                }
            } catch (Exception $e) {
                DBX(0)->run($err, array($e->getMessage(), $ustat, json_encode($parm), date("Y-m-d H:i:s")));
            }
        }
    }
    $client->close();
    logger("WSC","ERROR","LOGGER",array("msg"=>"Reconnecting"));
    //usleep(500000); // sleep for 0.5 second
    sleep(3); // sleep 3s
    echo "\n";
}
