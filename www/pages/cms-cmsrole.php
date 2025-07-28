<?php
$Model = '{
    ID:        {caption:"ID", title:"No", type:"numeric", autoValue:true, formatter: autoNumFormatter, frozen:true},
    Name:      {caption:"Name", type:"string", frozen:true, upperCase:true, headerFilter: true},
    UpdateDate:{caption:"Last Update", type:"datetime", autoValue:true, formatter: datetimeFormatter},
    UserID:    {caption:"Update By", type:"string", autoValue:true, formatter: useridRefFormatter}
}';

$PRM = array(
    "icon" => "medal",
    "caption" => "CMS Roles",
    "accordionID" => "acc_cms",
    "menuID" => "menu-cmsrole",
    "apiList" => "api/?99/cmsasset/role/list",
    "apiCreate" => "api/?99/cmsasset/role/create",
    "apiUpdate" => "api/?99/cmsasset/role/update",
    "apiDelete" => "api/?99/cmsasset/role/delete",
    "labelField" => "Name",
    "extraButtons" => "
        <div class='padder2'></div>
        <div class='ui buttons'>
            <button class='ui mini green button' id='btnRight'><i class='check double icon'></i> Role Rights</button>
        </div>
    ",
    "jsStartup" => "
        $('#btnRight').on('click', btnRight_click);

        Ref.load('User', 'api/?99/cmsasset/user/listall');
    ",
    "extraJS" => "
        btnRight_click = () => {
            var sel = (Table.getSelectedData())[0]; // get first selected element
            if (sel == undefined) {
                ToastError('No row selected');
                return;
            }
            document.location = '?p=cmsroleright/'+sel.ID;
        }
    ",
    "addAfterShow" => "",
    "editAfterShow" => "",
    "model" => $Model
);

include_once "pages/cms-genform.php";
