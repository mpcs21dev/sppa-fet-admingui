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

    $lmt = $rpp;
    $ofs = ($page-1) * $rpp;

    $sql = "select * from ".withSchema("V_Domain")." where DomainKey_ID=?";
    $sqlc = "select count(*) ctr from ({$sql}) t1";
    $sql .= " order by ID offset {$ofs} rows fetch first {$lmt} rows only";

    $arrWhere = array($parm[0]);

    $count = DBX(DB_DATA)->run($sqlc, $arrWhere)->fetchColumn();
    $pages = intval(floor($count / $rpp));
    if ($count % $rpp > 0) $pages += 1;

    $rows = DBX(DB_DATA)->run($sql, $arrWhere)->fetchAll();

    $hasil->data = $rows;
    $hasil->last_page = $pages;
    $hasil->last_row = $count;
    $hasil->count = count($rows);
    $hasil->rpp = $rpp;
    $hasil->page = $page;

    return done($hasil);
}
