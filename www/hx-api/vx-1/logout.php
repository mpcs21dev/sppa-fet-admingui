<?php
/* 
    API Logout
    logout user
*/
function api_fn($hasil, $ar = array(), $json = null) {
    if (session_status() != PHP_SESSION_ACTIVE) session_start();
    log_ui("LOGOUT","SUCCESS");
    global $HX;
    $HX->unregdb();
    
    clearVars();
    done($hasil);
}
