<?php
class XDSN {
    static function mssql($svr,$db) {
        return "sqlsrv:server={$svr}; Database={$db}; TrustServerCertificate=true;";
    }
    static function mysql($svr,$db) {
        return "mysql:host={$svr};dbname={$db}";
    }
    static function sqlite($db,$reltv=true) {
        if ($reltv) {
            return "sqlite:".__DIR__."/".$db;
        } else {
            return "sqlite:".$db;
        }
    }
    static function postgresql($svr,$db) {
        return "pgsql:host={$svr};dbname={$db}";
    }
}
class XKDB {
    private $con;
    
    public function __construct() {
        $this->con = new stdClass();
        $this->con->user = "";
        $this->con->password = "";
        $this->con->dsn = "";
    }
    /*
    public function getInstance() {
        if ($this->me == null) {
            $this->me = new XKDB();
        }
        return $this->me;
    }
    */

    public function AESCrypt($key, $plaintext) {
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv); 
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true); 

        // Encrypted string 
        return base64_encode($iv.$hmac.$ciphertext_raw);
    }
    
    public function AESDecrypt($key, $ciphertext) {
        $c = base64_decode($ciphertext); 
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC"); 
        $iv = substr($c, 0, $ivlen); 
        $hmac = substr($c, $ivlen, $sha2len=32); 
        $ciphertext_raw = substr($c, $ivlen+$sha2len); 
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv); 
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true); 

        if(hash_equals($hmac, $calcmac)){ //PHP 5.6+ Timing attack safe string comparison 
            return $original_plaintext; 
        }else{ 
            return false;
        }
    }
    
    public function SetCreds($uid,$pwd) {
        $this->con->user = $uid;
        $this->con->password = $pwd;
    }
    public function SetRawDSN($dsn) {
        $this->con->dsn = $dsn;
    }
    public function SetDSN($ngine,$db,$svr="") {
        $dsn = "";
        if (strtolower($ngine) == "mssql") $dsn = XDSN::mssql($svr,$db);
        if (strtolower($ngine) == "mysql") $dsn = XDSN::mysql($svr,$db);
        if (strtolower($ngine) == "sqlite") $dsn = XDSN::sqlite($db);
        if (strtolower($ngine) == "postgresql") $dsn = XDSN::postgresql($svr,$db);
        $this->con->dsn = $dsn;
    }
    public function GetUser() {
        return $this->con->user;
    }
    public function GetPassword() {
        return $this->con->password;
    }
    public function GetDSN() {
        return $this->con->dsn;
    }
    
    public function Store($path,$phpMode=true) {
        try {
            $key = bin2hex(openssl_random_pseudo_bytes(16));
            $teks = json_encode($this->con);
            $out = $this->AESCrypt($key, $teks);
            $junk = bin2hex(openssl_random_pseudo_bytes(32+16));
            $fout = ($phpMode ? "<?php //" : "").$key.$junk.$out;
            file_put_contents($path, $fout);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public function LoadFromFile($path,$phpMode=true) {
        $tin = file_get_contents($path);
        if ($phpMode) $tin = substr($tin, 8);
        $key = substr($tin, 0, 32);
        $ciphertext = substr($tin, 128); 
        $json = $this->AESDecrypt($key, $ciphertext);
        if ($json != false) {
            $obj = json_decode($json);
            $this->SetCreds($obj->user, $obj->password);
            $this->SetRawDSN($obj->dsn);
            return true;
        }
        return false;
    }
}
class Hpdo extends PDO
{
    private $xkdb;
    public $engine = ""; // MSSQL || POSTGRES || MYSQL || SQLITE
    //public function __construct($dsn, $username = NULL, $password = NULL, $options = [])
    public function __construct($xfiles, $engine="MSSQL", $options = [])
    {
        $default_options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_ORACLE_NULLS       => PDO::NULL_TO_STRING
        ];
        $options = array_replace($default_options, $options);
        $uid=""; $pwd=""; $dsn="";
        if (is_array($xfiles)) {
            $uid=$xfiles[0]; 
            $pwd=$xfiles[1]; 
            $dsn=$xfiles[2];
        } else {
            $this->xkdb = new XKDB();
            $this->xkdb->LoadFromFile($xfiles);
            $uid = $this->xkdb->GetUser();
            $pwd = $this->xkdb->GetPassword();
            $dsn = $this->xkdb->GetDSN();
        }
        $this->engine = strtoupper($engine);
        parent::__construct($dsn, $uid, $pwd, $options);
    }
    public function __call($method, $args)
    {
        return call_user_func_array(array($this, $method), $args);
    }
    public function run($sql, $args = [])
    {
        if (!$args)
        {
            return $this->query($sql);
        }
        $stmt = $this->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
}

/*
class DB
{
    // https://phpdelusions.net/pdo/pdo_wrapper
    protected static $instance = null;
    protected static $xkdb = null;
    protected function __construct() {}
    protected function __clone() {}

    //public static $schema = "CMS";
    public static $engine = "SQLITE"; // MSSQL || POSTGRES || MYSQL || SQLITE

    public static function getClass() {
        return __CLASS__;
    }

    public static function instance()
    {
        if (self::$instance === null)
        {
            self::$xkdb = new XKDB();
            self::$xkdb->LoadFromFile(__DIR__."/datadb.con");
            $opt  = array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => FALSE,
                PDO::ATTR_ORACLE_NULLS       => PDO::NULL_TO_STRING
            );
            //$dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHAR;
            //self::$instance = new PDO($dsn, DB_USER, DB_PASS, $opt);

            $uid = self::$xkdb->GetUser();
            $pwd = self::$xkdb->GetPassword();
            $dsn = self::$xkdb->GetDSN();
            self::$instance = new PDO($dsn, $uid, $pwd, $opt);
            //$dsn = "sqlite:".__DIR__."/../data.db";
            //self::$instance = new PDO($dsn, "", "", $opt);
            

            //$dsn = "sqlsrv:server=localhost ; Database=CMS";

            //$dsn = "sqlsrv:server=192.168.94.10 ; Database=S21_RT_PLUS_IDXMOBILE";
            //self::$instance = new PDO($dsn, "s21rt", "diehards21rt", $opt);

        }
        return self::$instance;
    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(self::instance(), $method), $args);
    }

    public static function run($sql, $args = [])
    {
        if (!$args)
        {
            return self::instance()->query($sql);
        }
        $stmt = self::instance()->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
}

class DBR
{
    protected static $instance = null;
    protected static $xkdb = null;
    protected function __construct() {}
    protected function __clone() {}

    //public static $schema = "CMS";
    public static $engine = "SQLITE"; // MSSQL || POSTGRES || MYSQL || SQLITE

    public static function getClass() {
        return __CLASS__;
    }

    public static function instance()
    {
        if (self::$instance === null)
        {
            self::$xkdb = new XKDB();
            self::$xkdb->LoadFromFile(__DIR__."/rightdb.con");
            $opt  = array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => FALSE,
                PDO::ATTR_ORACLE_NULLS       => PDO::NULL_TO_STRING
            );
            $uid = self::$xkdb->GetUser();
            $pwd = self::$xkdb->GetPassword();
            $dsn = self::$xkdb->GetDSN();
            self::$instance = new PDO($dsn, $uid, $pwd, $opt);
            //$dsn = "sqlite:".__DIR__."/../rights.db";
            //self::$instance = new PDO($dsn, "", "", $opt);
        }
        return self::$instance;
    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(self::instance(), $method), $args);
    }

    public static function run($sql, $args = [])
    {
        if (!$args)
        {
            return self::instance()->query($sql);
        }
        $stmt = self::instance()->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
}
*/

function DBX($idx) {
    static $slfile = null;
    if ($slfile == null) $slfile = getenv('DB_MEM_DSN') ?? "sqlite:/dev/shm/sppa_fet_log.db";

    static $dbusr = null;
    static $dbpas = null;
    static $dbdsn = null;
    
    if ($dbusr == null) $dbusr = getenv('DB_TRX_USR') ?? "sppa";
    if ($dbpas == null) $dbpas = getenv('DB_TRX_PAS') ?? "bjfgua5M5gkUDZxjXxkIOMYZ4";
    if ($dbdsn == null) $dbdsn = getenv('DB_TRX_DSN') ?? "pgsql:host=10.102.0.43;port=5432;dbname=sppa_fet;sslmode=disable";
    /*
    if ($dbusr == null) $dbusr = "postgres";
    if ($dbpas == null) $dbpas = "postgres";
    if ($dbip == null) $dbip = "192.168.0.13";
    */
    
    if ($hdb == null) $hdb = new Hpdo(array($dbusr,$dbpas,$dbdsn),"POSTGRESQL");
    if ($hdbr == null) $hdbr = new Hpdo(array($dbusr,$dbpas,$dbdsn),"POSTGRESQL");
    if ($hdbl == null) $hdbl = new Hpdo(array("","",XDSN::sqlite($slfile,false)),"SQLITE");
    
    switch ($idx) {
        case 1:
            return $hdbr;
        case 2:
            return $hdbl;
        default:
            return $hdb;
    }
}
