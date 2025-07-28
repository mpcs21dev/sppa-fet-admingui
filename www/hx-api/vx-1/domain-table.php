<?php
/*
    API domain-list
    $arr = array(api_ver, fn_name, page, rpp, orderby, where)
    function data_list($table, $page=1, $rpp=10, $orderby="", $where="")
*/
function api_fn($hasil, $parm, $json) {
    global $JPOST;
    
    $page = isset($JPOST["page"]) ? intval($JPOST["page"]) : 1;
    $rpp  = isset($JPOST["size"]) ? intval($JPOST["size"]) : 10;
    $oby = isset($JPOST["sort"]) ? $JPOST["sort"] : array();
    if (count($oby) == 0) $oby = array(array("field"=>"Value","dir"=>"asc"));
    //$whr = isset($JPOST["filter"]) ? $JPOST["filter"] : array(); // ignore for now
    //$whr = array();

    $sql = "select * from ".withSchema("V_Domain")."";
    //$sqlc = "select count(*) ctr from ({$sql}) t1";
    //$sql .= " order by ID offset {$ofs} rows fetch first {$lmt} rows only";

    $whr = array(array("field"=>"upper(DomainName)","type"=>"eq","value"=> urldecode($parm[0])));

    $lst = data_list_sql_nopage($sql, $oby, $whr);

    $hasil->data = $lst;
    $hasil->last_page = 1;
    $hasil->last_row = count($lst);
    $hasil->count = $hasil->last_row;
    $hasil->rpp = 10;
    $hasil->page = 1;
    $hasil->debug = $whr;

    return done($hasil);
}
