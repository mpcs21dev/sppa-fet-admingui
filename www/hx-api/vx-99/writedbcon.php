<?php
function api_fn($hasil, $parm, $json) {
    global $JPOST;

    //$hasil->message = implode(",", $parm);
    
    $fnm = $parm[0] ?? "store.key";
    $eng = $parm[1] ?? "";
    $usr = $parm[2] ?? "";
    $pwd = $parm[3] ?? "";
    $dbn = $parm[4] ?? "";
    $svr = $parm[5] ?? "";

    $x = false;
    $btl = false;

    if ($eng=="") $btl = true;
    if ($eng!="sqlite") {
        if ($usr=="") $btl = true;
        if ($pwd=="") $btl = true;
    }
    if ($dbn=="") $btl = true;

    if ($btl) {
        $hasil->error = 998;
        $hasil->message = "Invalid parameters.";
    } else {
        $cdb = new XKDB();
        $cdb->SetCreds($usr,$pwd);
        $cdb->SetDSN($eng, $dbn, $svr);
    
        $x = $cdb->Store($fnm);
    }

    if (!$x) {
        $hasil->error = 999;
        $hasil->message = "Use: ?a=public.writedbcon/21/output-file-name/db-engine/db-user/db-pass/db-name[/db-server[:port]]";
    }

    return done($hasil, 0, "API: OK");
}
