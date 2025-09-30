<?php
/*
    API login
    user login
*/
function api_fn($hasil, $ar = array(), $json = null) {
    global $LOGGED;
    global $HX;

    $hasil->debug = array();

    //session_start();

    $ip1 = $_SERVER["REMOTE_ADDR"];
    $ip2 = $_SERVER["X_FORWARDED_FOR"]??"";
    $ip3 = $_SERVER["HTTP_X_FORWARDED_FOR"]??"";

    $ch = getChallange();
    $uid = $json["uid"];
    $pwd = $json["passwd"];

    $sql = "select * from ".withSchema("user")." where UPPER(uid)=UPPER(?)";
    $row = DBX(DB_DATA)->run($sql, array($uid))->fetchAll();
    if (count($row) == 0) { // invalid user id
        $hasil->debug[] = log_uilogin(0, $uid, $ip1, $ip2, $ip3, "UserID not found");
        log_add(0, "LOGIN-ATTEMPT",$uid);
        clearVars();
        $hasil->challange = getChallange();
        done($hasil, 11);
    }

    $usr = $row[0];
    $pin = $usr["passwd"];
    if ($pin == "") $pin = defHash("");
    $hashed = defHash($pin.getChallange());

    if (($hashed != $pwd) || ($usr["enabled"]!=1)) {  // invalid pin
        log_uilogin($usr["id"], $uid, $ip1, $ip2, $ip3, "Wrong password");
        log_add(0, "LOGIN-ATTEMPT",$uid);
        clearVars();
        $hasil->challange = getChallange();
        done($hasil, 11);
    }

    log_uilogin($usr["id"], $uid, $ip1, $ip2, $ip3, "Login Success", false);
    log_add($usr["id"], "LOGIN", $uid);
    /*
    if ($HX->reset($uid)) {
        $HX->save();
    }
    */

    $sql = "select * from CMSUser_Right where User_ID = ?";
    $rig = array(); //DBX(DB_RIGHT)->run($sql,array($usr["ID"]))->fetchAll();
    $sql = "select * from CMSAsset where Type='PAGE'";
    $ast = array(); //DBX(DB_RIGHT)->run($sql)->fetchAll();
    $sql = "select max(id) maxid from wsc_log";
    $lastId = DBX(DB_LOG)->run($sql,array())->fetchColumn();
    $usr["passwd"] = "";
    if ($usr["ulevel"] == "") $usr["ulevel"] = 1;
    setVars("user-data",$usr);
    setVars("user-right",$rig);
    setVars("asset",$ast);
    setVars("last-id", $lastId);
    $_SESSION["logged"] = $LOGGED;

    $hasil->data = $usr;
    $hasil->lastLog = $lastId;
    $hasil->challange = $ch;
    done($hasil);
}
