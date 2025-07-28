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
    $drpp = 10;
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
        case 'user':
            $dbx = 0;
            $doby = array(array("field"=>"UserName","dir"=>"asc"));
            switch ($action) {
                case 'listall':
                    //$doby = array(array("field"=>"ID","dir"=>"asc"));
                    $JPOST["page"] = 1;
                    $JPOST["size"] = 10000;
                    $sql = "select * from ".withSchema("V_User");
                    $action = "list";
                    break;
                default:
                    $sql = "";
                    break;
            }
            break;

        case 'asset':
            $doby = array(array("field"=>"Type","dir"=>"desc"),array("field"=>"Name","dir"=>"asc"));
            switch ($action) {
                case 'list':
                    $sql = "SELECT a.*, b.Name Right_Name FROM CMSAsset a left join CMSRight b on a.Right_ID=b.ID";
                    break;
                case 'create':
                    $sql = "CMSAsset";
                    $json["UpdateDate"] = date('Y-m-d H:i:s');
                    $json["UserID"] = $usr["ID"];
                    break;
                case 'update':
                    $sql = "CMSAsset";
                    unset($json["UpdateBy"]);
                    $json["UpdateDate"] = date('Y-m-d H:i:s');
                    $json["UserID"] = $usr["ID"];
                    break;
                case 'delete':
                    $sql = "CMSAsset";
                    break;
                case 'finder':
                case 'createright':
                    $sql = "--";
                    break;
                default:
                    $sql = "";
                    break;
            }
            break;

        case 'right':
            $doby = array(array("field"=>"Name","dir"=>"asc"));
            switch ($action) {
                case 'list':
                    $sql = "SELECT a.* FROM CMSRight a";
                    break;
                case 'create':
                    $sql = "CMSRight";
                    $json["UpdateDate"] = date('Y-m-d H:i:s');
                    $json["UserID"] = $usr["ID"];
                    break;
                case 'update':
                    $sql = "CMSRight";
                    unset($json["UpdateBy"]);
                    $json["UpdateDate"] = date('Y-m-d H:i:s');
                    $json["UserID"] = $usr["ID"];
                    break;
                case 'delete':
                    $sql = "CMSRight";
                    break;
                case 'listall':
                    $JPOST["page"] = 1;
                    $JPOST["size"] = 10000;
                    $sql = "SELECT a.* FROM CMSRight a";
                    $action = "list";
                    break;
                default:
                    $sql = "";
                    break;
            }
            break;
        
        case 'role':
            $doby = array(array("field"=>"Name","dir"=>"asc"));
            switch ($action) {
                case 'list':
                    $sql = "SELECT a.* FROM CMSRole a";
                    break;
                case 'create':
                    $sql = "CMSRole";
                    $json["UpdateDate"] = date('Y-m-d H:i:s');
                    $json["UserID"] = $usr["ID"];
                    break;
                case 'update':
                    $sql = "CMSRole";
                    unset($json["UpdateBy"]);
                    $json["UpdateDate"] = date('Y-m-d H:i:s');
                    $json["UserID"] = $usr["ID"];
                    break;
                case 'delete':
                    $sql = "CMSRole";
                    break;
                case 'listall':
                    $JPOST["page"] = 1;
                    $JPOST["size"] = 100000;
                    $sql = "SELECT a.* FROM CMSRole a";
                    $action = "list";
                    break;
                default:
                    $sql = "";
                    break;
            }
            break;
                
        case "roleright":
            if (in_array($action,array("list","create","rightlist","assignall"))) {
                if (count($parm)<3) {
                    done($hasil, 997, "Missing parameters");
                }
                $roid = intval($parm[2]);
            }
            $doby = array(array("field"=>"Right_Name","dir"=>"asc"));
            switch ($action) {
                case "list":
                    $sql = "select a.*, b.Name Role_Name, c.Name Right_Name from CMSRole_Right a left join CMSRole b on a.Role_ID=b.ID left join CMSRight c on a.Right_ID=c.ID where a.Role_ID=".intval($roid);
                    break;
                case "create":
                    $da = data_lookup("CMSRight","ID",$json["rightID"],"DefaultAccess",$dbx);
                    $sql = "CMSRole_Right";
                    $json["Role_ID"] = $roid;
                    $json["Right_ID"] = $json["rightID"];
                    $json["DefaultAccess"] = $da;
                    $json["UpdateDate"] = date('Y-m-d H:i:s');
                    $json["UserID"] = $usr["ID"];
                    unset($json["rightID"]);
                    break;
                case "update":
                    $sql = "CMSRole_Right";
                    unset($json["UpdateBy"]);
                    $json["UpdateDate"] = date('Y-m-d H:i:s');
                    $json["UserID"] = $usr["ID"];
                    break;
                case "delete":
                    $sql = "CMSRole_Right";
                    break;
                case "rightlist":
                    $doby = array(array("field"=>"Name","dir"=>"asc"));
                    $sql = "select * from CMSRight where ID not in (select distinct Right_ID from CMSRole_Right where Role_ID=".$roid.")";
                    $action = "list";
                    break;
                case "assignall":
                    $sql = "insert into CMSRole_Right (Role_ID, Right_ID, DefaultAccess, UserID, UpdateDate)
                        select ?, ID, DefaultAccess, ?, ? 
                        from CMSRight 
                        where ID not in (select Right_ID from CMSRole_Right where ID=?)
                    ";
                    $prms[] = $roid;
                    $prms[] = $usr["ID"];
                    $prms[] = date('Y-m-d H:i:s');
                    $prms[] = $roid;
                    $action = "execsql";
                    break;
            }
            break;
        case "userrole":
            if (in_array($action,array("list","rolelist"))) {
                if (count($parm)<3) {
                    done($hasil, 997, "Missing parameters");
                }
                $xid = intval($parm[2]);
            }
            $doby = array(array("field"=>"Role_Name","dir"=>"asc"));
            switch ($action) {
                case "list":
                    $sql = "select a.*, b.Name Role_Name from CMSUser_Role a left join CMSRole b on a.Role_ID=b.ID where a.User_ID=".intval($xid);
                    break;
                case "create":
                    $sql = "CMSUser_Role";
                    $json["UpdateDate"] = date('Y-m-d H:i:s');
                    $json["UserID"] = $usr["ID"];
                    break;
                case "delete":
                    $sql = "CMSUser_Role";
                    break;
                case "rolelist":
                    $doby = array(array("field"=>"Name","dir"=>"asc"));
                    $sql = "select * from CMSRole where ID not in (select distinct Role_ID from CMSUser_Role where User_ID=".$xid.")";
                    $action = "list";
                    break;
                case "apply":
                case "reset":
                    $sql = "---";
                    break;
            }
            break;
        case "userright":
            if (in_array($action,array("list","rightlist"))) {
                if (count($parm)<3) {
                    done($hasil, 997, "Missing parameters");
                }
                $xid = intval($parm[2]);
            }
            $doby = array(array("field"=>"Right_Name","dir"=>"asc"));
            switch ($action) {
                case "list":
                    $sql = "select a.*, b.Name Right_Name from CMSUser_Right a left join CMSRight b on a.Right_ID=b.ID where a.User_ID=".$xid;
                    break;
                case "create":
                    $sql = "CMSUser_Right";
                    $json["UpdateDate"] = date('Y-m-d H:i:s');
                    $json["UserID"] = $usr["ID"];
                    break;
                case "update":
                    $sql = "CMSUser_Right";
                    unset($json["UpdateBy"]);
                    $json["UpdateDate"] = date('Y-m-d H:i:s');
                    $json["UserID"] = $usr["ID"];
                    break;
                case "delete":
                    $sql = "CMSUser_Right";
                    break;
                case "rightlist":
                    $doby = array(array("field"=>"Name","dir"=>"asc"));
                    $sql = "select * from CMSRight where ID not in (select distinct Right_ID from CMSUser_Right where User_ID=".$xid.")";
                    $action = "list";
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
    $oby = isset($JPOST["sorters"]) ? $JPOST["sorters"] : $doby;
    $whr = isset($JPOST["filter"]) ? $JPOST["filter"] : $dwhr;

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
                $obj = data_create($sql,"ID",$json, false, false, $dbx);
            } catch (Exception $e) {
                $hasil->debug[] = array("error"=>$e->getMessage(),"sql"=>$sql,"param"=>$json);
                return done($hasil, 889, "Error adding data.");
            }

            log_add($usr["ID"], "CREATE", $sql, $obj["ID"], "", json_encode($obj));
            $hasil->data = $obj;
            break;

        case 'update':
            $old = data_read($sql,"ID",$json["ID"],$dbx);
            $new = null;
            try {
                $new = data_update($sql,"ID",$json,$dbx);
            } catch (Exception $e) {
                $hasil->debug[] = array("error"=>$e->getMessage(),"sql"=>$sql,"data"=>$json);
                done($hasil, 889, "Error updating data.");
            }
        
            log_add($usr["ID"], "UPDATE", $sql, $json["ID"], json_encode($old), json_encode($new));
            $hasil->data = $new;
           break;

        case 'delete':
            $obj = null;
            try {
                $obj = data_delete($sql,"ID",$json["ID"],$dbx);
            } catch (Exception $e) {
                $hasil->debug[] = array("error"=>$e->getMessage(),"sql"=>$sql,"data"=>$json);
                done($hasil, 889, "Error deleting data.");
            }
            log_add($usr["ID"], "DELETE", $sql, $json["ID"], json_encode($obj), "");
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

        case 'finder':
            $dbx = 1;
            $pathPage = "..".DS."pages";
            $pathApi = "..".DS."hx-api";

            $pags = getDirContents($pathPage);
            $apis = getDirContents($pathApi);

            //$hasil->pages = $pags;
            //$hasil->apis = $apis;
            //$hasil->debug = array($pags,$apis);

            $nyu = 0;

            foreach ($pags as $page) {
                if ($page[2] != "php") continue;
                $pfn = $page[1];
                $pn = strtoupper(str_replace(".php","",str_replace("cms-","",$pfn)));
                $prm = array(
                    "Name" => $pn,
                    "Type" => "PAGE",
                    "Filename" => $pfn,
                    "NeedAuth" => 1,
                    "UserID" => $usr["ID"],
                    "UpdateDate" => date('Y-m-d H:i:s')
                );
                $sql = "select count(ID) cn from CMSAsset where Name=? and Type='PAGE' ";
                $cn = DBR::run($sql, array($pn))->fetchColumn();
                if ($cn == 0) {
                    try {
                        $rid = create_right($prm, $usr);
                        $prm["Right_ID"] = $rid;
                        $obj = data_create("CMSAsset","ID",$prm, false, false, $dbx);
                        $nyu++;
                    } catch (Exception $e) {
                        $hasil->debug[] = array("error"=>$e->getMessage(),"params"=>$prm);
                    }
                }
            }
            foreach ($apis as $api) {
                $pat = "..".DS."hx-api".DS."vx-";
                if (substr($api[0],0,strlen($pat)) != $pat) continue;
                $ver = explode("-", explode(DS,$api[0])[2])[1];
                $pfn = $api[1];
                $pn = strtoupper(str_replace(".php","",$pfn));
                $prm = array(
                    "Name" => $pn."-".$ver,
                    "Type" => "API",
                    "Filename" => $ver."/".$pfn,
                    "NeedAuth" => 1,
                    "UserID" => $usr["ID"],
                    "UpdateDate" => date('Y-m-d H:i:s')
                );
                $sql = "select count(ID) cn from CMSAsset where Name=? and Type='API' ";
                $cn = DBR::run($sql, array($prm["Name"]))->fetchColumn();
                if ($cn == 0) {
                    try {
                        $rid = create_right($prm, $usr);
                        $prm["Right_ID"] = $rid;
                        $obj = data_create("CMSAsset","ID",$prm, false, false, $dbx);
                        $nyu++;
                    } catch (Exception $e) {
                        $hasil->debug[] = array("error"=>$e->getMessage(),"params"=>$prm);
                    }
                }
            }
            $hasil->newItem = $nyu;
            break;

        case 'createright':
            $dbx = 1;
            $rid = create_right($json, $usr);
            $prm = array(
                "ID" => $json["ID"],
                "Right_ID" => $rid,
                "UserID" => $usr["ID"],
                "UpdateDate" => date('Y-m-d H:i:s')
            );
            $upd = null;
            try {
                $upd = data_update("CMSAsset","ID",$prm,$dbx);
            } catch (Exception $e) {
                $hasil->debug[] = array("error"=>$e->getMessage(),"json"=>$json,"params"=>$prm);
                done($hasil, 889, $e->getMessage());
            }
            $hasil->data = $upd;
            break;
        
        case 'reset':
            $dbx = 1;
            $roleid = $json["Role_ID"];
            $sql = "select * from CMSRole_Right where Role_ID=?";
            $rights = DBX($dbx)::run($sql,array($roleid))->fetchAll();
            foreach ($rights as $right) {
                $sql = "select count(ID) ids from CMSUser_Right where Right_ID=?";
                $ids = DBX($dbx)::run($sql,array($right["Right_ID"]))->fetchColumn();
                if ($ids == 0) {
                    $sql = "insert into CMSUser_Right 
                        (User_ID,User_Role_ID,Role_ID,Right_ID,Access,UserID,UpdateDate)
                        values (?,?,?,?,?,?,?)
                    ";
                    $prm = array(
                        $json["User_ID"],
                        $json["ID"],
                        $roleid,
                        $right["Right_ID"],
                        $right["DefaultAccess"],
                        $usr["ID"],
                        date('Y-m-d H:i:s')
                    );
                    try {
                        DBX($dbx)::run($sql,$prm);
                    } catch (Exception $e) {
                        $hasil->debug[] = array("error"=>$e->getMessage(),"sql"=>$sql,"params"=>$prm);
                    }
                } else {
                    $sql = "update CMSUser_Right set Access=?, UserID=?, UpdateDate=? where Right_ID=?";
                    $prm = array($right["DefaultAccess"], $usr["ID"], date('Y-m-d H:i:s'), $right["Right_ID"]);
                    try {
                        DBX($dbx)::run($sql,$prm);
                    } catch (Exception $e) {
                        $hasil->debug[] = array("error"=>$e->getMessage(),"sql"=>$sql,"params"=>$prm);
                    }
                }
            }
            break;
        
        case 'apply':
            $dbx = 1;
            $roleid = $json["Role_ID"];
            $sql = "select * from CMSRole_Right where Role_ID = ?";
            $rights = DBX($dbx)::run($sql,array($roleid))->fetchAll();
            foreach ($rights as $right) {
                $sql = "select count(ID) ids from CMSUser_Right where Right_ID=? and User_ID=?";
                $ids = DBX($dbx)::run($sql,array($right["Right_ID"], $json["User_ID"]))->fetchColumn();
                if ($ids == 0) {
                    $sql = "insert into CMSUser_Right 
                        (User_ID,User_Role_ID,Role_ID,Right_ID,Access,UserID,UpdateDate)
                        values (?,?,?,?,?,?,?)
                    ";
                    $prm = array(
                        $json["User_ID"],
                        $json["ID"],
                        $roleid,
                        $right["Right_ID"],
                        $right["DefaultAccess"],
                        $usr["ID"],
                        date('Y-m-d H:i:s')
                    );
                    try {
                        DBX($dbx)::run($sql,$prm);
                    } catch (Exception $e) {
                        $hasil->debug[] = array("error"=>$e->getMessage(),"sql"=>$sql,"params"=>$prm);
                    }
                }
            }
            //$hasil->debug = $rights;
            break;
    }

    return done($hasil);
}

function create_right($json, $usr) {
    $dbx = 1;

    $rnarr = explode("-", $json["Type"]."-".$json["Name"]);
    if ($json["Type"] == "API") $rn = array_pop($rnarr);
    $rn = implode("-", $rnarr);

    $sql = "select * from CMSRight where Name=?";
    $cnx = DBR::run($sql, array($rn))->fetchAll();
    $rid = 0;

    if (count($cnx) > 0) {
        $rid = $cnx[0]["ID"];
    } else {
        $prm = array(
            "Name" => $rn,
            "DefaultAccess" => 1,
            "UserID" => $usr["ID"],
            "UpdateDate" => date('Y-m-d H:i:s')
        );
        $res = data_create("CMSRight","ID",$prm,false,false,$dbx);
        $rid = $res["ID"];
    }
    return $rid;
}