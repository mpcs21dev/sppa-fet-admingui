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
    $dbx = 2; // DB_LOG

    $table = $parm[0];
    $action= $parm[1];
    $last_id= $parm[2] ?? "";

    $prms  = array();
    $hasil->debug = array();

    switch ($table) {
        case 'event':
            $doby = array(array("field"=>"id","dir"=>"desc"));
            switch ($action) {
                case 'latest':
                    $sql = "SELECT *, case app_type when 'FIX' then app_id else '' end app_idx FROM logging";
                    $df = $parm[2] ?? "";
                    $dt = $parm[3] ?? "";
                    if ($df != "" && $dt == "") {
                        $df = str_replace("T"," ",$df);
                        $sql .= " where inserted_at >= '{$df}'";
                    } else if ($df != "" && $dt != "") {
                        $df = str_replace("T"," ",$df);
                        $dt = str_replace("T"," ",$dt);
                        $sql .= " where inserted_at between '{$df}' and '{$dt}'";
                    }
                    $dbx = 0;
                    break;
                default:
                    $sql = "";
                    break;
            }
            break;

        case 'wsc':
            $doby = array(array("field"=>"id","dir"=>"asc"));
            switch ($action) {
                case 'latest':
                    $sql = "SELECT * FROM wsc_log where id>".$last_id;
                    break;
                default:
                    $sql = "";
                    break;
            }
            break;

        case 'metric':
            $doby = array(array("field"=>"id","dir"=>"asc"));
            switch ($action) {
                case 'latest':
                    $sql = "SELECT * FROM wsc_box";

                    break;
                default:
                    $sql = "";
                    break;
            }
            break;

        case 'stat':
            $doby = array(array("field"=>"id","dir"=>"asc"));
            switch ($action) {
                case 'latest':
                    $sql = "SELECT * FROM wsc_stat";

                    break;
                default:
                    $sql = "";
                    break;
            }
            break;
    
        case 'connection':
            $doby = array(array("field"=>"id","dir"=>"asc"));
            switch ($action) {
                case 'latest':
                    $sql = "SELECT * FROM wsc_login";

                    break;
                default:
                    $sql = "";
                    break;
            }
            break;

        case 'err':
            $doby = array(array("field"=>"id","dir"=>"desc"));
            switch ($action) {
                case 'latest':
                    $sql = "SELECT * FROM wsc_err";

                    break;
                default:
                    $sql = "";
                    break;
            }
            break;
    
        case 'log':
            $doby = array(array("field"=>"id","dir"=>"desc"));
            switch ($action) {
                case 'latest':
                    $sql = "SELECT * FROM wsc_log";

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
    $rpp = $drpp; //isset($JPOST["size"]) ? intval($JPOST["size"]) : $drpp;
    $oby = isset($JPOST["sort"]) ? $JPOST["sort"] : $doby;
    if (is_array($oby)) {
        if (count($oby) == 0) $oby = $doby;
    }
    $whr = isset($JPOST["filter"]) ? $JPOST["filter"] : $dwhr;
    if (is_array($whr)) {
        if (count($whr) == 0) $whr = $dwhr;
    }

    switch ($action) {
        case 'latest':
            $hasil->sql = $sql;
            $lst = null;
            try {
                //$lst = data_list_sql_nopage($sql, $oby, $whr, $dbx);
                $lst = data_list_sql($sql,true,$page,$rpp,$oby,$whr,$dbx);
                //$lst = DBX($dbx)->run($sql,array())->fetchAll();
            } catch (Exception $e) {
                $hasil->debug[] = array("error"=>$e->getMessage(), "sql"=>$sql,"data"=>$json,"where"=>$whr);
                done($hasil, 889, "Error listing data.");
            }
            $hasil->data = $lst["rows"];
            $hasil->last_page = $lst["pages"];
            $hasil->last_row = $lst["count"];
            $hasil->count = $lst["count"];
            $hasil->rpp = $rpp;
            $hasil->page = $page;
            $hasil->debug[] = array("list-debug"=>$lst["debug"],"data"=>$json);

            if ($table == "wsc") {
                if ($lst["count"] > 0) {
                    $lastId = $lst["rows"][count($lst["rows"])-1]["id"];
                    setVars("last-id", $lastId);
                }
            }
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

    $dfstr = shell_exec('df -h | grep /dev/shm');
    $dfarr = preg_split('/\s+/', $dfstr, -1, PREG_SPLIT_NO_EMPTY);
    $hasil->diskFree = $dfarr;
    return done($hasil);
}
