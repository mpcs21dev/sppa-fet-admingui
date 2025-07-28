<?php
/*
    API user-update
    $arr = array(api_ver, fn_name, fn_param)
*/
function api_fn($hasil, $ar3, $json) {
    if (intval($json["id"]) == 1) return done($hasil, 16);  // prevent changing user ROOT
    $usr = getVars("user-data");

    $arr = array();
    $arr["id"] = $json["id"];
    $arr["user_name"] = $json["user_name"];
    $arr["updated_at"] = date('Y-m-d H:i:s');
    $arr["updated_by"] = $usr["id"];
    $arr["enabled"] = $json["enabled"];
    if (intval($json["id"]) == intval($usr["id"])) $arr["enabled"] = 1; // no-self-void

    $old = data_read(withSchema("user"),"id",$json["id"]);
    $new = data_update(withSchema("user"),"id",$arr);

    log_add($usr["id"], "UPDATE", withSchema("user"), $json["id"], json_encode($old), json_encode($new));
    $hasil->data = $new;

    return done($hasil);
}
