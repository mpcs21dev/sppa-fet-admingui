<?php
/*
    API domainkey-delete
    $arr = array(api_ver, fn_name, fn_param)
*/
function api_fn($hasil, $ar3, $json) {
    $usr = getVars("user-data");
    $obj = data_delete("".withSchema("CMS_DomainKey")."","ID",$json["ID"]);
    log_add($usr["ID"], "DELETE", "".withSchema("CMS_DomainKey")."", $json["ID"], json_encode($obj), "");
    $hasil->data = $obj;

    return done($hasil);
}
