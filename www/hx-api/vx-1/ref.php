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
    if (!cekLevel(LEVEL_DEV)) done($hasil, 26);
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
    $dbx = DB_DATA;

    $table = $parm[0];
    $action= $parm[1];
    $prms  = array();
    $hasil->debug = array();

    switch ($table) {
        case 'ref':
            $doby = array(array("field"=>"name","dir"=>"asc"),array("field"=>"int_key","dir"=>"asc"),array("field"=>"str_key","dir"=>"asc"));
            switch ($action) {
                case 'list':
                    $sql = "SELECT * FROM public.reference";
                    break;
                case 'create':
                    $xr = false;
                    if ($json["name"] == "") $xr = true;
                    if ($json["str_val"] == "") $xr = true;
                    if ($json["int_key"] == "" && $json["str_key"] == "") $xr = true;
                    if ($xr) {
                        done($hasil, 600, "Empty required field");
                    }


                    if ($json["int_key"] != "") {
                        $cmd = "select count(id) ctr from public.reference where name=? and int_key=?";
                        $cmp = array($json["name"], $json["int_key"]);
                        $ctr = DBX($dbx)->run($cmd, $cmp)->fetchColumn();
                        if ($ctr > 0) done($hasil, 601, "Duplicate key for ".$json["name"]." : INT Key [".$json["int_key"]."]");
                    }
                    if ($json["str_key"] != "") {
                        $cmd = "select count(id) ctr from public.reference where name=? and str_key=?";
                        $cmp = array($json["name"], $json["int_key"]);
                        $ctr = DBX($dbx)->run($cmd, $cmp)->fetchColumn();
                        if ($ctr > 0) done($hasil, 601, "Duplicate key for ".$json["name"]." : STR Key [".$json["str_key"]."]");
                    }

                    $sql = "public.reference";
                    $json["inserted_at"] = date('Y-m-d H:i:s');
                    $json["updated_at"] = date('Y-m-d H:i:s');
                    if ($json["int_key"] == "") unset($json["int_key"]);
                    if ($json["str_key"] == "") unset($json["str_key"]);
                    break;
                case 'update':
                    $sql = "public.reference";
                    $json["updated_at"] = date('Y-m-d H:i:s');
                    if ($json["int_key"] == "") unset($json["int_key"]);
                    if ($json["str_key"] == "") unset($json["str_key"]);
                    break;
                case 'delete':
                    $sql = "public.reference";
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
                $lst = data_list_sql($sql, true, $page, $rpp, $oby, $whr, $dbx);
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

        case 'create':
            $obj = null;
            try {
                $obj = data_create($sql,"id",$json, false, false, $dbx);
            } catch (Exception $e) {
                $hasil->debug[] = array("error"=>$e->getMessage(),"sql"=>$sql,"param"=>$json);
                return done($hasil, 889, "Error adding data.");
            }

            log_add($usr["id"], "CREATE", $sql, $obj["id"], "", json_encode($obj));
            $hasil->data = $obj;
            break;

        case 'update':
            $old = data_read($sql,"id",$json["id"],$dbx);
            $new = null;
            try {
                $new = data_update($sql,"id",$json,$dbx);
            } catch (Exception $e) {
                $hasil->debug[] = array("error"=>$e->getMessage(),"sql"=>$sql,"data"=>$json);
                done($hasil, 889, "Error updating data.");
            }
        
            log_add($usr["id"], "UPDATE", $sql, $json["id"], json_encode($old), json_encode($new));
            $hasil->data = $new;
           break;

        case 'delete':
            $obj = null;
            try {
                $obj = data_delete($sql,"id",$json["id"],$dbx);
            } catch (Exception $e) {
                $hasil->debug[] = array("error"=>$e->getMessage(),"sql"=>$sql,"data"=>$json);
                done($hasil, 889, "Error deleting data.");
            }
            log_add($usr["id"], "DELETE", $sql, $json["id"], json_encode($obj), "");
            $hasil->data = $obj;
            break;

        case 'execsql':
            try {
                DBX($dbx)::run($sql,$prms);
            } catch (Exception $e) {
                $hasil->debug[] = array("error"=>$e->getMessage(), "sql"=>$sql, "params"=>$prms);
                done($hasil, 889, "Error executing command.");
            }
            break;
        
    }

    return done($hasil);
}
