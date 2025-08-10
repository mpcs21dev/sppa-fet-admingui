<?php
$Model = '{
        id: {caption: "ID", title: "No", type: "numeric", autoValue: true, formatter: "rownum", frozen: true},
        uid: {caption: "UserID", type: "string", readOnly: true, upperCase:true, width: 100, frozen: true, verify: (v)=>{
            if (v.length < 4) {
                FiError("Error","Minimum length for [UserID] is 4 chars");
                return false;
            } else {
                return true;
            }
        }},
        user_name: {caption: "User Name", type: "string", upperCase:true, width: 300, verify: (v)=>{
            if (v.length < 4) {
                FiError("Error","Minimum length for [User Name] is 4 chars");
                return false;
            } else {
                return true;
            }
        }},
        passwd: {caption: "Password", type: "string", control: "password", readOnly: true, visible:false, verify: (v)=>{
            if (v.length == 0) {
                FiError("Error","Empty password is not allowed");
                return false;
            } else {
                return true;
            }
        }},
        ulevel:{caption:"User Level", type:"numeric", noview:true, visible:false, control:"lookup", option:{table:"UserLevel", id:"xkey", text:"xval"}},
        user_level:{caption:"User Level", type:"string", noadd:true, noedit:true},
        email: {caption:"Email", type: "string"},
        chpwd: {caption:"Ch Pass", type: "boolean", formatter: boolFormatter, hozAlign: "center"},
        enabled: {caption:"Enable", type: "boolean", noadd: true, formatter: boolFormatter, hozAlign: "center"},
        inserted_at: {caption:"Created", type: "datetime", autoValue: true, formatter: datetimeFormatter},
        updated_at: {caption:"Updated", type: "datetime", autoValue: true, formatter: datetimeFormatter}
}';

$PRM = array(
    "icon" => "users",
    "caption" => "CMS Users",
    "accordionID" => "acc_cms",
    "menuID" => "menu-cmsuser",
    "apiList" => "api/?1/user/user/list",
    "apiCreate" => "",
    "apiUpdate" => "api/?1/user/user/update",
    "apiDelete" => "",
    "uppercase" => false,
    "labelField" => "UserName",
    "extraButtons" => "
        <div class='padder2'></div>
        <div class='ui mini icon buttons'>
            <button class='ui orange button' id='btnReset'><i class='key icon'></i> Reset Pwd</button>
        </div>
    ",
    "jsStartup" => "
        $('#btnReset').on('click', btnReset_click);

        Ref.load('User', 'api/?1/user/user/listall');
        Ref.load('UserLevel', 'api/?1/user/user/level');
    ",
    "fnAdd" => "btnAdd2_click",
    "fnDelete" => "btnDel_click",
    "addAfterShow" => "",
    "editAfterShow" => "
        \$('#fld-ulevel').dropdown('set selected', sel['ulevel']);
    ",
    "model" => $Model,
    "extraJS" => "
        btnAdd2_click = () => {
            XFrame.setCaption('Add User')
                .setContent(frmPage.formAdd('add_user'))
                .setConfirmation()
                .setVerifier(true, ()=>{ return frmPage.doVerify(); })
                .setAction(true,()=>{
                    var fdata = frmPage.readForm(false,true);
                    var pwd = fdata.get('passwd');
                    fdata.set('passwd',forge_sha256(pwd));
                    Loader('Creating New User...');
                    //console.log(fdata);
                    Api('api/?1/user/user/create', {body: fdata}).then(
                        data => {
                            LoaderHide();
                            if (data.error == 0) {
                                ToastSuccess('New User registered');
                                btnRefresh_click();
                            } else {
                                FError('Add User Failed', data.message);
                            }
                        },
                        error => {
                            LoaderHide();
                            FError('Add User Error', error);
                        }
                    );
                })
                .setAfterShow(()=>{
                    $('.dropdown').dropdown();
                    $('#fld-ulevel').dropdown('set selected', 1);
                })
                .show();
        }
        btnDel_click = () => {
            var sel = (Table.getSelectedData())[0]; // get first selected element
            if (sel == undefined) {
                ToastError('No row selected');
                return;
            }

            if (sel['uid'] == 'ROOT') {
                ToastError('Delete User','Cannot delete user ROOT');
                return;
            }

            if (getSetting('user-data')['uid'] != 'ROOT'  &&  getSetting('user-data')['uid'] != 'APPDEV') {
                ToastError('Delete User','Only ROOT can delete user');
                return;
            }

            $('body').modal('myConfirm', `<i class='exclamation triangle icon red'></i> Delete User`, `Delete user [\${sel.user_name}] ?`, ()=>{
                Loader('Deleting User...');
                var fdata = frmPage.formDataTabRow(sel);
                Api('api/?1/user/user/delete', {body: fdata}).then(
                    data => {
                        LoaderHide();
                        if (data.error == 0) {
                            ToastSuccess('User deleted');
                            btnRefresh_click();
                        } else {
                            FError('Delete User Failed', data.message);
                        }
                    },
                    error => {
                        LoaderHide();
                        FError('Delete User Error', error);
                    }
                );
            });
        }
        btnReset_click = () => {
            var sel = (Table.getSelectedData())[0]; // get first selected element
            if (sel == undefined) {
                ToastError('No row selected');
                return;
            }

            if (sel['ulevel'] == 99) {
                ToastError('Reset Password','Cannot reset password for user ROOT');
                return;
            }

            if (getSetting('user-data')['ulevel'] < 5) {
                ToastError('Reset Password','Only Administrator can reset user password');
                return;
            }

            var html = `<form class='ui form' id='rst_user' onsubmit='return false;'>`+
                    `<div class='field'><label>New Password</label>`+
                    `<input type='password' id='fld-pwd0' name='pwd0' placeholder='New Password'>`+
                    '</div>'+
                    `<div class='field'><label>Confirm Password</label>`+
                    `<input type='password' id='fld-pwd1' name='pwd1' placeholder='Confirm Password'>`+
                    '</div>';

            XFrame.setCaption('Reset Password')
                .setContent(html)
                .setVerifier(true, ()=>{
                    var data = {
                        pwd1: \$id('fld-pwd0').value,
                        pwd2: \$id('fld-pwd1').value
                    };
                    if (data.pwd1 != data.pwd2) {
                        ToastError('Error','New password not confirmed');
                        $('#fld-pwd2').focus();
                        return false;
                    }
                    if (data.pwd1 == '') {
                        ToastError('Error','Empty new password');
                        $('#fld-pwd1').focus();
                        return false;
                    }
                    if (data.pwd1.length < 6) {
                        ToastError('Error','Minimum new password length is 6 chars');
                        $('#fld-pwd1').focus();
                        return false;
                    }
                    return true;
                })
                .setConfirmation()
                .setAction(true,()=>{
                    var fdata = frmPage.formDataManual(['id',sel['id'],'passwd',forge_sha256(\$id('fld-pwd0').value)]);
                    Loader('Resetting Password...');
                    Api('api/?1/user/user/reset', {body: fdata}).then(
                        data => {
                            LoaderHide();
                            if (data.error == 0) {
                                ToastSuccess('Password updated');
                                //btnRefresh_click();
                            } else {
                                FError('Reset Password Failed', data.message);
                            }
                        },
                        error => {
                            LoaderHide();
                            FError('Reset Password Error', error);
                        }
                    );
                })
                .show();
        }
    "
);

include_once "pages/cms-genform.php";
