<?php
$Model = '{
    id:    {caption:"ID", type:"string", formatting: "rownum", autoValue:true, frozen:true},
    month_name:  {caption:"Month Name", type:"string"},
    date_list: {title:"Date List",caption:"Date List (numbers and pipe only)", type:"string",verify:(val)=>{
        let hasil = true;
        const arr = val.split("");
        for (var i in arr) {
            const c = arr[i];
            const d = "1234567890|".indexOf(c);
            if (d<0) {
                hasil = false;
                FiError("Error","Illegal character detected. Only numbers and pipe are allowed.");
                break;
            }
        }
        return hasil;        
    }},
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
       // \$id('btnAdd').parentNode.removeChild(\$id('btnAdd'));
      //  \$id('btnDelete').parentNode.removeChild(\$id('btnDelete'));
    ",
    "addAfterShow" => "",
    "editAfterShow" => "",
    "model" => $Model,
    "extraJS" => "",
);

include_once "pages/cms-genform.php";
