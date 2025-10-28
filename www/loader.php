<?php
require_once "check.php";
header_remove("X-Powered-By");
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1024, initial-scale=1"/>
    <script src="lib/forge-sha256.min.js"></script>
    <script src="lib/jquery-3.7.1.min.js"></script>
    <script src="lib/sweetalert2.all.min.js"></script>
    <link href="lib/tabulator/css/tabulator_simple.min.css" rel="stylesheet">
    <script src="lib/tabulator/js/tabulator.js"></script>
    <link href="lib/fomantic-ui/semantic.min.css" rel="stylesheet" />
    <script src="lib/fomantic-ui/semantic.min.js"></script>
    <link href="loader.css" rel="stylesheet" />
    <style type="text/css">
        body { 
            padding: 0; 
            margin: 0; 
            height: auto !important;
        }
        .active.item {
            background-color: rgb(52,16,201) !important;
            color: white !important;
        }
        .content.active a:last-child {
            border-bottom: none !important;
        }
        .item {
            background-color: rgb(46,6,111) !important;
            color: rgba(255,255,255,.8) !important;
            border-bottom: 1px solid rgb(67,9,162) !important;
        }
        .item:not(.ui):not(.active):hover {
            background-color: rgb(67,9,162) !important;
            /*color: lime !important;*/
        }
        .title {
            color: rgba(255,255,255,.8) !important;
        }
    </style>
    <script type="text/javascript" src="index.js"></script>
    <script type="text/javascript" src="classes.js"></script>
    <script type="text/javascript">
        <?php
            echo "PARAM = [$JS_PARAM];\n";
        ?>
        loadStorage();
        Api("api/?1/getChallange").then(
            data => {
                if (data.error == 0) {
                    var ccl = getSetting("challange");
                    setSetting("challange", data.challange);

                    var sts = (data.challange == ccl);
                    if (!sts) {
                        console.log("SESSION CHALLANGE IS DIFFERENT");
                    }
                } else {
                    //SwalToast('error','Error Get Challange');
                    ToastError("Error getting challange");
                    console.log(data);
                }
            },
            error => {
                //SwalToast('error','Get Challange: Server Error');
                ToastError("Get Challange: Server Error");
                console.log({status: "Get Challange: Server Error", detail: error});
            }
        );
    </script>
</head>
<body>
    <div class="pusher">
    <?php
    $pages = array(
        "cmsuser" => 2,
        "config" => 1,
        "holiday" => 9999,
        "ref" => 2
    );
    function cekPage($ix) {
        global $pages;
        if (isset($pages[$ix])) {
            return cekLevel($pages[$ix]);
        } else {
            return true;
        }
    }
    if ($PATH != "") {
        if (!cekPage($PATH)) {
            include_once("static/403.html");
        } else {
            if (file_exists("pages/cms-".$PATH.".php")) {
                include_once("pages/cms-".$PATH.".php");
            } else {
                include_once("static/404.html");
            }
        }
    }
    ?>
    </div>
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
</body>
<script type="text/javascript">
    var Ref;
    $(()=>{
        Ref = new Refs();
        //$('.ui.accordion').accordion({animateChildren: false});
        //$('.ui.sidebar').sidebar({closable: false});

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
        try {
            if (mod_startup) mod_startup();
        } catch (e) {
            // do nothing;
        }
    });
</script>
</html>