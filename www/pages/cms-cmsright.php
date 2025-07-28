<?php
$Model = '{
    ID:        {caption:"ID", title:"No", type:"numeric", autoValue:true, formatter: autoNumFormatter, frozen:true},
    Name:      {caption:"Name", type:"string", frozen:true, upperCase:true, headerFilter: true},
    DefaultAccess:{caption:"Default Access", type:"boolean", formatter: boolFormatter, hozAlign:"center",headerHozAlign:"center"},
    UpdateDate:{caption:"Last Update", type:"datetime", autoValue:true, formatter: datetimeFormatter, hozAlign:"right", headerHozAlign: "right"},
    UserID:    {caption:"Update By", type:"string", autoValue:true, formatter: useridRefFormatter}
}';

$PRM = array(
    "icon" => "check double",
    "caption" => "CMS Rights",
    "accordionID" => "acc_cms",
    "menuID" => "menu-cmsright",
    "apiList" => "api/?99/cmsasset/right/list",
    "apiCreate" => "api/?99/cmsasset/right/create",
    "apiUpdate" => "api/?99/cmsasset/right/update",
    "apiDelete" => "api/?99/cmsasset/right/delete",
    "labelField" => "Name",
    "extraButtons" => "",
    "jsStartup" => "
        Ref.load('User', 'api/?99/cmsasset/user/listall');
    ",
    "extraJS" => "",
    "addAfterShow" => "",
    "editAfterShow" => "",
    "model" => $Model
);

include_once "pages/cms-genform.php";
