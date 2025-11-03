<div class="ui top attached segment">
    <div class="ui header"><h1><i class="sign in alternate icon"></i> Login</h1></div>
</div>
<div class="standard">
    <form id="frm_login" class="ui form" method="POST">
        <div class="field">
            <label>User ID</label>
            <input type="text" id="uid" name="uid" placeholder="User ID">
        </div>
        <div class="field">
            <label>Password</label>
            <input type="password" id="pwd" name="pwd" placeholder="Password">
        </div>
        <button class="ui button" type="submit">Submit</button>
    </form>
</div>
<script type="text/javascript">
    var oldies = null;
    mod_startup = () => {
        window.document.title = "SPPA FET Web Admin - Login";
        if (PARAM.length > 0) {
            SwalToast("error", PARAM[0]);
            ToastError(PARAM[0]);
        }
        $("#frm_login").on("submit",e => {
            e.preventDefault();
            do_login();
        });
        $id("uid").focus();
    }
    do_login = () => {
        const uid = $id("uid").value;
        const pwd = $id("pwd");
        var cl = getSetting("challange");
        var has = forge_sha256(forge_sha256(pwd.value)+cl);
        var data = new FormData();
        data.append("uid", uid);
        data.append("passwd", has);
        data.append("challange",cl);
        pwd.value = "";
        Loader("Logging in...");
        Api("api/?1/login",{body: data}).then(
            data => {
                LoaderHide();
                setSetting("challange",data.challange);
                if (data.error == 0) {
                    ToastSuccess("Login Success");
                    setSetting("user-data",data.data);
                    setSetting("last-log",data.lastLog);
                    window.location.replace("index.php");
                } else {
                    ToastError("Login", data.message);
                    if (data.error == 300) {
                        oldies = data.data;
                        window.setTimeout(do_expired,900);
                    } else {
                        pwd.focus();
                    }
                }
            },
            error => {
                LoaderHide();
                FError("Login",error);
            }
        );
    }
    do_expired = () => {
        var frmPwd = new Formation();
        frmPwd.setModel({
            pwd0: {caption:"Old Password", type:"string", control:"password"},
            pwd1: {caption:"New Password", type:"string", control:"password"},
            pwd2: {caption:"Confirm New Password", type:"string", control:"password"}
        });

        XFrame.setCaption('Password Expired')
            .setContent(frmPwd.formAdd('exp_user_pass'))
            .setConfirmation('Change Password','Continue?',true,()=>{
                var sdata = frmPwd.readForm(false,true, true);
                var opwd = sdata['pwd0'];
                var npwd = sdata['pwd1'];
                var cpwd = sdata['pwd2'];
                if (npwd != cpwd) {
                    ToastError('New password not confirmed');
                    return false;
                }
                if (npwd == opwd) {
                    ToastError('New password same as old password');
                    return false;
                }
                if (npwd == "") {
                    ToastError('New password empty');
                    return false;
                }
                if (npwd.length < 6) {
                    ToastError('Minimum password length is six chars');
                    return false;
                }
                hopwd = forge_sha256(opwd);
                if (hopwd != oldies.passwd) {
                    ToastError('Wrong old password');
                    return false;
                }
                //fdata['pwd1'] = forge_sha256(npwd);
                //fdata['pwd2'] = forge_sha256(cpwd);
                return true;                    
            })
            .setVerifier(true, ()=>{ return frmPwd.doVerify(); })
            .setAction(true,()=>{
                var fdata = frmPwd.readForm(false,true, true);
                var opwd = fdata['pwd0'];
                var npwd = fdata['pwd1'];
                var cpwd = fdata['pwd2'];
                fdata['pwd0'] = forge_sha256(opwd);
                fdata['pwd1'] = forge_sha256(npwd);
                fdata['pwd2'] = forge_sha256(cpwd);
                //const usr = getSetting("user-data", {});
                fdata['id'] = oldies["id"];
                Loader('Changing user password...');
                //console.log(fdata);
                Api('api/?1/unexpire', {body: JSON.stringify(fdata)}).then(
                    data => {
                        LoaderHide();
                        setSetting("challange",data.challange);
                        if (data.error == 0) {
                            ToastSuccess('Change password success');
                            //btnRefresh_click();
                        } else {
                            FError('Change password failed', data.message);
                        }
                    },
                    error => {
                        LoaderHide();
                        FError('Change password error', error);
                    }
                );
            })
            .show();
    }
</script>
