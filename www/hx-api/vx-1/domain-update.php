<?php
/*
    API domain-update
    $arr = array(api_ver, fn_name, fn_param)
*/
function api_fn($hasil, $ar3, $json) {
    $usr = getVars("user-data");

    unset($json["UpdateBy"]);
    $json["LastUpdate"] = date('Y-m-d H:i:s');
    $json["UserID"] = $usr["ID"];

    $old = data_read("".withSchema("CMS_Domain")."","ID",$json["ID"]);
    $new = data_update("".withSchema("CMS_Domain")."","ID",$json);

    log_add($usr["ID"], "UPDATE", "".withSchema("CMS_Domain")."", $json["ID"], json_encode($old), json_encode($new));
    $hasil->data = $new;

    return done($hasil);
}
