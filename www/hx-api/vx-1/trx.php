<?php
/*
    API ipostatus-list
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

    if (count($parm)<2) {
        done($hasil, 999, "Invalid parameters");
    }

    $usr = getVars("user-data");
    $dpage = 1;
    $drpp = 50;
    $doby = array();
    $dwhr = array();

    $roid = 0;
    $xid = 0;

    $sql = "";
    $dbx = 1;

    $table = $parm[0];
    $action= $parm[1];
    $partid= $parm[2] ?? "ALL";
    $parent= $parm[3] ?? 0;

    $prms  = array();
    $hasil->debug = array();

    switch ($table) {
        case 'transaction':
            $doby = array(array("field"=>"id","dir"=>"asc"));
            switch ($action) {
                case 'list':
                    if (strtolower($partid) == "all") {
                        $uni = "";
                        $prts = DBX($dbx)->run("select participant_id partid, participant_name partname from public.config where record_type <> 'FTP'")->fetchAll();
                        foreach ($prts as $part) {
                            if (strlen($uni)>0) $uni .= "union ";
                            $uni .= "select '".$part["partid"]."' partid, '".$part["partname"]."' participant, * FROM public.transaction_".$part["partid"]." ";
                        }
                        $sql = "select a.*, b.str_val status_enum from (".$uni.") a left join public.reference b on a.status=b.int_key and b.name='RFO-STATUS'";
                    } else {
                        $partname = data_lookup("public.config","participant_id",strtoupper($partid),"participant_name");
                        $sql = "SELECT '".$partid."' partid, '".$partname."' participant, a.*, b.str_val status_enum FROM public.transaction_".$partid." a left join public.reference b on a.status=b.int_key and b.name='RFO-STATUS'";
                    }
                    break;
                default:
                    $sql = "";
                    break;
            }
            break;

        case 'message':
            $doby = array(array("field"=>"id","dir"=>"asc"));
            switch ($action) {
                case 'list':
                    $sql = "SELECT * FROM public.message_".$partid." where parent_id=".$parent;

                    break;
                default:
                    $sql = "";
                    break;
            }
            break;
        default:
            # code...
            break;
    }

    if ($sql == "") {
        done($hasil, 998, "Error parsing command");
    }

    $page = isset($JPOST["page"]) ? intval($JPOST["page"]) : $dpage;
    $rpp = isset($JPOST["size"]) ? intval($JPOST["size"]) : $drpp;
    $oby = isset($JPOST["sort"]) ? $JPOST["sort"] : $doby;
    if (is_array($oby)) {
        if (count($oby) == 0) $oby = $doby;
    }
    $whr = isset($JPOST["filter"]) ? $JPOST["filter"] : $dwhr;
    if (is_array($whr)) {
        if (count($whr) == 0) $whr = $dwhr;
    }

    switch ($action) {
        case 'list':
            $hasil->sql = $sql;
            $lst = null;
            try {
                if ($partid != "") {
                    $lst = data_list_sql($sql, true, $page, $rpp, $oby, $whr, $dbx);
                } else {
                    $lst = array(
                        "rows" => array(),
                        "pages" => 1,
                        "count" => 0,
                        "debug" => array()
                    );
                }
            } catch (Exception $e) {
                $hasil->debug[] = array("error"=>$e->getMessage(), "sql"=>$sql,"data"=>$json);
                done($hasil, 889, "Error listing data.");
            }
            $hasil->data = $lst["rows"];
            $hasil->last_page = $lst["pages"];
            $hasil->last_row = $lst["count"];
            $hasil->count = $lst["count"];
            $hasil->rpp = $rpp;
            $hasil->page = $page;
            $hasil->debug[] = array("list-debug"=>$lst["debug"],"data"=>$json);
            break;

        case 'execsql':
            try {
                DBX($dbx)->run($sql,$prms);
            } catch (Exception $e) {
                $hasil->debug[] = array("error"=>$e->getMessage(), "sql"=>$sql, "params"=>$prms);
                done($hasil, 889, "Error executing command.");
            }
            break;
        
    }

    return done($hasil);
}
