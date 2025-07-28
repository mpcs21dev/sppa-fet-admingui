<?php
/*
    API user-delete
    $arr = array(api_ver, fn_name, fn_param)
*/
function api_fn($hasil, $ar3, $json) {
    if (intval($json["id"]) == 1) return done($hasil, 16); // prevent deleting ROOT

    $usr = getVars("user-data");
    $obj = data_delete(withSchema("user"),"id",$json["id"]);
    log_add($usr["id"], "DELETE", withSchema("user"),$json["id"], json_encode($obj), "");
    $hasil->data = $obj;

    return done($hasil);
}
