<?php
/*
    API getChallange
    reset session
*/
function api_fn($hasil, $ar3, $json) {
    $ch = getChallange();
    $hasil->challange = $ch;
    done($hasil);
}
