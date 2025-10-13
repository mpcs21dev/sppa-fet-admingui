<?php
/*
    API Change Password    
*/
function api_fn($hasil, $ar = array(), $json = null) {
    global $JPOST;
    $usr = data_read(withSchema("user"),"id",$JPOST["id"]);
    $old = $usr;
    //$usr["passwd"] = data_lookup(withSchema("user"),"id",$usr["id"],"passwd");
    //$id = intval($usr["id"]);
    if ($usr["passwd"] == $JPOST["pwd0"]) {
        $dx = data_lookup(withSchema("reference"),"str_key","PASSWORD-EXPIRE","str_val");
        // update password;
        $usr["passwd"] = $JPOST["pwd1"];
        $usr["updated_at"] = date("Y-m-d H:i:s");
        $usr["updated_by"] = $JPOST["id"];
        $usr["chpwd"] = 0;
        unset($usr["inserted_at"]);
        unset($usr["inserted_by"]);
        $skrg = new DateTime('now');
        $skrg->add(new DateInterval("P{$dx}D"));
        $usr["passwd_expire"] = $skrg->format("Y-m-d")." 00:00:00";
        $new = data_update(withSchema("user"),"id",$usr);
        if (isset($new["error"])) {
            $hasil->error = $new["error"];
            $hasil->message = $new["message"];
        } else {
            log_add($usr["id"], "UNEXPIRE", withSchema("user"), $usr["id"], json_encode($old), json_encode($new));
        }
        $hasil->debug = $new;
        done($hasil);
    } else {
        $hasil->debug = array($JPOST,$usr);
        done($hasil, 12);
    }
}
