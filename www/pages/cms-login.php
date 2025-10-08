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
                if (data.error == 0) {
                    setSetting("user-data",data.data);
                    setSetting("challange",data.challange);
                    setSetting("last-log",data.lastLog);
                    window.location.replace("index.php");
                } else {
                    ToastError("Login", data.message);
                    setSetting("challange",data.challange);
                    pwd.focus();
                }
            },
            error => {
                LoaderHide();
                FError("Login",error);
            }
        );
    }
</script>