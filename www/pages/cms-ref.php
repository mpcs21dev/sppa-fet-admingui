<?php
$Model = '{
    id:        {caption:"ID", title:"No", type:"numeric", autoValue:true, formatter: autoNumFormatter, frozen:true},
    name:      {caption:"Name", type:"string", frozen:true, upperCase:true, headerFilter: true},
    int_key:   {caption:"INT Key", type:"numeric", headerFilter: true},
    str_key:   {caption:"STR Key", type:"string", upperCase:true, headerFilter: true},
    str_val:   {caption:"Value", type:"string", upperCase:true, headerFilter: true},
    updated_at:{caption:"Updated Date", type:"datetime", autoValue:true, formatter: datetimeFormatter},
    inserted_at:{caption:"Inserted Date", type:"datetime", autoValue:true, formatter: datetimeFormatter}
}';

$PRM = array(
    "icon" => "check double",
    "caption" => "Reference",
    "accordionID" => "",
    "menuID" => "menu-ref",
    "apiList" => "api/?1/ref/ref/list",
    "apiCreate" => "api/?1/ref/ref/create",
    "apiUpdate" => "api/?1/ref/ref/update",
    "apiDelete" => "api/?1/ref/ref/delete",
    "labelField" => "name",
    "extraButtons" => "",
    "jsStartup" => "",
    "extraJS" => "",
    "addAfterShow" => "",
    "editAfterShow" => "",
    "model" => $Model
);

include_once "pages/cms-genform.php";
