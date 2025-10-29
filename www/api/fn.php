<?php
use Nullix\CryptoJsAes\CryptoJsAes;
require_once("CryptoJsAes.php");

function done($ret, $err=0, $msg="") {
    global $ERR;
    $ret->error = $err;
    if ($err != 0) $ret->message = $msg=="" ? (isset($ERR[$err]) ? $ERR[$err] : "Unregistered Error") : $msg;
    $ret->tgl = date('Y-m-d H:i:s'); //date_format(date(), 'Y-m-d H:i:s');
    unset($ret->debug);
    unset($ret->sql);

    if (isset($ret->data)) {
        if ($ret->data != "") {
            $ch = isset($ret->challange) ? $ret->challange : getChallange();
            $px = $ch.$ret->tgl;
            //$de = encryptDx($ret->data,$px);
            $ret->HXDATA = CryptoJsAes::encrypt(json_encode($ret->data),$px);
            unset($ret->data);
        }
    }

    header('Content-Type: application/json');
    echo(json_encode($ret)."\n");
    die();
}

function fnRegistered($ver, $fn) {
    global $FUNCTIONS;
    $vteks = "v".$ver;
    if (array_search($fn, $FUNCTIONS->$vteks)===false) {
        return false;
    } else {
        return true;
    }
}

function squote($teks) {
    return "'{$teks}'";
}

function defHash($teks) {
    return hash("sha256", $teks);
}

function getChallange() {
    $uid = isset($_SESSION["challange"]) ? $_SESSION["challange"] : uniqid('cms_', true);
    $_SESSION["challange"] = $uid;
    return $uid;
}

function SFormat($tmp, $val = array()) {
    $hasil = $tmp;
    /*
    $x = count($val);
    for ($i=0; $i<$x; $i++) {
        $hasil = str_replace("{".$i."}", $val[$i], $hasil);
    }
    */
    foreach ($val as $i => $s) { $hasil = str_replace("{".$i."}", $s, $hasil); }
    return $hasil;
}

function ambilFile($fn, $b64=false) {
    $isi = file_get_contents($fn);
    if ($b64) {
        return base64_encode($isi);
    } else {
        return $isi;
    }
}

function json2CSV($data, $fn="") {
    $fp = fopen('php://temp/maxmemory:'. (15*1024*1024), 'r+'); //fopen($cfilename, 'w');
    $header = false;
    foreach ($data as $row) {
        if (empty($header)) {
            $header = array_keys($row);
            fputcsv($fp, $header);
            $header = array_flip($header);
        }
        fputcsv($fp, array_merge($header, $row));
    }
    rewind($fp);
    $output = stream_get_contents($fp);

    fclose($fp);
    //return $output;
    $usr = getVars("user-data")["Uid"];
    $skrg = date("Y-m-d_H.i.s");
    header('Content-Type: text/csv');
    header("Content-disposition: attachment; filename=\"".$fn."_".$usr."_".$skrg.".csv\""); 
    echo($output);
    die();
}

function url_get_content($URL){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_URL, $URL);
      $data = curl_exec($ch);
      curl_close($ch);
      return $data;
}

function postJson($url, $json) {
    // Initialize cURL
    $ch = curl_init($url);
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json)
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    
    // Execute the request and capture the response
    $response = curl_exec($ch);

    $hasil = array();
    // Check for errors
    if (curl_errno($ch)) {
        $hasil[] = false;
        $hasil[] = curl_error($ch);
    } else {
        $hasil[] = true;
        $hasil[] = $response;
    }
    
    // Close the cURL session
    curl_close($ch);
    
    return $hasil;
}

function fmtParagraph($teks) {
    $tar = explode(".", $teks);
    $ct = count($tar);
    for ($i=0; $i<$ct; $i++) {
        $tar[$i] = ucfirst(trim($tar[$i]));
    }
    return implode(" ",$tar);
}

function RandomAPIKey($x = 32) {
    return bin2hex(random_bytes($x));
}

function getDirContents($path) {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

    $files = array(); 
    foreach ($rii as $file)
        if (!$file->isDir()) {
            $files[] = array(
                $file->getPathname(),
                $file->getBasename(),
                $file->getExtension(),
                $file->getSize()
            ); 
        }

    return $files;
}

function checkMime($fn,$xtra=array()) {
    if (is_uploaded_file($fn)) {
        $mt = mime_content_type($fn);
        $mt_allow = array_merge(array('image/png', 'image/jpeg'),$xtra);
        if (! in_array($mt, $mt_allow)) {
            // File type is NOT allowed.
            return false;
        } else {
            return true;
        }
    } else {
        return false;
    }
}

