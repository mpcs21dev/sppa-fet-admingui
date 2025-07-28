<?php
function api_fn($hasil, $ar = array(), $json = null) {
/*
    $u1 = data_read("CMS_User", "ID", 1);
    if ($u1 == null) {
        initRoot();
        //initSector();

        done($hasil);
    } else {
        done($hasil, 8);
    }
*/
    initRoot();
    done($hasil);
}
