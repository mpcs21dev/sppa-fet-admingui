<?php
$Model = '{
    id:    {title: "No", caption:"ID", type:"string", formatting: autoNumFormatter, autoValue:true, frozen:true},
    participant_id:  {caption:"Participant", type:"string"},
    service_name: {caption:"Service", type:"string"},
    service_port: {caption: "Port", type: "string"},
    status: {caption: "Status", type: "string"}
}';

$PRM = array(
    "icon" => "file invoice dollar",
    "caption" => "Participant Services",
    "accordionID" => "",
    "menuID" => "menu-participant",
    "apiList" => "api/?1/config/config/listservice",
    "apiCreate" => "api/?1/holiday/holiday/create",
    "apiUpdate" => "api/?1/holiday/holiday/update",
    "apiDelete" => "api/?1/holiday/holiday/delete",
    "labelField" => "participant_id",
    "extraButtons" => "",
    "jsStartup" => "
        //Ref.load('User', 'api/?99/cmsasset/user/listall');
        //\$id('btnAdd').parentNode.removeChild(\$id('btnAdd'));
        \$id('btnEdit').parentNode.removeChild(\$id('btnEdit'));
        \$id('btnDelete').parentNode.removeChild(\$id('btnDelete'));
    ",
    "addAfterShow" => "",
    "editAfterShow" => "",
    "model" => $Model,
    //"fnView" => "btnView2_click",
    "fnAdd" => "btnAdd2_click",
    "extraJS" => "
        btnAdd2_click = () => {
            alert('Not implemented yet.');
        }
    ",
);

include_once "pages/cms-genform.php";
