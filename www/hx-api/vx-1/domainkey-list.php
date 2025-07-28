<?php
/*
    API domainkey-list
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
function api_fn($hasil, $parm, $json) {
    global $JPOST;

    $page = isset($JPOST["page"]) ? intval($JPOST["page"]) : 1;
    $rpp = isset($JPOST["size"]) ? intval($JPOST["size"]) : 10000;
    $oby = isset($JPOST["sort"]) ? $JPOST["sort"] : array();
    if (count($oby) == 0) $oby = array(array("field"=>"DomainName","dir"=>"asc"));
    
    //$whr = isset($JPOST["filter"]) ? $JPOST["filter"] : array(); // ignore for now
    $whr = array();

    $lst = data_list("".withSchema("V_DomainKey")."", true, $page, $rpp, $oby, $whr);
    $hasil->data = $lst["rows"];
    $hasil->last_page = $lst["pages"];
    $hasil->last_row = $lst["count"];
    $hasil->count = $lst["count"];
    $hasil->rpp = $rpp;
    $hasil->page = $page;
    $hasil->debug = $json;

    return done($hasil);
}
