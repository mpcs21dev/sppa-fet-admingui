<?php
/* 
    API isLogged
    Check if current sesstion Logged in
*/
function api_fn($hasil, $ar = array(), $json = null) {
    global $ISLOGGED;
    $hasil->logged = $ISLOGGED;
    done($hasil);
}
