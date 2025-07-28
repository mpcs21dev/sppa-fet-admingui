<?php
include_once "api/db.php";
include_once "api/const.php";
include_once "api/fn.db.php";

$dbx = 1;
$USERID = $PARAM[0];
$DAT = array();
$DAT = data_read(withSchema("V_User"),"ID",$USERID);

$LeftModel = '{
    ID:        {caption:"ID", title:"No", type:"numeric", autoValue:true, formatter: autoNumFormatter, frozen:true},
    //User_ID:   {caption:"Role", type:"int", visible:false, control:"lookup", option:{table:"Role", id:"ID", text:"Name"}},
    //User_Name: {caption:"Role", type:"string", noadd:true, noedit:true},
    //Role_ID:     {caption:"Role", type:"int", visible:false, control:"lookup", option:{table:"Role", id:"ID", text:"Name"}},
    Role_Name:   {caption:"Role", type:"string", noadd:true, noedit:true, headerFilter:true}
    //UpdateDate:{caption:"Last Update", type:"datetime", autoValue:true, formatter: datetimeFormatter},
    //UserID:    {caption:"Update By", type:"string", autoValue:true, formatter: useridRefFormatter}
}';

$RightModel = '{
    ID:        {caption:"ID", title:"No", type:"numeric", autoValue:true, formatter: autoNumFormatter, frozen:true},
    Name:      {caption:"Role", type:"string", upperCase:true, headerFilter: true}
    //UpdateDate:{caption:"Last Update", type:"datetime", autoValue:true, formatter: datetimeFormatter},
    //UserID:    {caption:"Update By", type:"string", autoValue:true, formatter: useridRefFormatter}
}';

$PRM = array(
    "icon" => "tasks",
    "caption" => "CMS User Roles - ".$DAT["Uid"]." :: ".$DAT["UserName"],
    "accordionID" => "acc_cms",
    "menuID" => "menu-cmsuser",

    "leftModel" => $LeftModel,
    "leftLabel" => "User Roles",
    "leftApiList"   => "api/?99/cmsasset/userrole/list/".$USERID,
    "leftButtons" => "
        <button class='ui mini brown button' id='btnApply'><i class='check double icon'></i> Apply</button>
        <button class='ui mini brown button' id='btnReset'><i class='recycle icon'></i> Reset</button>
        <button class='ui mini red button' id='btnDel'><i class='minus circle icon'></i> Remove</button>
    ",

    "rightModel" => $RightModel,
    "rightLabel" => "Role List",
    "rightApiList" => "api/?99/cmsasset/userrole/rolelist/".$USERID,
    "rightButtons" => "
        <button class='ui mini blue button' id='btnAdd'><i class='chevron circle left icon'></i> Assign</button>
    ",

    "jsStartup" => "
        $('#btnAdd').on('click', btnAdd_click);
        $('#btnDel').on('click', btnDel_click);
        $('#btnApply').on('click', btnApply_click);
        $('#btnReset').on('click', btnReset_click);

        Ref.load('User', 'api/?99/cmsasset/user/listall');
    ",
    "extraJS" => "
        btnAdd_click = () => {
            var sel = tbltrGetSelRow(Tablr, 'Role list not selected');
            if (sel == null) return;

            Loader('Assigning...');
            var fdata = frmRight.formDataManual(['User_ID', $USERID, 'Role_ID', sel.ID]);
            Api('api/?99/cmsasset/userrole/create', {body: fdata}).then(
                data => {
                    LoaderHide();
                    if (data.error == 0) {
                        ToastSuccess('Role assigned');
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
            var sel = tbltrGetSelRow(Tabll, 'User role not selected');
            if (sel == null) return;

            $('body').modal('myConfirm', \"<i class='exclamation triangle icon red'></i> Delete Record\", 'Delete [ '+sel.Role_Name+' ] ?', ()=>{
                Loader('Deleting record...');
                var fdata = frmLeft.formDataTabRow(sel);
                Api('api/?99/cmsasset/userrole/delete', {body: fdata}).then(
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
        btnApply_click = () => {
            var sel = tbltrGetSelRow(Tabll, 'User role not selected');
            if (sel == null) return;

            $('body').modal('myConfirm', \"<i class='exclamation triangle icon red'></i> Apply Role\", 'Rights from selected Role will be applied with its default values. Existing Right will not be replaced.<br><br>Continue ?', ()=>{
                Loader('Applying role...');
                var fdata = frmLeft.formDataTabRow(sel);
                Api('api/?99/cmsasset/userrole/apply', {body: fdata}).then(
                    data => {
                        LoaderHide();
                        if (data.error == 0) {
                            ToastSuccess('Role applied.');
                            //Tabl1.replaceData();
                            //var cp = Tabll.getPage();
                            //Tabll.setPage(cp);
                            //cp = Tablr.getPage();
                            //Tablr.setPage(cp);
                        } else {
                            FError('Applying role failed', data.message);
                        }
                    },
                    error => {
                        LoaderHide();
                        FError('Applying role error', error);
                    }
                );
            });
        }
        btnReset_click = () => {
            var sel = tbltrGetSelRow(Tabll, 'User role not selected');
            if (sel == null) return;

            $('body').modal('myConfirm', \"<i class='exclamation triangle icon red'></i> Reset Role\", 'Rights from selected Role will be applied. Existing Right will be replaced to its default values.<br><br>Continue ?', ()=>{
                Loader('Resetting role...');
                var fdata = frmLeft.formDataTabRow(sel);
                Api('api/?99/cmsasset/userrole/reset', {body: fdata}).then(
                    data => {
                        LoaderHide();
                        if (data.error == 0) {
                            ToastSuccess('Reset success.');
                            //Tabl1.replaceData();
                            var cp = Tabll.getPage();
                            Tabll.setPage(cp);
                            cp = Tablr.getPage();
                            Tablr.setPage(cp);
                        } else {
                            FError('Resetting role failed', data.message);
                        }
                    },
                    error => {
                        LoaderHide();
                        FError('Resetting role error', error);
                    }
                );
            });
        }
    "
);

include_once "pages/cms-genform2.php";
