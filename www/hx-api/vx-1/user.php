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

    $prms  = array();
    $hasil->debug = array();
    $old = null;
    $new = null;

    switch ($table) {
        case 'user':
            $doby = array(array("field"=>"uid","dir"=>"asc"));
            switch ($action) {
                case 'reset':
                    if (!cekLevel(LEVEL_ADMIN)) done($hasil, 26);
                    $sql = "public.user";
		            $old = data_read($sql,"id",$json["id"],$dbx);
                    if (intval($old["ulevel"]) == 99) return done($hasil, 16);  // prevent changing user ROOT
                    if (intval($json["id"]) == intval($usr["id"])) return done($hasil, 18); // prevent resetting self password; must use change password menu;
                    $prms["id"] = $json["id"];
                    $prms["passwd"] = $json["passwd"];
                    $prms["updated_at"] = date('Y-m-d H:i:s');
                    $prms["updated_by"] = $usr["id"];
                    $prms["chpwd"] = 1;
                    break;
                case 'level':
                    $sql = "select int_key xkey, str_val xval from public.reference where name='USER-LEVEL'";
                    break;
                case 'listall':
                case 'list':
                    $sql = "select a.*, b.str_val user_level from public.user a left join public.reference b on a.ulevel=b.int_key and b.name='USER-LEVEL'";
                    break;
                case 'create':
                    if (!cekLevel(LEVEL_ADMIN)) done($hasil, 26);
                    $sql = "public.user";
                    $json["inserted_at"] = date('Y-m-d H:i:s');
                    $json["updated_at"] = date('Y-m-d H:i:s');
                    $json["enabled"] = 1;
                    $json["inserted_by"] = $usr["id"];
                    $json["updated_by"] = $usr["id"];
                    unset($json["user_level"]);
                    break;
                case 'update':
                    if (!cekLevel(LEVEL_ADMIN)) done($hasil, 26);
                    $sql = "public.user";
                    unset($json["uid"]);
                    unset($json["passwd"]);
                    unset($json["inserted_at"]);
                    $json["updated_at"] = date('Y-m-d H:i:s');
                    break;
                case 'delete':
                    if (!cekLevel(LEVEL_ADMIN)) done($hasil, 26);
                    $sql = "public.user";
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
        case 'reset':
            //$old = data_read($sql,"id",$json["id"],$dbx);
            try {
                $new = data_update($sql,"id",$prms,$dbx);
                $hasil->data = $new;
            } catch (Exception $e) {
                $hasil->debug[] = array("error"=>$e->getMessage(), "sql"=>$sql,"data"=>$json);
                done($hasil, 798, "Error resetting password.");
            }
            break;
        case 'level':
        case 'listall':
            try {
                $lst = DBX($dbx)->run($sql)->fetchAll();
                $hasil->data = $lst;
                $hasil->last_page = 1;
                $hasil->last_row = count($lst);
                $hasil->count = $hasil->last_row;
                $hasil->page = 1;
            } catch (Exception $e) {
                $hasil->debug[] = array("error"=>$e->getMessage(), "sql"=>$sql,"data"=>$json);
                done($hasil, 799, "Error listing data.");
            }
            break;
        case 'list':
            $hasil->sql = $sql;
            $lst = null;
            try {
                $lst = data_list_sql($sql, true, $page, $rpp, $oby, $whr, $dbx);
                $hasil->data = $lst["rows"];
                $hasil->last_page = $lst["pages"];
                $hasil->last_row = $lst["count"];
                $hasil->count = $lst["count"];
                $hasil->rpp = $rpp;
                $hasil->page = $page;
            } catch (Exception $e) {
                $hasil->debug[] = array("error"=>$e->getMessage(), "sql"=>$sql,"data"=>$json);
                done($hasil, 800, "Error listing data.");
            }
            $hasil->debug[] = array("list-debug"=>$lst["debug"],"data"=>$json);
            break;

        case 'create':
            try {
                $obj = data_create($sql,"id",$json);
                $hasil->data = $obj;
            } catch (Exception $e) {
                $hasil->debug[] = array("error"=>$e->getMessage(), "sql"=>$sql,"data"=>$json);
                done($hasil, 801, "Error creating data.");
            }
            break;

        case 'update':
            $old = data_read($sql,"id",$json["id"],$dbx);
            $new = null;
            try {
                $new = data_update($sql,"id",$json,$dbx);
                $hasil->data = $new;
            } catch (Exception $e) {
                $hasil->debug[] = array("error"=>$e->getMessage(), "sql"=>$sql,"data"=>$json);
                done($hasil, 802, "Error updating data.");
            }
            break;

        case 'delete':
            try {
                $obj = data_delete($sql,"id",$json["id"]);
                $hasil->data = $obj;
            } catch (Exception $e) {
                $hasil->debug[] = array("error"=>$e->getMessage(), "sql"=>$sql,"data"=>$json);
                done($hasil, 803, "Error deleting data.");
            }
            break;

        case 'execsql':
            try {
                DBX($dbx)->run($sql,$prms);
            } catch (Exception $e) {
                $hasil->debug[] = array("error"=>$e->getMessage(), "sql"=>$sql, "params"=>$prms);
                done($hasil, 804, "Error executing command.");
            }
            break;
        
    }

    return done($hasil);
}
