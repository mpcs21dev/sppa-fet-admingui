<?php
/*
require_once("htmlpurifier-4.14.0/HTMLPurifier.standalone.php");
$PurConfig = HTMLPurifier_Config::createDefault();
$XPuri = new HTMLPurifier($PurConfig);
//$clean_html = $XPuri->purify($dirty_html);
*/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function withSchema($tblname) {
    if (DEF_SCHEMA == "") {
        return $tblname;
    } else {
        return DEF_SCHEMA.".".$tblname;
    }
}

function db_init() {
    $sql = "CREATE TABLE IF NOT EXISTS CMS_User (
        ID 			INTEGER PRIMARY KEY AUTOINCREMENT,
        Uid 		VARCHAR(128) collate nocase UNIQUE NOT NULL,
        Pwd 		VARCHAR(256) NOT NULL,
        UserName 	VARCHAR(128) NULL,
        LastUpdate 	DATETIME NULL,
        UserID 		INTEGER NULL,
        Void 		INTEGER DEFAULT 0 NOT NULL
    )";
    DBX(DB_DATA)->run($sql);

    $sql = "CREATE TABLE IF NOT EXISTS CMS_Banner(
        ID 			INTEGER PRIMARY KEY AUTOINCREMENT,
        Name 		VARCHAR(64) NOT NULL,
        Label 		VARCHAR(64) NULL,
        BgColor 	VARCHAR(16) NULL,
        BgImage 	VARCHAR(128) NULL,
        ImageType 	VARCHAR(64) NULL,
        Icon 		VARCHAR(128) NULL,
        IconType 	VARCHAR(64) NULL,
        Link        VARCHAR(256) NULL,
        UserID 		INTEGER NULL,
        LastUpdate 	DATETIME NULL,
        Void 		INTEGER DEFAULT 0 NOT NULL
    )";
    DBX(DB_DATA)->run($sql);

    $sql = "CREATE TABLE IF NOT EXISTS CMS_Log(
        ID          INTEGER PRIMARY KEY AUTOINCREMENT,
        IPAddress   VARCHAR(64) NULL,
        UserID      INTEGER NOT NULL,
        Action      VARCHAR(50) NULL,
        TableName   VARCHAR(256) NULL,
        TableID     INTEGER NULL,
        DataBefore  VARCHAR(1024) NULL,
        DataAfter   VARCHAR(1024) NULL,
        LastUpdate  DATETIME NULL
    )";
    DBX(DB_DATA)->run($sql);

    $sql = "CREATE TABLE IF NOT EXISTS CMS_BannerImage(
        ID          INTEGER PRIMARY KEY AUTOINCREMENT,
        Banner_ID   INTEGER NOT NULL,
        BGImageBase64 VARCHAR(1024) NULL,
        BGImageBin  VARCHAR(1024) NULL,
        IconBase64  VARCHAR(1024) NULL,
        IconBin     VARCHAR(1024) NULL,
        UserID      INTEGER NULL,
        LastUpdate  DATETIME NULL
    )";
    DBX(DB_DATA)->run($sql);

    $sql = "CREATE VIEW IF NOT EXISTS V_User
        AS
        SELECT CMS_User.ID, CMS_User.Uid, CMS_User.Pwd, CMS_User.UserName, CMS_User.LastUpdate, CMSUser.Uid AS UpdateBy, CMS_User.UserID, CMS_User.Void
        FROM CMS_User LEFT OUTER JOIN
            CMS_User AS CMSUser ON CMS_User.UserID = CMSUser.ID
    ";
    DBX(DB_DATA)->run($sql);

    $sql = "CREATE VIEW IF NOT EXISTS V_Banner
        AS
        SELECT CMS_Banner.ID, CMS_Banner.Name, CMS_Banner.Label, CMS_Banner.BgColor, CMS_Banner.BgImage, CMS_Banner.Icon, CMS_Banner.Link, CMS_Banner.LastUpdate, CMS_User.Uid AS UpdateBy, 
            CMS_Banner.ImageType, CMS_Banner.IconType, CMS_Banner.Void
        FROM CMS_Banner LEFT OUTER JOIN
            CMS_User ON CMS_Banner.UserID = CMS_User.ID
    ";
    DBX(DB_DATA)->run($sql);

    /*
    $sql = "";
    DB::run($sql);
    */
}


function isMultiKey($varkey) {
    if (!is_array($varkey)) return false;
    if (count($varkey) <= 1) return false;
    return true;
}
function getKey($varkey) {
    if (!is_array($varkey)) return $varkey;
    if (count($varkey)==1) return array_values($varkey)[0];
    return $varkey;
}
function isKey($needle, $haystack) {
    if (is_array($haystack)) {
        return in_array($needle, $haystack);
    } else {
        return $needle == $haystack;
    }
}
function asArray($vals) {
    if (is_array($vals)) return $vals;
    return array($vals);
}

function data_create($table, $keyfld, $json, $emptyasnull=false, $insertkey=false, $dbx=0) {
    $obj = $json;

    $keyfield = getKey($keyfld);
    $inskey = $insertkey;
    if (isMultiKey($keyfield)) $inskey = true;    // if keyfield > 1 ~ insertkey auto true;

    $fld = array();
    $val = array();
    $prm = array();

    foreach ($obj as $key => $value) {
        if (isKey($key,$keyfield) && !$inskey) continue;

        $fld[] = $key;
        $val[] = ($emptyasnull && ($value == "")) ? null : $value;
        $prm[] = "?";
    }

    //$sql = "insert into {$table} ( ".implode(',', $fld)." ) values ( ".implode(',', $prm)." ) ";
    $sql = SFormat(
        "insert into {0} ({1}) values ({2}) ",
        array($table, implode(',',$fld), implode(',',$prm))
    );
    //echo $sql;
    //print_r($val);
    DBX($dbx)->run($sql, $val);
    if (!$inskey){
        $id = DBX($dbx)->lastInsertId();
        $obj[$keyfield] = $id;
    }
    //$obj->$keyfield = $id;

    return $obj;
}
function data_update($table, $keyfld, $json, $dbx=0, $selectFrom = "") {
    $obj = $json;
    $keyfield = getKey($keyfld);

    $fld = "";
    $val = array();
    $whr = "";
    $val_key = array();

    foreach ($obj as $key => $value) {
        if (isKey($key,$keyfield)) continue;
        if ($fld != "") $fld .= ", ";
        $fld .= "{$key} = ?";
        $val[] = $value;
    }
    if (isMultiKey($keyfield)) {
        foreach($keyfield as $key) {
            if ($whr != "") $whr .= " and ";
            $whr .= "{$key}=?";
            $val_key[] = $obj[$key];
        }
    } else {
        $whr = "{$keyfield}=?";
        $val_key[] = $obj[$keyfield];
    }

    $arm = array_merge($val, $val_key);
    $sql = "update {$table} set {$fld} where {$whr}";

    //print_r($sql);
    //print_r($arm);
    try {
        DBX($dbx)->run($sql, $arm);
    } catch (Exception $e) {
        return array("error"=>e->getCode(),"message"=>e->getMessage(),"sql"=>$sql,"prm"=>$arm);
    }

    if ($selectFrom == "") {
        return data_read($table, $keyfield, $val_key, $dbx);
    } else {
        return data_read($selectFrom, $keyfield, $val_key, $dbx);
    }
}
function data_read($table, $keyfld, $keyval, $dbx=0) {
    $keyfield = getKey($keyfld);
    $keyvalue = asArray($keyval);

    $whr = "";
    if (is_array($keyfield)) {
        foreach ($keyfield as $key) {
            if ($whr != "") $whr .= " and ";
            $whr .= "{$key}=?";
        }
    } else {
        $whr = "{$keyfield}=?";
    }
    $sql = "select * from {$table} where {$whr}";
    $row = DBX($dbx)->run($sql, $keyvalue)->fetchAll();
    if (count($row)>0) {
        return $row[0];
    } else {
        return null;
    }
}
function data_filter($table, $where, $dbx=0) {
    $w = "";
    $v = array();
    if (count($where)>0) {
        foreach ($where as $h) {
            $e = "(";
            $e = $h[0] . $h[1] . "?";
            $v[] = $h[2];
            
            if ($w != "") $w .= " and ";
            $w .= $e;
        }
    }
    $sql = "select * from {$table} where {$w}";
    $row = DBX($dbx)->run($sql, $v)->fetchAll();
    if (count($row)>0) {
        return $row[0];
    } else {
        return null;
    }
}
function data_lookup($table, $keyfld, $keyval, $lkpfld, $dbx=0){
    $row = data_read($table, $keyfld, $keyval, $dbx);
    if ($row != null) {
        return $row[$lkpfld];
    } else {
        return null;
    }
}

function data_mlookup($table, $fld, $whr, $dbx=0) {
    $row = data_filter($table, $whr, $dbx);
    if ($row != null) {
        return $row[$fld];
    } else {
        return null;
    }
}

function data_delete($table, $keyfld, $keyval, $dbx=0) {
    $keyfield = getKey($keyfld);
    $keyvalue = asArray($keyval);
    $data = data_read($table, $keyfield, $keyvalue, $dbx);

    $whr = "";
    if (is_array($keyfield)) {
        foreach ($keyfield as $key) {
            if ($whr != "") $whr .= " and ";
            $whr .= "{$key}=?";
        }
    } else {
        $whr = "{$keyfield}=?";
    }
    $sql = "delete from {$table} where {$whr}";

    DBX($dbx)->run($sql, $keyvalue);

    return $data;
}
function data_list($table, $do_count=false, $page=1, $rpp=50, $oby=array(), $where=array(), $dbx=0) {
    $sql = "select * from {$table}";
    return data_list_sql($sql, $do_count, $page, $rpp, $oby, $where, $dbx);
}
function data_list_sql($list_sql, $do_count=false, $page=1, $rpp=50, $oby=array(), $where=array(), $dbx=0) {
    $hasil = array();
    $hasil["debug"] = array();

    $lmt = $rpp;
    $ofs = ($page-1) * $rpp;

    $orderby = "";
    foreach ($oby as $row) {
        if (strlen($orderby)>0) $orderby .= ",";
        $orderby .= "{$row["field"]} {$row["dir"]}";
    }

    $strWhere = "";
    $arrWhere = array();
    if (count($where)>0) {
        foreach ($where as $o) {
            if ($strWhere != "") $strWhere .= " and ";
            if ($o["type"] == "like") {
                $strWhere .= "LOWER(".$o["field"].") ".$o["type"]." LOWER(?)";
                $arrWhere[] = "%".$o["value"]."%";
            } else if ($o["type"] == "eq") {
                //$strWhere .= "LOWER(cast(".$o["field"]." as text)) = LOWER((cast ? as text))";
                $strWhere .= $o["field"]." = ?";
                $arrWhere[] = $o["value"];
            } else {
                $strWhere .= $o["field"]." ".$o["type"]." ?";
                $arrWhere[] = $o["value"];
            }
        }
    }
    if ($strWhere != "") $strWhere = "where ".$strWhere;

    $sql = "select * from (".$list_sql.") h__tbl ".$strWhere. " ";
    $sqlc = "select count(*) ctr from ({$sql}) t1";
    //$sql .= ($orderby=="" ? "" : "order by ".$orderby) . " offset {$ofs} rows fetch first {$lmt} rows only";
    $sql .= ($orderby=="" ? "" : "order by ".$orderby);
    if (DBX($dbx)->engine == "MSSQL") {
        $sql .= " offset {$ofs} rows fetch first {$lmt} rows only";
    } else {
        $sql .= " limit {$lmt} offset {$ofs}";
    }

/*
    var_dump($sql);
    echo "\n\n";
*/

    $pages = 1;
    $count = 0;
    if ($do_count) {
        $count = DBX($dbx)->run($sqlc, $arrWhere)->fetchColumn();
        $mods = $count % $rpp;
        $pages = intval(floor($count / $rpp));
        if ($mods > 0) $pages += 1;
    }

    $rows = null;
    try {
        $rows = DBX($dbx)->run($sql, $arrWhere)->fetchAll();
    } catch (Exception $e) {
        $hasil["debug"][] = $e->getMessage();
        //
    }

    //$hasil["table"] = $table;
    $hasil["count"] = $count;
    $hasil["rows"] = $rows;
    $hasil["rpp"] = $rpp;
    $hasil["page"] = $page;
    $hasil["pages"] = $pages;
    //$hasil["orderby"] = $orderby;
    //$hasil["where"] = $where;
    $hasil["debug"][] = $sql;

    return $hasil;
}
function data_list_sql_nopage($list_sql, $oby=array(), $where=array(), $dbx=0){
    $orderby = "";
    foreach ($oby as $row) {
        if (strlen($orderby)>0) $orderby .= ",";
        $orderby .= "{$row["field"]} {$row["dir"]}";
    }

    $strWhere = "";
    $arrWhere = array();
    if (count($where)>0) {
        foreach ($where as $o) {
            if ($strWhere != "") $strWhere .= " and ";
            if ($o["type"] == "like") {
                $strWhere .= "LOWER(".$o["field"].") ".$o["type"]." LOWER(?)";
                $arrWhere[] = "%".$o["value"]."%";
            } else if ($o["type"] == "eq") {
                $strWhere .= "LOWER(".$o["field"].") = LOWER(?)";
                $arrWhere[] = $o["value"];
            } else {
                $strWhere .= $o["field"]." ".$o["type"]." ?";
                $arrWhere[] = $o["value"];
            }
        }
    }
    if ($strWhere != "") $strWhere = "where ".$strWhere;

    $sql = "select * from (".$list_sql.") h__tbl ".$strWhere. " ";
    $sql .= ($orderby=="" ? "" : "order by ".$orderby);

    $hasil = array();
    $hasil["debug"] = array();

    $rows = null;
    try {
        $rows = DBX($dbx)->run($sql, $arrWhere)->fetchAll();
    } catch (Exception $e) {
        $hasil["debug"][] = $e->getMessage();
    }
    $hasil["rows"] = $rows;
    $hasil["debug"][] = $sql;
    $hasil["count"] = count($rows);
    return $hasil;
}

function checkMyRight($uid, $rn) {
    //function data_lookup($table, $keyfld, $keyval, $lkpfld, $dbx=0){
    if (!CHECK_RIGHT) return true;
    $rid = data_lookup("CMSRight","Name",$rn,"ID",1);
    if ($rid == null) return false;
    $acc = data_mlookup("CMSUser_Right", "Access", array(
        array("User_ID","=",$uid),
        array("Right_ID","=",$rid)
    ), 1);
    //echo "rid={$rid}; acc={$acc}";
    if ($acc == null) return false;
    return ($acc == 1);
}

function log_add($uid, $act, $table="", $tid=0, $before="", $after="") {
    global $CLIENT_IP;
    $data = array();
    try {
        $data["IPAddress"] = $CLIENT_IP; //$_SERVER["REMOTE_ADDR"]??"";
        $data["UserID"] = $uid;
        $data["Action"] = $act;
        $data["TableName"] = $table;
        $data["TableID"] = $tid;
        $data["DataBefore"] = $before;
        $data["DataAfter"] = $after;
        $data["LastUpdate"] = date('Y-m-d H:i:s');
        //data_create(withSchema("CMS_Log"),"ID",$data);
    } catch (Exception $e) {
        // do nothing
    }
}

function log_ui($act, $table="", $before="", $after="") {
    $usr = getVars("user-data");
    $data = array();
    $data["action"] = $act;
    $data["table"] = $table;
    $data["before"] = $before;
    $data["after"] = $after;
    $data["ip_addr"] = $_SERVER["HTTP_X_FORWARDED_FOR"] ?? $_SERVER["X_FORWARDED_FOR"] ?? $_SERVER["REMOTE_ADDR"];

    $log = array();
    $log["log_type"] = "INFO";
    $log["app_type"] = "ADM";
    $log["app_id"] = "UI-".$usr["uid"];
    $log["inserted_at"] = date("Y-m-d H:i:s");
    $log["data"] = json_encode($data);

    data_create(withSchema("logging"), "id", $log);
}

function log_uilogin($id,$uid,$ip1,$ip2,$ip3,$msg,$fail=true,$dbx=2) {
    $hasil = array();
    if ($fail) {
        $hasil[] = "FAILED";
        $sql = "insert into wsc_uilogin (user_id,user_uid,ip1,ip2,ip3,msg,lastUpdate)
                values(?,?,?,?,?,?,?)";
        $prm = array($id,$uid,$ip1,$ip2,$ip3,$msg,date('Y-m-d H:i:s'));
        try { DBX($dbx)->run($sql,$prm); } catch (Exception $e) { 
            $hasil[] = array(
                "sql" => $sql,
                "val" => $prm,
                "err" => $e->getMessage()
            ); 
        }

        $sql = "select user_id,user_uid,ip1,ip2,ip3,msg,lastUpdate 
                from wsc_uilogin 
                where user_id=? and lastUpdate >= ?";
        $dt = new DateTime();
        $dt->modify('-5 minutes');
        $sdt = $dt->format('Y-m-d H:i:s');
        $prm = array($id, $sdt);
        $lst = null;
        try { $lst = DBX($dbx)->run($sql,$prm)->fetchAll(); } catch (Exception $e) { 
            $hasil[] = array(
                "sql" => $sql,
                "val" => $prm,
                "err" => $e->getMessage()
            ); 
        }
        $hasil[] = $lst;
        if ($lst != null) {
            if (count($lst) >= 5) {
                DBX($dbx)->run("delete from wsc_uilogin where user_id=?", array($id));
                $sql = "insert into public.logging (log_type,app_type,app_id,data,inserted_at) values (?,?,?,?,LOCALTIMESTAMP)";
                $prm = array('ERR','ADM','UI-LOGIN',json_encode($lst));
                try { DBX(1)->run($sql,$prm); } catch (Exception $e) { 
                    $hasil[] = array(
                        "sql" => $sql,
                        "val" => $prm,
                        "err" => $e->getMessage()
                    ); 
                }
                // send mail
                if ($id != 0) {
                    $du = data_read(withSchema("user"),"id",$id);
                    $mailfrom = strtolower(data_lookup(withSchema("reference"),"str_key","SYSTEM-MAIL","str_val")??"");
                    $email = $du["email"] ?? "";
                    $name = $du["user_name"] ?? "";
                    if ($du != null && $email != "" && $mailfrom != "") {
                        require 'vendor/autoload.php'; // Adjust path if needed

                        $mail = new PHPMailer(true);
                        try {
                            //Server settings
                            $mail->isSMTP();                                            // Send using SMTP
                            $mail->Host       = 'mail.smtp2go.com';                     // Set the SMTP server to send through
                            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                            $mail->Username   = 'test-sppa-dev';                     // SMTP username
                            $mail->Password   = 'Asht123$';                        // SMTP password
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable implicit TLS encryption
                            $mail->Port       = 2525;                                    // TCP port to connect to; use 587 if you added SMTPSecure above

                            //Recipients
                            $mail->setFrom($mailfrom, 'SPPA FET - DEV');
                            $mail->addAddress($email, $name);     // Add a recipient

                            //Content
                            $t = "<table><tr><th>UserID</th><th>IP 1</th><th>IP 2</th><th>IP 3</th><th>Message</th><th>Logged At</th></tr>";
                            foreach ($lst as $row) {
                                $t .= "<tr><td>{$row['user_uid']}</td><td>{$row['ip1']}</td><td>{$row['ip2']}</td><td>{$row['ip3']}</td><td>{$row['msg']}</td><td>{$row['lastUpdate']}</td></tr>";
                            }
                            $t .= "</table>";
                            $mail->isHTML(true);                                  // Set email format to HTML
                            $mail->Subject = 'SPPA FET - Login Failed';
                            $mail->Body    = '<b>Dear '.$name.'.</b><br><br>This is an automated email to '.
                                'warn you that there are failed login attempts using your UserID.<br><br>'.
                                $t.
                                '<br><br>'.
                                '';
                            $mail->AltBody = 'Dear '.$name.'. This is an automated email to warn you that '.
                                'there are failed login attempts using your UserID.';

                            $mail->send();
                            //echo 'Message has been sent';
                        } catch (Exception $e) {
                            //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                            $hasil[] = array(
                                "when" => "Sending email",
                                "err" => $mail->ErrorInfo,
                                "from" => $mailfrom
                            );
                        }
                    }
                }
            }
        }
    } else {
        $hasil[] = "SUCCESS";
        // login success
        $sql = "delete from wsc_uilogin where user_id=?";
        $prm = array($id);
        try { DBX($dbx)->run($sql,$prm); } catch (Exception $e) { 
            $hasil[] = array(
                "cmd" => "LOGIN SUCCESS",
                "sql" => $sql,
                "val" => $prm,
                "err" => $e->getMessage()
            ); 
        }
    }
    return $hasil;
}

function initRoot() {
    $data = array();
    $data["UserID"] = "ROOT";
    $data["Pwd"] = defHash("root1234");
    $data["UserName"] = "SUPER USER";
    $data["AsRoot"] = 1;
    $data["LastUpdate"] = date('Y-m-d H:i:s');
    $data["LastUserID"] = 1;
    data_create(withSchema("CMS_User"),"ID",$data);
}

function initSector() {
    $sector = array(
        array("Code"=>"A", "SectorName"=>"ENERGY", "UserID"=>1, "rows"=>array(
            array("Code"=>"A11", "SubSectorName"=>'OIL & GAS', "UserID"=>1),
            array("Code"=>"A12", "SubSectorName"=>"COAL", "UserID"=>1),
            array("Code"=>"A13", "SubSectorName"=>'OIL, GAS & COAL SUPPORTS', "UserID"=>1),
            array("Code"=>"A21", "SubSectorName"=>"ALTERNATIVE ENERGY EQUIPMENT", "UserID"=>1),
            array("Code"=>"A22", "SubSectorName"=>"ALTERNATIVE FUELS", "UserID"=>1)
        )),
        array("Code"=>"B", "SectorName"=>"BASIC MATERIALS", "UserID"=>1, "rows"=>array(
            array("Code"=>"B11", "SubSectorName"=>"CHEMICALS", "UserID"=>1),
            array("Code"=>"B12", "SubSectorName"=>"CONSTRUCTION MATERIALS", "UserID"=>1),
            array("Code"=>"B13", "SubSectorName"=>'CONTAINERS & PACKAGING', "UserID"=>1),
            array("Code"=>"B14", "SubSectorName"=>'METALS & MINERALS', "UserID"=>1),
            array("Code"=>"B15", "SubSectorName"=>"FORESTRY & PAPER", "UserID"=>1)
        )),
        array("Code"=>"C", "SectorName"=>"INDUSTRIALS", "UserID"=>1, "rows"=>array(
            array("Code"=>"C11", "SubSectorName"=>'AEROSPACE & DEFENSE', "UserID"=>1),
            array("Code"=>"C12", "SubSectorName"=>'BUILDING PRODUCTS & FIXTURES', "UserID"=>1),
            array("Code"=>"C13", "SubSectorName"=>"ELECTRICAL", "UserID"=>1),
            array("Code"=>"C14", "SubSectorName"=>"MACHINERY", "UserID"=>1),
            array("Code"=>"C21", "SubSectorName"=>"DIVERSIFIED INDUSTRIAL TRADING", "UserID"=>1),
            array("Code"=>"C22", "SubSectorName"=>"COMMERCIAL SERVICES", "UserID"=>1),
            array("Code"=>"C23", "SubSectorName"=>"PROFESSIONAL SERVICES", "UserID"=>1),
            array("Code"=>"C31", "SubSectorName"=>"MULTI-SECTOR HOLDINGS", "UserID"=>1)
        )),
        array("Code"=>"D", "SectorName"=>"CONSUMER NON-CYCLICALS", "UserID"=>1, "rows"=>array(
            array("Code"=>"D11", "SubSectorName"=>'FOOD & STAPLES RETAILING', "UserID"=>1),
            array("Code"=>"D21", "SubSectorName"=>"BEVERAGES", "UserID"=>1),
            array("Code"=>"D22", "SubSectorName"=>"PROCESSED FOODS", "UserID"=>1),
            array("Code"=>"D23", "SubSectorName"=>"AGRICULTURAL PRODUCTS", "UserID"=>1),
            array("Code"=>"D31", "SubSectorName"=>"TOBACCO", "UserID"=>1),
            array("Code"=>"D41", "SubSectorName"=>"HOUSEHOLD PRODUCTS", "UserID"=>1),
            array("Code"=>"D42", "SubSectorName"=>"PERSONAL CARE PRODUCTS", "UserID"=>1)
        )),
        array("Code"=>"E", "SectorName"=>"CONSUMER CYCLICALS", "UserID"=>1, "rows"=>array(
            array("Code"=>"E11", "SubSectorName"=>"AUTO COMPONENTS", "UserID"=>1),
            array("Code"=>"E12", "SubSectorName"=>"AUTOMOBILES", "UserID"=>1),
            array("Code"=>"E21", "SubSectorName"=>"HOUSEHOLD GOODS", "UserID"=>1),
            array("Code"=>"E31", "SubSectorName"=>"CONSUMER ELECTRONICS", "UserID"=>1),
            array("Code"=>"E32", "SubSectorName"=>'SPORT EQUIPMENT & HOBBIES GOODS', "UserID"=>1),
            array("Code"=>"E41", "SubSectorName"=>'APPAREL & LUXURY GOODS', "UserID"=>1),
            array("Code"=>"E51", "SubSectorName"=>'TOURISM & RECREATION', "UserID"=>1),
            array("Code"=>"E52", "SubSectorName"=>'EDUCATION & SUPPORT SERVICES', "UserID"=>1),
            array("Code"=>"E61", "SubSectorName"=>"MEDIA", "UserID"=>1),
            array("Code"=>"E62", "SubSectorName"=>'ENTERTAINMENT & MOVIE PRODUCTION', "UserID"=>1),
            array("Code"=>"E71", "SubSectorName"=>"CONSUMER DISTRIBUTORS", "UserID"=>1),
            array("Code"=>"E72", "SubSectorName"=>'INTERNET & HOMESHOP RETAIL', "UserID"=>1),
            array("Code"=>"E73", "SubSectorName"=>"DEPARTMENT STORES", "UserID"=>1),
            array("Code"=>"E74", "SubSectorName"=>"SPECIALTY RETAIL", "UserID"=>1)
        )),
        array("Code"=>"F", "SectorName"=>"HEALTHCARE", "UserID"=>1, "rows"=>array(
            array("Code"=>"F11", "SubSectorName"=>'HEALTHCARE EQUIPMENT & SUPPLIES', "UserID"=>1),
            array("Code"=>"F12", "SubSectorName"=>"HEALTHCARE PROVIDERS", "UserID"=>1),
            array("Code"=>"F21", "SubSectorName"=>"PHARMACEUTICALS", "UserID"=>1),
            array("Code"=>"F22", "SubSectorName"=>"HEALTHCARE RESEARCH", "UserID"=>1)
        )),
        array("Code"=>"G", "SectorName"=>"FINANCIALS", "UserID"=>1, "rows"=>array(
            array("Code"=>"G11", "SubSectorName"=>"BANKS", "UserID"=>1),
            array("Code"=>"G21", "SubSectorName"=>"CONSUMER FINANCING", "UserID"=>1),
            array("Code"=>"G22", "SubSectorName"=>"BUSINESS FINANCING", "UserID"=>1),
            array("Code"=>"G31", "SubSectorName"=>"INVESTMENT SERVICES", "UserID"=>1),
            array("Code"=>"G41", "SubSectorName"=>"INSURANCE", "UserID"=>1),
            array("Code"=>"G51", "SubSectorName"=>'HOLDING & INVESTMENT COMPANIES', "UserID"=>1)
        )),
        array("Code"=>"H", "SectorName"=>"PROPERTY & REAL ESTATE", "UserID"=>1, "rows"=>array(
            array("Code"=>"H11", "SubSectorName"=>'REAL ESTATE MANAGEMENT & DEVELOPMENT', "UserID"=>1)
        )),
        array("Code"=>"I", "SectorName"=>"TECHNOLOGY", "UserID"=>1, "rows"=>array(
            array("Code"=>"I11", "SubSectorName"=>'ONLINE APPLICATIONS & SERVICES', "UserID"=>1),
            array("Code"=>"I12", "SubSectorName"=>'IT SERVICES & CONSULTING', "UserID"=>1),
            array("Code"=>"I13", "SubSectorName"=>"SOFTWARE", "UserID"=>1),
            array("Code"=>"I21", "SubSectorName"=>"NETWORKING EQUIPMENT", "UserID"=>1),
            array("Code"=>"I22", "SubSectorName"=>"COMPUTER HARDWARE", "UserID"=>1),
            array("Code"=>"I23", "SubSectorName"=>'ELECTRONIC EQUIPMENT, INSTRUMENTS & COMPONENTS', "UserID"=>1)
        )),
        array("Code"=>"J", "SectorName"=>"INFRASTRUCTURES", "UserID"=>1, "rows"=>array(
            array("Code"=>"J11", "SubSectorName"=>"TRANSPORT INFRASTRUCTURE OPERATOR", "UserID"=>1),
            array("Code"=>"J21", "SubSectorName"=>'HEAVY CONSTRUCTIONS & CIVIL ENGINEERING', "UserID"=>1),
            array("Code"=>"J31", "SubSectorName"=>"TELECOMMUNICATION SERVICE", "UserID"=>1),
            array("Code"=>"J32", "SubSectorName"=>"WIRELESS TELECOMMUNICATION SERVICES", "UserID"=>1),
            array("Code"=>"J41", "SubSectorName"=>"ELECTRIC UTILITIES", "UserID"=>1),
            array("Code"=>"J42", "SubSectorName"=>"GAS UTILITIES", "UserID"=>1),
            array("Code"=>"J43", "SubSectorName"=>"WATER UTILITIES", "UserID"=>1)
        )),
        array("Code"=>"K", "SectorName"=>"TRANSPORTATION AND LOGISTIC", "UserID"=>1, "rows"=>array(
            array("Code"=>"K11", "SubSectorName"=>"AIRLINES", "UserID"=>1),
            array("Code"=>"K12", "SubSectorName"=>"PASSENGER MARINE TRANSPORTATION", "UserID"=>1),
            array("Code"=>"K13", "SubSectorName"=>"PASSENGER LAND TRANSPORTATION", "UserID"=>1),
            array("Code"=>"K21", "SubSectorName"=>'LOGISTICS & DELIVERIES', "UserID"=>1)
        )),
        array("Code"=>"Z", "SectorName"=>"LISTED INVESTMENT PRODUCT", "UserID"=>1, "rows"=>array(
            array("Code"=>"Z11", "SubSectorName"=>"INVESTMENT TRUSTS", "UserID"=>1),
            array("Code"=>"Z21", "SubSectorName"=>"BONDS", "UserID"=>1)
        ))
    );
    foreach ($sector as $data) {
        $row = array();
        $row["Code"] = $data["Code"];
        $row["SectorName"] = $data["SectorName"];
        $row["UserID"] = $data["UserID"];
        $row["LastUpdate"] = date('Y-m-d H:i:s');
        $rec = data_create("CMS_Sector","ID",$row);
        $SID = $rec["ID"];
        foreach ($data["rows"] as $sub) {
            $row = array();
            $row["Sector_ID"] = $SID;
            $row["Code"] = $sub["Code"];
            $row["SubSectorName"] = $sub["SubSectorName"];
            $row["UserID"] = $sub["UserID"];
            $row["LastUpdate"] = date('Y-m-d H:i:s');
            $ss = data_create("CMS_Subsector","ID",$row);
        }
    }
}
