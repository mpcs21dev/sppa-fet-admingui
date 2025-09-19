<?php
require_once("base.php");

if (!$ISLOGGED) {
    if (strpos($_SERVER['REQUEST_URI'], "?p=login")) {
        // do nothing
    } else {
        if ($DEVELOPMENT) {
            header("Location: loader.php?p=login");
        } else {
            header("Location: loader.php?p=login");
        }
    }
}

if (!$HX->active) {
    //clearVars();
    //header("Location: loader.php?p=login");
}
