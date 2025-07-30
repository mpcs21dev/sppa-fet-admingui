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

    switch ($table) {
        case 'ref':
            switch ($action) {
                case 'rec-type':
                    $sql = "select str_key xkey, str_val xval from public.reference where name='PART-REC-TYPE' order by str_val, str_key";
                    break;
                default:
                    $sql = "";
                    break;
            }
            break;
        case 'config':
            $doby = array(array("field"=>"participant_id","dir"=>"asc"));
            switch ($action) {
                case 'listall-noftp':
                    $sql = "SELECT * FROM public.config where record_type <> 'FTP' order by record_type, participant_id";
                    break;
                case 'listall':
                    $sql = "SELECT * FROM public.config order by record_type, participant_id";
                    break;
                case 'listservice':
                case 'list':
                    $sql = "SELECT a.*, b.str_val rec_type_str FROM public.config a left join reference b on a.record_type=b.str_key and b.name='PART-REC-TYPE'";
                    break;
                case 'create':
                    $sql = "public.config";
                    $json["inserted_at"] = date('Y-m-d H:i:s');
                    //$json["inserted_by"] = $usr["id"];
                    $json["updated_at"] = date('Y-m-d H:i:s');
                    $json["data"] = "[]";
                    //$json["updated_by"] = $usr["id"];
                    //$json["data"] = "{\"bbId\":\"{$json['bbId']}\",\"firmId\":\"{$json['firmId']}\",\"sourceId\":\"{$json['sourceId']}\"}";
                    unset($json["bbId"]);
                    unset($json["firmId"]);
                    unset($json["sourceId"]);
                    break;
                case 'update':
                    $sql = "public.config";
                    $json["updated_at"] = date('Y-m-d H:i:s');
                    //$json["updated_by"] = $usr["id"];
                    //$json["data"] = "{\"bbId\":\"{$json['bbId']}\",\"firmId\":\"{$json['firmId']}\",\"sourceId\":\"{$json['sourceId']}\"}";
                    unset($json["bbId"]);
                    unset($json["firmId"]);
                    unset($json["sourceId"]);
                    unset($json["rec_type_str"]);
                    break;
                case 'delete':
                    $sql = "public.config";
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
        $hasil->table = $table;
        $hasil->action = $action;
        $hasil->sql = $sql;
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
        case 'rec-type':
        case 'listall':
        case 'listall-noftp':
            $lst = DBX($dbx)->run($sql)->fetchAll();
            $hasil->data = $lst;
            $hasil->last_page = 1;
            $hasil->last_row = count($lst);
            $hasil->count = count($lst);
            $hasil->page = 1;
            break;
        case 'listservice':
            $lst = DBX($dbx)->run($sql)->fetchAll();
            $dat = array();
            foreach ($lst as $row) {
                $rec = array(
                    "id" => $row["id"],
                    "participant_id" => $row["participant_id"],
                    "service_name" => "sppafet-service-dev-".strtolower($row['participant_id'])."-net",
                    "service_port" => "80",
                    "status" => "---"
                );
                $dat[] = $rec;
            }
            $hasil->data = $dat;
            $hasil->last_page = 1;
            $hasil->last_row = count($dat);
            $hasil->count = count($dat);
            $hasil->page = 1;
            break;
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
                if ($json["record_type"] == "PART") {
                    $create = "CREATE TABLE \"transaction_".strtolower($json['participant_id'])."\" (
                            \"id\" INTEGER NOT NULL UNIQUE GENERATED BY DEFAULT AS IDENTITY,
                            \"record_date\" INTEGER NOT NULL,
                            \"order_side\" VARCHAR(3) NOT NULL,
                            \"order_id\" VARCHAR(50) NOT NULL,
                            \"cln_order_id\" VARCHAR(50) NOT NULL,
                            \"mkt_order_id\" VARCHAR(50) NOT NULL,
                            \"cln_user_id\" VARCHAR(50) NOT NULL,
                            \"cln_party_id\" VARCHAR(25) NOT NULL,
                            \"trd_user_id\" VARCHAR(50) NOT NULL,
                            \"trd_party_id\" VARCHAR(25) NOT NULL,
                            \"initiator\" INTEGER NOT NULL,
                            \"trade_id\" VARCHAR(50),
                            \"status\" INTEGER NOT NULL,
                            \"inserted_at\" TIMESTAMP,
                            \"updated_at\" TIMESTAMP,
                            PRIMARY KEY(\"id\")
                        );
                    ";
                    DBX($dbx)->run($create);
                    $create = "CREATE TABLE \"message_".strtolower($json['participant_id'])."\" (
                            \"id\" INTEGER NOT NULL UNIQUE GENERATED BY DEFAULT AS IDENTITY,
                            \"parent_id\" INTEGER NOT NULL,
                            \"direction\" VARCHAR(3) NOT NULL,
                            \"record_type\" VARCHAR(5) NOT NULL,
                            \"message_type\" VARCHAR(5) NOT NULL,
                            \"data\" VARCHAR NOT NULL,
                            \"inserted_at\" TIMESTAMP,
                            \"updated_at\" TIMESTAMP,
                            PRIMARY KEY(\"id\")
                        );
                    ";
                    DBX($dbx)->run($create);
                }
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
