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
    //User_ID:     {caption:"Role", type:"int", visible:false, control:"lookup", option:{table:"Role", id:"ID", text:"Name"}},
    //User_Name:   {caption:"Role", type:"string", noadd:true, noedit:true},
    //Role_ID:     {caption:"Role", type:"int", visible:false, control:"lookup", option:{table:"Role", id:"ID", text:"Name"}},
    Right_Name:  {caption:"Right", type:"string", noadd:true, noedit:true, headerFilter:true},
    Access:      {caption:"Default Access", type:"boolean", formatter: boolFormatter, hozAlign:"center", headerHozAlign:"center"},
    //UpdateDate:{caption:"Last Update", type:"datetime", autoValue:true, formatter: datetimeFormatter},
    //UserID:    {caption:"Update By", type:"string", autoValue:true, formatter: useridRefFormatter}
}';

$RightModel = '{
    ID:        {caption:"ID", title:"No", type:"numeric", autoValue:true, formatter: autoNumFormatter, frozen:true},
    Name:      {caption:"Right", type:"string", upperCase:true, headerFilter: true},
    DefaultAccess:{caption:"Default Access", type:"boolean", formatter: boolFormatter, hozAlign: "center", headerHozAlign:"center"},
    //UpdateDate:{caption:"Last Update", type:"datetime", autoValue:true, formatter: datetimeFormatter},
    //UserID:    {caption:"Update By", type:"string", autoValue:true, formatter: useridRefFormatter}
}';

$PRM = array(
    "icon" => "tasks",
    "caption" => "CMS User Rights - ".$DAT["Uid"]." :: ".$DAT["UserName"],
    "accordionID" => "acc_cms",
    "menuID" => "menu-cmsuser",

    "leftModel" => $LeftModel,
    "leftLabel" => "User Rights",
    "leftApiList"   => "api/?99/cmsasset/userright/list/".$USERID,
    "leftButtons" => "
        <button class='ui mini blue button' id='btnTog'><i class='check icon'></i> Toggle Access</button>
        <button class='ui mini red button' id='btnDel'><i class='minus circle icon'></i> Remove</button>
    ",

    "rightModel" => $RightModel,
    "rightLabel" => "Right List",
    "rightApiList" => "api/?99/cmsasset/userright/rightlist/".$USERID,
    "rightButtons" => "
        <button class='ui mini blue button' id='btnAdd'><i class='chevron circle left icon'></i> Assign</button>
    ",

    "jsStartup" => "
        $('#btnAdd').on('click', btnAdd_click);
        $('#btnDel').on('click', btnDel_click);
        $('#btnTog').on('click', btnTog_click);

        //Ref.load('User', 'api/?99/cmsasset/user/listall');
    ",
    "extraJS" => "
        btnAdd_click = () => {
            var sel = tbltrGetSelRow(Tablr, 'Right list not selected');
            if (sel == null) return;

            Loader('Assigning...');
            var fdata = frmRight.formDataManual(['User_ID', $USERID, 'Right_ID', sel.ID, 'Access', sel.DefaultAccess]);
            Api('api/?99/cmsasset/userright/create', {body: fdata}).then(
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
            var sel = tbltrGetSelRow(Tabll, 'User right not selected');
            if (sel == null) return;

            $('body').modal('myConfirm', \"<i class='exclamation triangle icon red'></i> Delete Record\", 'Delete [ '+sel.Right_Name+' ] ?', ()=>{
                Loader('Deleting record...');
                var fdata = frmLeft.formDataTabRow(sel);
                Api('api/?99/cmsasset/userright/delete', {body: fdata}).then(
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
            var sel = tbltrGetSelRow(Tabll, 'User right not selected');
            if (sel == null) return;

            var fdata = frmLeft.formDataManual(['ID',sel.ID,'Access',sel.Access==0?1:0]);
            Api('api/?99/cmsasset/userright/update', {body: fdata}).then(
                data => {
                    LoaderHide();
                    if (data.error == 0) {
                        ToastSuccess('Access toggled');
                        //Tabl1.replaceData();
                        var cp = Tabll.getPage();
                        Tabll.setPage(cp);
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
    "
);

include_once "pages/cms-genform2.php";
