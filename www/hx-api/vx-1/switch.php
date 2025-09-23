<?php
function api_fn($hasil, $parm, $json) {
    if (!cekLevel(LEVEL_ADMIN)) done($hasil, 26);
    global $JPOST;

    if (count($parm)<1) {
        done($hasil, 999, "Invalid parameters");
    }

    $usr = getVars("user-data");
    $sql = "";
    $dbx = DB_DATA;

    $svr = $parm[0];
    $nex = $svr == "MAIN" ? "DRC" : "MAIN";
    $hasil->debug = array();

    $res = null;
    $jp = new stdClass();
    $jp->FetFixSwitch = array(
        "fromServer" => $svr,
        "toServer" => $nex
    );
    $cdt = new stdClass();
    $cdt->currentServer = $nex;

    try {
        // update config table;
        $sql = "update public.config set data=? where participant_id=?";
        $prms = array(json_encode($cdt), 'FIX');
        DBX($dbx)->run($sql,$prms);
        // send json to admin server;
        $res = postJson("http://sppafet-admin-net/sppa-fet/admin/switch",json_encode($jp));
        // fill response;
        $hasil->connectTo = $nex;
        $hasil->debug[] = "sppa-fet/admin/switch";
        $hasil->debug[] = $res;
        $hasil->debug[] = $jp;
        $hasil->debug[] = $cdt;
    } catch (Exception $e) {
        $hasil->debug[] = array("error"=>$e->getMessage(),"posturl"=>"/sppa-fet/admin/switch","data"=>$jp);
        done($hasil, 700, $e->getMessage());
    }

    return done($hasil);
}
