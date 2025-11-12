<?php
/*
    Call this api with :
        /api/?1/nama-fungsi/param1/param2...
*/
require_once("../base.php");

require_once("const.php");
require_once("db.php");
require_once("fn.php");
require_once("fn.db.php");

$hasil = new stdClass();
$hasil->message = "OK";
$hasil->error = 0;
$hasil->data = array();

//print $_SERVER['REQUEST_URI']."\n";
//$arr = explode("index.php", $_SERVER['REQUEST_URI']);
$arr = explode("?", $_SERVER['REQUEST_URI']);

$err = -1;
//if (count($arr)!=2) $err = 1;
if (count($arr)==2) {
    $a1 = $arr[1];
    if ($a1=="" || $a1=="/") $err = 2;
} else {
    $err = 1;
}

$ar2 = null;
if ($err < 0) {
    $ar2 = explode("/", $arr[1]);
    if (count($ar2) < 2) $err = 3;
    //if ($ar2[1] != "api") $err = 4;
}
//var_dump($ar2);
if ($err > 0) done($hasil, $err);

$apiver = $ar2[0];
$apifn = $ar2[1];
array_splice($ar2, 0, 2);
$ar3 = array();
foreach($ar2 as $a) {
    $s = strpos($a, "=");
    if ($s !== false) {
        // abcd=efghijkl ;; 4
        $key = substr($a,0,$s);
        $val = substr($a,1+$s);
        $ar3[$key] = $val;
    }
}
$arx = array_merge($ar2, $ar3);

if (CHECK_RIGHT) {
    $fns = "{$apiver}\\{$apifn}.php";
    $asset = data_read("CMSAsset","Filename",$fns,1);
    $auth = $asset["NeedAuth"]??false;
    $rightid = $asset["Right_ID"]??0;

    if (!$ISLOGGED && $auth) done($hasil, 10);

    if ($ISLOGGED) {
        $ur=null;
        if ($rightid != 0) {
            $usr = getVars("user-data");
            $ur = varLookup(getVars("user-right"),"Right_ID",$rightid,"Access");
            /*
            $ur = data_filter("CMSUser_Right",array(
                array("User_ID","=",$usr["ID"]),
                array("Right_ID","=",$rightid)
            ),1);
            */
        }
        $acc = $rightid == 0 ? 1 : $ur; //$ur["Access"];
        if ($acc != 1) done($hasil, 40);
    }
    
} else {
    if ((!$ISLOGGED && $apiver=="1" && !in_array($apifn, array("login","unexpire","getChallange","init-root","info","migrate")) ) ||
        (!$ISLOGGED && $apiver=="99" && !in_array($apifn, array("writedbcon")) ))
    {
        // ($apifn!="login" && $apifn!="getChallange" && $apifn!="init-root" && $apifn!="info")
        done($hasil, 10);
    }

    if (array_search($apiver, $VERSIONS) === false) done($hasil, 5);
    if (!fnRegistered($apiver, $apifn)) done($hasil, 6);
}

//$hasil->HXAwal = $HXAwal;
//$hasil->HXActive = $HX->toString();
if ($ISLOGGED) {
    if (!$HX->active) {
        clearVars();
        $hasil->message = "Session expired";
        $hasil->error = 888;
        $hasil->data = array();
        done($hasil, 888, "Session expired.");
    } else {
        $HX->tick();
    }
}


//$hasil->hx = json_encode($HX);

// Read JSON POST
$JPOST = json_decode(file_get_contents('php://input'), true); // change to $_POST from FormData object

$apifile = "../hx-api/vx-{$apiver}/{$apifn}.php";
if (file_exists($apifile)) {
    require_once($apifile);
    api_fn($hasil, $arx, $_POST);
} else {
    done($hasil, 7);
}
