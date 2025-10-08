<?php
/*
    API Change Password    
*/
function api_fn($hasil, $ar = array(), $json = null) {
    global $JPOST;
    $usr = getVars("user-data");
    $old = getVars("user-data");
    $usr["passwd"] = data_lookup(withSchema("user"),"id",$usr["id"],"passwd");
    //$id = intval($usr["id"]);
    if ($usr["passwd"] == $JPOST["pwd0"]) {
        // update password;
        $usr["passwd"] = $JPOST["pwd1"];
        $usr["updated_at"] = date("Y-m-d H:i:s");
        $usr["chpwd"] = 0;
        unset($usr["inserted_at"]);
        unset($usr["inserted_by"]);
        unset($usr["updated_by"]);
        $new = data_update(withSchema("user"),"id",$usr);
        if (isset($new["error"])) {
            $hasil->error = $new["error"];
            $hasil->message = $new["message"];
        } else {
            log_add($usr["id"], "PASSWD", withSchema("user"), $usr["id"], json_encode($old), json_encode($new));
            setVars("user-data",$new);
        }
        $hasil->debug = $new;
        done($hasil);
    } else {
        $hasil->debug = array($JPOST,$usr);
        done($hasil, 12);
    }
}
