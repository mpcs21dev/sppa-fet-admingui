<?php
/*
    API domain-add
    $arr = array(api_ver, fn_name, fn_param)
*/
function api_fn($hasil, $parm, $json) {
    $usr = getVars("user-data");
    
    $json["LastUpdate"] = date('Y-m-d H:i:s');
    $json["UserID"] = $usr["ID"];

    $obj = null;
    try {
        $obj = data_create(withSchema("CMS_Domain"),"ID",$json);
    } catch (Exception $e) {
        return done($hasil, 101, $e->getMessage());
    }
    log_add($usr["ID"], "CREATE", withSchema("CMS_Domain"), $obj["ID"], "", json_encode($obj));
    $hasil->data = $obj;

    return done($hasil);
}
