<?php
/*
    API user-add
    $arr = array(api_ver, fn_name, fn_param)
*/
function api_fn($hasil, $ar3, $json) {
    $usr = getVars("user-data");
    $json["inserted_at"] = date('Y-m-d H:i:s');
    $json["updated_at"] = date('Y-m-d H:i:s');
    $json["enabled"] = 1;
    $json["inserted_by"] = $usr["id"];
    $json["updated_by"] = $usr["id"];

    $obj = data_create(withSchema("user"),"id",$json);
    //log_add($usr["id"], "CREATE", withSchema("user"), $obj["id"], "", json_encode($obj));
    $hasil->data = $obj;

    return done($hasil);
}
