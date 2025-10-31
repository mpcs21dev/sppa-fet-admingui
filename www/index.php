<?php
/*
spl_autoload_register(function ($class_name) {
    if (file_exists("model/{$class_name}.php")) {
        include_once "model/{$class_name}.php";
    } elseif (file_exists("classes/{$class_name}.php")) {
        include_once "classes/{$class_name}.php";
    }
});
*/
require_once("check.php");
require_once("api/const.php");
$lastId = getVars("last-id",0);
$usrx = getVars("user-data");
?><!DOCTYPE html>
<html>
<head>
    <title>SPPA FET Dashboard</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <script src="lib/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="lib/fomantic-ui/semantic.min.css">
    <script src="lib/fomantic-ui/semantic.min.js"></script>
    <link rel="stylesheet" type="text/css" href="lib/tabulator/css/tabulator_simple.min.css">
    <script src="lib/tabulator/js/tabulator.min.js"></script>
    <link rel="stylesheet" type="text/css" href="loader.css">
    <style type="text/css">
        .ui.dropdown .menu > .item {
            min-height: 0 !important;
        }

        body { cursor: default; }
        #menu-top { margin: 0; }
        #eventBoard,
        #syncBoard,
        #trxBoard { overflow: hidden; }
        .min-padding { padding: 5px !important; }
        .ui.sidebar { overflow: visible !important; }
        .judul { color: yellow !important; font-weight: bold !important; }
        #badane { padding: 0 6px; }
        #lehere,
        #leher { padding: 6px; }

        .fixmargin { margin-top: 0 !important; }
        .m180 { left: -30px !important; }
        .bgviolet { background-color: violet !important; }
        .bggreen { background-color: green !important; }
        .bggrey { border: 1px solid grey; }
        .pointer { cursor: pointer !important; }
        .rounded { border-radius: 5px; padding-left: 5px; padding-right: 5px; }
        .divcen { display: flex; flex-direction: row; align-items: center; align-content: center; }
        .divcencol { display: flex; flex-direction: column; justify-content: center; align-items: flex-start; align-content: center; }
        .flexrow { display: flex; flex-direction: row; }
        .flexrow div {margin: 0 !important;}
        #detail_message,
        #detail_event {min-width: 600px; max-width:600px; padding: 10px; margin: 0 !important; overflow-y: auto; }

        .board {
            display: none;
        }
        .board-active {
            display: block !important;
        }

        .session-grid {
            padding: 10px;
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
            justify-content: flex-start;
            align-items: flex-start;
            align-content: flex-start;
            gap: 2px 10px;
            height: 100%;
            overflow-x: auto;
        }
        .sync_outer_box {
            padding: 10px;
            display: flex;
            flex-direction: row;
            height: -webkit-fill-available;
        }
        .sync_box {
            display: flex;
            flex-direction: column;
            margin: 0 !important;
            padding: 10px;
        }
        .sync_box > div {
            padding: 5px 0;
            display: flex;
            flex-direction: row;
        }
        .sync_box > div > div:first-child {
            flex: 1 0 0;
        }
        .sync_box > div > div {
            min-width: 200px;
        }
        .sync_box .grow {
            flex: 1 0 0;
            overflow: auto;
        }
        .sync_box legend {
            background-color: navy;
            border-radius: 9px;
            color: yellow;
            padding: 3px 10px;
        }
        .finput {
            display: flex;
            flex-direction: row;
            border: 1px solid brown;
            border-radius: 5px;
        }
        .finput > div {
            font-size: .7em;
            padding: 3px 8px;
            background-color: yellow;
            border-radius: 5px 0 0 5px;
        }
        .finput > input {
            border: none;
            width: 150px;
            font-size: .7em;
            padding-left: 3px;
            padding-right: 3px;
            text-align: center;
            border-radius: 0 5px 5px 0;
        }
        .finput > input.donly {
            width: 100px;
        }
        .finput > input:focus {
            outline: none;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="classes.css">
    <link rel="stylesheet" type="text/css" href="framer.css">
    <script src="lib/forge-sha256.min.js"></script>
    <script type="text/javascript" src="lib/cryptojs-aes.min.js"></script>
    <script type="text/javascript" src="lib/cryptojs-aes-format.js"></script>
    <script src="index.js"></script>
    <script src="classes.js"></script>
    <script src="framer.js"></script>
    <script type="text/javascript">
    	loadStorage();
    </script>

    <script src="boardDash.js"></script>
    <script src="boardSync.js"></script>
</head>
<body>
    <div id='menubar' class="ui top inverted attached mini pointing menu">
        <!-- MENU KIRI :: START -->
        <div id='menu-kiri' class="ui inverted blue dropdown item icon">
            <i class="bars icon"></i>
            <div class="menu">
                <div style='display:none;' class="item" id="mnu-participant">Participant Services</div>
                <div style='display:none;' class="item" id="mnu-transaction">Transaction List</div>
                <div style='display:none;' class="item" id="mnu-event">Event List</div>
                <div class="item">
                    <i class="dropdown icon"></i>
                    <span class="text">Settings</span>
                    <div class="menu">
                        <div class="item" id="mnu-config">Config</div>
                        <?= cekLevel(1000)?'<div class="item" id="mnu-holiday">Holiday</div>':'' ?>
                    </div>
                </div> 
                <?php if (cekLevel(2)) { ?>
                <div class="item">
                    <i class="dropdown icon"></i>
                    <span class="text">Administrative</span>
                    <div class="menu">
                        <?= cekLevel(LEVEL_DEV) ? '<div class="item" id="mnu-ref">Reference</div>' : '' ?>
                        <div class="item" id="mnu-user">User</div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <!-- MENU KIRI :: END -->
        <!-- CAPTION -->
        <div class="item judul">SPPA Front End Trading - Admin</div>
        <!-- TASKBAR :: START -->
        <a id='tdash' class="active red item"><i class="tachometer alternate icon"></i> Dashboard</a>
        <a id='ttrx' class="red item"><i class="shopping cart icon"></i> Transaction</a>
        <a id='tevent' class="red item"><i class="bell outline icon"></i> Event</a>
        <!-- TASKBAR :: END -->
        <!-- MENU KANAN :: START -->
        <div class="right menu">
            <a id='tswitch' class='item bggreen'><i class='server icon'></i> <span id='connect-to'>Server []</span></a>
            <a id='tsync' class="red item" <?= cekLevel(LEVEL_DEV) ? "" : "style=\"visibility:hidden;\"" ?>><i id="icon_sync" class="sync icon"></i> Sync</a>
            <a id='tinfo' class='item'><i id='dbicon' class='database icon'></i> <span id='devshm'></span></a>
            <div id='menu-kanan' class="ui blue inverted dropdown item">
                <i class="user icon"></i>
                <?=$usrx["uid"]?> &bull; <?=$usrx["user_name"]?> 
                <div class="menu">
                    <a class="item" id="mnu-chpwd"><i class="key icon"></i> Change Password</a>
                    <a class="item" id="mnu-logout"><i class="sign out alternate icon"></i> Sign Out</a>
                </div>
            </div>
        </div>
        <!-- MENU KANAN :: END -->
    </div>
    <div id="dashBoard"></div>
    <div id="syncBoard" class="board"></div>
    <?php require_once("boardTrx.php"); ?>
    <?php require_once("boardEvent.php"); ?>
    <?php //require_once("boardSync.php"); ?>
    <div id="frame_" class="ui longer coupled modal">
        <div id="frame_header" class="header">Header</div>
        <div id="frame_content" class="scrolling content">
            <p>Very long content goes here</p>
        </div>
        <div id="frame_action" class="actions">
            <div class="ui approve button green">Save</div>
            <div class="ui cancel button">Cancel</div>
        </div>
    </div>
    <div id="confirm_" class="ui mini inverted coupled modal">
        <div id="confirm_header" class="header">Header</div>
        <div id="confirm_content" class="content">Content</div>
        <div id="confirm_action" class="actions">
            <div class="ui approve button green">OK</div>
            <div class="ui cancel button">Cancel</div>
        </div>
    </div>
    <script type="text/javascript">
        SyncInterval = 6000;
        XFrame = new Framer("frame_");
        //$(".coupled.modal").modal({centered: false, allowMultiple: true, closable: false});
        $.fn.modal.settings.templates.myConfirm = function(caption, body, fnOk = null, fnClose = null) {
            // do something according to modals settings and/or given parameters
            //var settings = this.get.settings(); // "this" is the modal instance
            return {
                allowMultiple: true,
                title: caption,
                content: body,
                class: 'inverted',
                actions: [{
                    text    : 'Yes',
                    class   : 'green',
                    icon    : 'check', 
                    click   : fnOk
                },{
                    text    : 'No',
                    icon    : 'times',
                    click   : fnClose
                }]
            }
        }
        $.fn.modal.settings.templates.myError = function(caption, body) {
            // do something according to modals settings and/or given parameters
            //var settings = this.get.settings(); // "this" is the modal instance
            return {
                allowMultiple: true,
                title: caption,
                content: body,
                class: 'inverted',
                actions: [{
                    text    : 'Close',
                    class   : 'red',
                    icon    : 'times'
                }]
            }
        }

        $('#menu-kiri').dropdown();
        $('#menu-kanan').dropdown();

        function assignFr(name,caption,url,vfill=true,vgutt=35) {
            let o = new Framr(name,caption,url);
            o.set("fill",vfill).set("gutter",vgutt).render();
            //$(".dropdown").dropdown('clear');
            return o;
        }

        //$("#mnu-participant").on("click", ()=>{assignFr("mprt","Participant Services","loader.php?p=participant");});
        //$("#mnu-transaction").on("click", ()=>{assignFr("mtrx","Transaction List","loader.php?p=transaction");});
        //$("#mnu-event").on("click", ()=>{assignFr("meve","Event List","loader.php?p=event");});

        if ($id("mnu-config")) $("#mnu-config").on("click", ()=>{assignFr("mcfg","Configuration","loader.php?p=config");});
        //$("#mnu-holiday").on("click", ()=>{assignFr("mday","Holiday","loader.php?p=holiday");});

        if ($id("mnu-ref")) $("#mnu-ref").on("click", ()=>{assignFr("mref","Reference","loader.php?p=ref");});
        if ($id("mnu-user")) $("#mnu-user").on("click", ()=>{assignFr("musr","User","loader.php?p=cmsuser");});
        $("#mnu-chpwd").on("click", ()=>{
            var frmPwd = new Formation();
            frmPwd.setModel({
                pwd0: {caption:"Old Password", type:"string", control:"password"},
                pwd1: {caption:"New Password", type:"string", control:"password"},
                pwd2: {caption:"Confirm New Password", type:"string", control:"password"}
            });

            XFrame.setCaption('Change Password')
                .setContent(frmPwd.formAdd('chg_user_pass'))
                .setConfirmation("Change Password")
                .setVerifier(true, ()=>{ return frmPwd.doVerify(); })
                .setAction(true,()=>{
                    var fdata = frmPwd.readForm(false,true, true);
                    var opwd = fdata['pwd0'];
                    var npwd = fdata['pwd1'];
                    var cpwd = fdata['pwd2'];
                    if (npwd != cpwd) {
                        ToastError('New password not confirmed');
                        return;
                    }
                    if (npwd == opwd) {
                        ToastError('New password same as old password');
                        return;
                    }
                    if (npwd == "") {
                        ToastError('New password empty');
                        return;
                    }
                    if (npwd.length < 6) {
                        ToastError('Minimum password length is six chars');
                        return;
                    }
                    fdata['pwd0'] = forge_sha256(opwd);
                    fdata['pwd1'] = forge_sha256(npwd);
                    fdata['pwd2'] = forge_sha256(cpwd);
                    const usr = getSetting("user-data", {});
                    fdata['id'] = usr["id"];
                    Loader('Changing user password...');
                    //console.log(fdata);
                    Api('api/?1/passwd', {body: JSON.stringify(fdata)}).then(
                        data => {
                            LoaderHide();
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
        });

        $("#mnu-logout").on("click", ()=>{
            if (confirm("Logout ?")) {
                Api("api/?1/logout").then(
                    data => {
                        burnSetting();
                        window.location.replace("loader.php?p=login");
                    },
                    error => {
                        FError("Logout", error);
                    }
                );
            }
        });

        Task = {
            Active: "",
            Map: {
                tdash: "dashBoard",
                ttrx: "trxBoard",
                tevent: "eventBoard",
                tsync: "syncBoard"
            },
            Show: function(id,cmd=false) {
                if (id != this.Active) {
                    if (this.Active != "") $id(this.Active).classList.remove('active');
                    if (this.Active != "") $id(this.Map[this.Active]).style.display = "none";  //.classList.remove('board-active');
                    this.Active = id;
                    $id(this.Active).classList.add('active');
                    $id(this.Map[this.Active]).style.display = "block";  //.classList.add('board-active');
                    $id(this.Map[this.Active]).style.height = (window.innerHeight-$id("menubar").offsetHeight)+"px";
                    if (id == 'tsync') $id('icon_sync').className = 'sync icon';
                    if (id == "tdash") Dash.startup();
                    if (id == "ttrx") Trx.startup();
                    if (id == "tevent") XEvent.startup();
                    if (cmd) setTimeout(()=>{cmd()}, 500);
                }
                //console.log(this.Active);
            }
        };

        $("#tdash").on("click", ()=>{Task.Show('tdash')});
        $("#ttrx").on("click", ()=>{Task.Show('ttrx')});
        $("#tevent").on("click", ()=>{Task.Show('tevent')});
        $("#tsync").on("click", ()=>{Task.Show('tsync')});

        function doSwitch(md) {
            Api('api/?1/switch/'+md).then(
                data => {
                    LoaderHide();
                    if (data.error == 0) {
                        ToastSuccess('Switching command sent');
                        $id('connect-to').innerHTML = 'Server ['+data.connectTo+']';
                } else {
                        FError('Failed', data.message);
                    }
                },
                error => {
                    LoaderHide();
                    FError('Error', error);
                }
            );
        }
        $("#tswitch").on("click", ()=>{
            if (confirm("Switch FIX Server?")) {
                Loader("Switching FIX Server...");
                Api('api/?1/config/ref/fix').then(
                    data => {
                        //LoaderHide();
                        if (data.error == 0) {
                            try {
                                var p = data.data[0]["xval"];
                                setTimeout(()=>{doSwitch(p)}, 300);
                            } catch (e) {
                                LoaderHide();
                                FError('Error',e);
                            }
                        } else {
                            FError('Failed', data.message);
                        }
                    },
                    error => {
                        LoaderHide();
                        FError('Error', error);
                    }
                );
            };
        });

        $(()=>{
            Ref = new Refs();
            Ref.load('FixCon', 'api/?1/config/ref/fix', ()=>{
                var z = Ref.find('FixCon','xkey','server','xval');
                $id('connect-to').innerHTML = 'Server ['+z+']';
            });
            //Ref.load('Legends', 'api/?1/config/ref/legends');
            //Ref.load('LogType', 'api/?1/config/ref/log-type');
            //Ref.load('AppType', 'api/?1/config/ref/app-type');

            // Dashboard Init;
            Dash.startup();
            //Trx.startup();
            //XEvent.startup();
            Task.Show("tdash");

            TSync.startup();
            TSync.doit(<?=$lastId?>);
            let udata = getSetting("user-data");
            if (udata) {
            	if (udata.chpwd != 0) {
            		$("#mnu-chpwd").click();
            	}
            }            
        });
    </script>
    <!-- 
    <?php print_r($HX); ?> 
    -->
</body>
</html>
