<?php
$Model = '{
    id:    {caption:"ID", type:"string", formatting: "rownum", autoValue:true, frozen:true},
    month_name:  {caption:"Month Name", type:"string"},
    date_list: {caption:"Date List", type:"string"},
    inserted_at: {caption: "Created", type: "datetime", autoValue: true, formatter: datetimeFormatter},
    updated_at: {caption: "Updated", type: "datetime", autoValue: true, formatter: datetimeFormatter}
}';

$PRM = array(
    "icon" => "file invoice dollar",
    "caption" => "Holiday",
    "accordionID" => "",
    "menuID" => "menu-holiday",
    "apiList" => "api/?1/holiday/holiday/list",
    "apiCreate" => "api/?1/holiday/holiday/create",
    "apiUpdate" => "api/?1/holiday/holiday/update",
    "apiDelete" => "api/?1/holiday/holiday/delete",
    "labelField" => "month_name",
    "extraButtons" => "",
    "jsStartup" => "
        //Ref.load('User', 'api/?99/cmsasset/user/listall');
      //  \$id('btnAdd').parentNode.removeChild(\$id('btnAdd'));
      //  \$id('btnDelete').parentNode.removeChild(\$id('btnDelete'));
    ",
    "addAfterShow" => "",
    "editAfterShow" => "",
    "model" => $Model,
    //"fnView" => "btnView2_click",
    //"fnEdit" => "btnEdit2_click",
    "extraJS" => "",
);

include_once "pages/cms-genform.php";
