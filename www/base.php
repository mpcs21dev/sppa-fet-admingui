<?php
date_default_timezone_set('Asia/Jakarta');
$LOGGED = "^___^";
$DEVELOPMENT = true;

require_once(__DIR__."/api/fn.php");
require_once(__DIR__."/api/db.php");

class HXActive {
    private $user;
    private $tick;
    private $timeout = 60;
    public $active = false;
    public $debug = array();

    public function __construct($uid="") {
        $this->user = $uid;
        $this->tick = time();
        //$this->debug[] = basename($_SERVER['PHP_SELF'])." ## construct: ".$uid;
    }

    public function reset($uid="") {
        $this->user = $uid;
        $this->tick = time();
        $this->active = false;
        $hreg = $this->reg();
        //$this->debug[] = basename($_SERVER['PHP_SELF'])." ## reset; ".$uid."; reg=".$hreg;
        return $hreg;
    }

    public function save() {
        //$this->debug[] = basename($_SERVER['PHP_SELF'])." ## save()";
        $j = new stdClass();
        $j->user = $this->user;
        $j->tick = $this->tick;
        $j->timeout = $this->timeout;
        $j->active = $this->active;
        //$j->debug = $this->debug;
        $_SESSION["__ACTIVE"] = $j;
        return true;
    }

    public function load() {
        $j = $_SESSION["__ACTIVE"] ?? null;
        if ($j == null) {
            $this->active = false;
            //$this->debug[] = basename($_SERVER['PHP_SELF'])." ## load :: no save :: false";
            return false;
        }

        $this->user = $j->user;
        $this->tick = $j->tick;
        $this->timeout = $j->timeout;
        //$this->debug = array_merge(array(), $j->debug);

        $ctr = $this->cekdb();
        if ($ctr < 1) {
            $this->active = false;
            //$this->debug[] = basename($_SERVER['PHP_SELF'])." ## load :: cekdb :: false ;; ".$ctr;
            return false;
        }

        //$this->debug[] = basename($_SERVER['PHP_SELF'])." ## load :: success";
        $this->active = true;
        return true;
    }

    private function cekdb($del=false) {
        //if (!$this->active) return false;
        if ($del) {
            $sqd = "delete from wsc_session where unixepoch()-tick > ?";
            DBX(2)->run($sqd, array($this->timeout));
            //$this->debug[] = basename($_SERVER['PHP_SELF'])." [cekdb del] ".$sqd;
        }
        $sql = "select count(id) ctr from wsc_session where uid=? and unixepoch()-tick <= ?";
        $prm = array($this->user, $this->timeout);
        $ctr = DBX(2)->run($sql, $prm)->fetchColumn();
        //$this->debug[] = basename($_SERVER['PHP_SELF'])." [cekdb] ".$this->user." ; ".$ctr;
        return $ctr;
    }

    private function regdb() {
        $sql = "insert into wsc_session(uid, tick) values (?, unixepoch())";
        $prm = array($this->user);
        DBX(2)->run($sql, $prm);
        //$this->debug[] = basename($_SERVER['PHP_SELF'])." [regdb] ".$this->user;
        return true;
    }

    public function unregdb() {
        $sql = "delete from wsc_session where uid=?";
        $prm = array($this->user);
        DBX(2)->run($sql, $prm);
        //$this->debug[] = basename($_SERVER['PHP_SELF'])." [unregdb] ".$this->user;
        return true;
    }

    public function reg() {
        if ($this->user == "") return false;
        $ctr = $this->cekdb(true);
        if ($ctr > 0) return false;

        $this->active = true;
        //$this->debug[] = basename($_SERVER['PHP_SELF'])." [reg] ".$this->user;
        return $this->regdb();
    }

    private function tickdb() {
        $sql = "update wsc_session set tick=unixepoch() where uid = ?";
        $prm = array($this->user);
        DBX(2)->run($sql,$prm);
        //$this->debug[] = basename($_SERVER['PHP_SELF'])." [tickdb] ".$this->user;
        return true;
    }
    public function tick() {
        $ctr = $this->cekdb();
        if ($ctr == 0) {
            $this->active = false;
            //$this->debug[] = basename($_SERVER['PHP_SELF'])." [tick] cekdb ".ctr;
            return false;
        }

        $this->active = true;
        $this->tick = time();
        //$this->debug[] = basename($_SERVER['PHP_SELF'])." [tick] ".$this->user;
        return $this->tickdb();
    }
}

session_start();

$_SESSION["logged"] = isset($_SESSION["logged"]) ? $_SESSION["logged"] : "";
$ISLOGGED = $_SESSION["logged"] == $LOGGED;
$_SESSION["vars"] = isset($_SESSION["vars"]) ? $_SESSION["vars"] : array();
//session_write_close();
$HX = new HXActive();
if ($HX->load()) $HX->tick();

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

function cekLevel($min) {
    $usr = getVars("user-data");
    $lvl = intval($usr["ulevel"]);
    if ($lvl == 0) $lvl = 1;
    return $lvl >= $min;
}