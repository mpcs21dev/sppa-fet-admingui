<?php
/*
    API user-lst
    $arr = array(api_ver, fn_name, page, rpp, orderby, where)
    function data_list($table, $page=1, $rpp=10, $orderby="", $where="")

    TABULATOR:
    filter
    [
        {field:"age", type:">", value:52}, //filter by age greater than 52
        {field:"height", type:"<", value:142}, //and by height less than 142
    ]

    sort
    [
        {
            column:column,
            field:"age",
            dir:"asc"
        },
        {
            column:column,
            field:"height"
            dir:"desc"
        }
    ]
*/
function api_fn($hasil, $ar3, $json) {
    global $JPOST;
    
    $page = isset($JPOST["page"]) ? intval($JPOST["page"]) : 1;
    $rpp = 50; //isset($JPOST["size"]) ? intval($JPOST["size"]) : 50;
    $oby = isset($JPOST["sort"]) ? $JPOST["sort"] : array();
    if (count($oby) == 0) $oby = array(array("field"=>"uid","dir"=>"asc"));
    $whr = isset($JPOST["filter"]) ? $JPOST["filter"] : array();

    $lst = data_list(withSchema("user"), true, $page, $rpp, $oby, $whr);
    $hasil->data = $lst["rows"];
    $hasil->last_page = $lst["pages"];
    $hasil->last_row = $lst["count"];
    $hasil->count = $lst["count"];
    $hasil->rpp = $rpp;
    $hasil->page = $page;

    return done($hasil);
}
