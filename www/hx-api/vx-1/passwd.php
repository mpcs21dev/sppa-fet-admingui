<?php
/*
    API Change Password    
*/
function api_fn($hasil, $ar = array(), $json = null) {
    global $JPOST;
    $usr = getVars("user-data");
    $old = getVars("user-data");
    $id = $usr["id"];
    if ($usr["passwd"] == $JPOST["pwd0"]) {
        // update password;
        $usr["passwd"] = $JPOST["pwd1"];
        $new = data_update(withSchema("user"),"id",$usr);

        log_add($usr["id"], "PASSWD", withSchema("user"), $usr["id"], json_encode($old), json_encode($new));
        setVars("user-data",$usr);
        done($hasil);
    } else {
        done($hasil, 12);
    }
}
