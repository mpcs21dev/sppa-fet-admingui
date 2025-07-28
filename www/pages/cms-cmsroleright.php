<?php
include_once "api/db.php";
include_once "api/fn.db.php";

$dbx = 1;
$ROLEID = $PARAM[0];
$DAT = data_read("CMSRole","ID",$ROLEID,$dbx);

$LeftModel = '{
    ID:        {caption:"ID", title:"No", type:"numeric", autoValue:true, formatter: autoNumFormatter, frozen:true},
    //Role_ID:   {caption:"Role", type:"int", visible:false, control:"lookup", option:{table:"Role", id:"ID", text:"Name"}},
    //Role_Name: {caption:"Role", type:"string", noadd:true, noedit:true},
    //Right_ID:  {caption:"Right", type:"int", visible:false, control:"lookup", option:{table:"Right", id:"ID", text:"Name"}},
    Right_Name:{caption:"Right", type:"string", noadd:true, noedit:true, headerFilter:true},
    DefaultAccess:{caption:"Default Access", type:"boolean", formatter: boolFormatter, hozAlign:"center", headerHozAlign:"center"},
    //UpdateDate:{caption:"Last Update", type:"datetime", autoValue:true, formatter: datetimeFormatter},
    //UserID:    {caption:"Update By", type:"string", autoValue:true, formatter: useridRefFormatter}
}';

$RightModel = '{
    ID:        {caption:"ID", title:"No", type:"numeric", autoValue:true, formatter: autoNumFormatter, frozen:true},
    Name:      {caption:"Right", type:"string", frozen:true, upperCase:true, headerFilter: true},
    DefaultAccess:{caption:"Default Access", type:"boolean", formatter: boolFormatter, hozAlign: "center", headerHozAlign:"center"},
    //UpdateDate:{caption:"Last Update", type:"datetime", autoValue:true, formatter: datetimeFormatter},
    //UserID:    {caption:"Update By", type:"string", autoValue:true, formatter: useridRefFormatter}
}';

$PRM = array(
    "icon" => "tasks",
    "caption" => "CMS Roles Rights - ".$DAT["Name"],
    "accordionID" => "acc_cms",
    "menuID" => "menu-cmsrole",

    "leftModel" => $LeftModel,
    "leftLabel" => "Role Right",
    "leftApiList"   => "api/?99/cmsasset/roleright/list/".$ROLEID,
    "leftButtons" => "
        <button class='ui mini blue button' id='btnTog'><i class='check icon'></i> Toggle Access</button>
        <button class='ui mini red button' id='btnDel'><i class='minus circle icon'></i> Remove</button>
    ",

    "rightModel" => $RightModel,
    "rightLabel" => "Right List",
    "rightApiList" => "api/?99/cmsasset/roleright/rightlist/".$ROLEID,
    "rightButtons" => "
        <button class='ui mini blue button' id='btnAdd'><i class='chevron circle left icon'></i> Assign</button>
        <button class='ui mini orange button' id='btnAll'><i class='angle double left icon'></i> Assign All</button>
    ",

    "jsStartup" => "
        $('#btnAdd').on('click', btnAdd_click);
        $('#btnDel').on('click', btnDel_click);
        $('#btnTog').on('click', btnTog_click);
        $('#btnAll').on('click', btnAll_click);

        Ref.load('User', 'api/?99/cmsasset/user/listall');
    ",
    "extraJS" => "
        btnAdd_click = () => {
            var sel = tbltrGetSelRow(Tablr, 'Right list not selected');
            if (sel == null) return;

            Loader('Assigning...');
            var fdata = frmRight.formDataManual(['rightID', sel.ID]);
            Api('api/?99/cmsasset/roleright/create/{$ROLEID}', {body: fdata}).then(
                data => {
                    LoaderHide();
                    if (data.error == 0) {
                        ToastSuccess('Right assigned');
                        //Tabl1.replaceData();
                        var cp = Tablr.getPage();
                        Tablr.setPage(cp);

                        cp = Tabll.getPage();
                        Tabll.setPage(cp);
                    } else {
                        FError('Assigning Failed', data.message);
                    }
                },
                error => {
                    LoaderHide();
                    FError('Assigning Error', error);
                }
            );
        }
        btnDel_click = () => {
            var sel = tbltrGetSelRow(Tabll, 'Role-right not selected');
            if (sel == null) return;

            $('body').modal('myConfirm', \"<i class='exclamation triangle icon red'></i> Delete Record\", 'Delete ['+sel.Right_Name+'] ?', ()=>{
                Loader('Deleting record...');
                var fdata = frmRight.formDataTabRow(sel);
                Api('api/?99/cmsasset/roleright/delete', {body: fdata}).then(
                    data => {
                        LoaderHide();
                        if (data.error == 0) {
                            ToastSuccess('Record deleted');
                            //Tabl1.replaceData();
                            var cp = Tabll.getPage();
                            Tabll.setPage(cp);
                            cp = Tablr.getPage();
                            Tablr.setPage(cp);
                        } else {
                            FError('Delete Failed', data.message);
                        }
                    },
                    error => {
                        LoaderHide();
                        FError('Delete Error', error);
                    }
                );
            });
        }
        btnTog_click = () => {
            var sel = tbltrGetSelRow(Tabll, 'Role-right not selected');
            if (sel == null) return;

            var fdata = frmLeft.formDataManual(['ID',sel.ID,'DefaultAccess',sel.DefaultAccess==0?1:0]);
            Api('api/?99/cmsasset/roleright/update', {body: fdata}).then(
                data => {
                    LoaderHide();
                    if (data.error == 0) {
                        ToastSuccess('Default access toggled');
                        //Tabl1.replaceData();
                        var cp = Tabll.getPage();
                        Tabll.setPage(cp);
                        cp = Tablr.getPage();
                        Tablr.setPage(cp);
                    } else {
                        FError('Toggle Failed', data.message);
                    }
                },
                error => {
                    LoaderHide();
                    FError('Toggle Error', error);
                }
            );
        }
        btnAll_click = () => {
            $('body').modal('myConfirm', \"<i class='exclamation triangle icon red'></i> Assign All\", 'Assign all Rights to current Role ?', ()=>{
                Loader('Assigning rights...');

                Api('api/?99/cmsasset/roleright/assignall/{$ROLEID}').then(
                    data => {
                        LoaderHide();
                        if (data.error == 0) {
                            ToastSuccess('All Rights assigned');
                            //Tabl1.replaceData();
                            var cp = Tabll.getPage();
                            Tabll.setPage(cp);
                            cp = Tablr.getPage();
                            Tablr.setPage(cp);
                        } else {
                            FError('Assign Failed', data.message);
                        }
                    },
                    error => {
                        LoaderHide();
                        FError('Assign Error', error);
                    }
                );
            });
        }
    "
);

include_once "pages/cms-genform2.php";
