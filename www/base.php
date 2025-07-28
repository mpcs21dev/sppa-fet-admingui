<?php
date_default_timezone_set('Asia/Jakarta');
$LOGGED = "^___^";
$DEVELOPMENT = true; 

session_start();

$_SESSION["logged"] = isset($_SESSION["logged"]) ? $_SESSION["logged"] : "";
$ISLOGGED = $_SESSION["logged"] == $LOGGED;

$PATH = "";
$PARAM = array();

$P = isset($_GET["p"]) ? $_GET["p"] : "home";
$Ar = explode("/",$P);
if (count($Ar)>0) {
    $PATH = $Ar[0];
    $PARAM = $Ar;
    array_splice($PARAM,0,1);
    $JS_PARAM = "";
    foreach ($PARAM as $fp) {
        if (strlen($JS_PARAM)>0) $JS_PARAM .= ",";
        $JS_PARAM .= "\"" . $fp . "\"";
    }
}

$_SESSION["vars"] = isset($_SESSION["vars"]) ? $_SESSION["vars"] : array();

function getVars($key,$def=null){
    return isset($_SESSION["vars"][$key]) ? $_SESSION["vars"][$key] : $def;
}
function setVars($key,$val){
    $_SESSION["vars"][$key] = $val;
}
function clearVars(){
    $_SESSION = array();
}

function varLookup($data,$fieldkey,$keyval,$fieldres) {
    $res = null;
    foreach ($data as $row) {
        if ($row[$fieldkey] == $keyval) {
            $res = $row[$fieldres];
            break;
        }
    }
    return $res;
}
function rowLookup($data,$fieldkey,$keyval) {
    $res = null;
    foreach ($data as $row) {
        if ($row[$fieldkey] == $keyval) {
            $res = $row;
            break;
        }
    }
    return $res;
}

function cekRight($assetName,$dataAsset,$dataRight) {
    if (!CHECK_RIGHT) return true;
    
    $arow = rowLookup($dataAsset,"Name",strtoupper($assetName));
    if ($arow["Right_ID"] < 1) return true;

    $accr = rowLookup($dataRight,"Right_ID",$arow["Right_ID"]);
    if ($accr["Access"] == 1) return true;

    return false;
}