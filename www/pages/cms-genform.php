<?php
$DPRM = array(
    "icon" => "thumbs up outline",
    "caption" => "Form Caption",
    "color" => "orange",
    "accordionID" => "",
    "menuID" => "menu-",
    "model" => "",
    "apiList" => "",
    "apiCreate" => "",
    "apiUpdate" => "",
    "apiDelete" => "",
    "labelField" => "", // field for deleting caption
    "extraButtons" => "",
    "jsStartup" => "",
    "extraJS" => "",
    "addAfterShow" => "",
    "editAfterShow" => "",
    "uppercase" => true,
    "fnRefresh" => "btnRefresh_click",
    "fnView" => "btnView_click",
    "fnAdd" => "btnAdd_click",
    "fnEdit" => "btnEdit_click",
    "fnDelete" => "btnDelete_click",
    "tabulatorOption" => "",
    "idField" => "id",
    "add_readform_isedit" => "false",
    "add_readform_getro" => "false",
    "edt_readform_isedit" => "true",
    "edt_readform_getro" => "false"
);
// $PRM
$PPRM = array();
foreach ($DPRM as $key=>$val) $PPRM[$key] = array_key_exists($key, $PRM) ? $PRM[$key] : $val;
?><style type="text/css">
    .fixmargin { margin-top: 0 !important; }
    .m180 { left: -30px !important; }
    .bgviolet { background-color: violet; }
    .bggrey { border: 1px solid grey; }
    .pointer { cursor: pointer !important; }
    .rounded { border-radius: 5px; padding-left: 5px; padding-right: 5px; }
    .divcen { display: flex; flex-direction: row; align-items: center; align-content: center; }
    .divcencol { display: flex; flex-direction: column; justify-content: center; align-items: flex-start; align-content: center; }
</style>
<!--
<div id="kepala" class="ui top attached segment fixmargin">
    <div class="ui header"><h1><i class="<?=$PPRM['icon']?> icon"></i> <?=$PPRM["caption"]?></h1></div>
</div>
-->
<div id="leher" class="ui attached menu padder">
    <div class="ui buttons">
        <button class="ui mini violet icon button" id="btnRefresh" data-tooltip="Refresh" data-position="bottom left"><i class="redo icon"></i></button>
        <div class="ui mini violet dropdown icon button">
            <i class="dropdown icon"></i>
            <div class="ui vertical menu m180">
                <div id="btnmRefresh" class="mini item"><i class="redo icon"></i> Refresh Data</div>
                <div id="btnmReset" class="mini item"><i class="cog icon"></i> Reset Column Settings</div>
            </div>
        </div>
    </div>
    <div class="padder2"></div>
    <div class="ui buttons">
        <button class="ui mini blue button" id="btnView"><i class="eye outline icon"></i> View</button>
        <button class="ui mini blue button" id="btnAdd"><i class="plus circle icon"></i> Add</button>
        <button class="ui mini blue button" id="btnEdit"><i class="edit outline icon"></i> Edit</button>
        <button class="ui mini red button"  id="btnDelete"><i class="minus circle icon"></i> Delete</button>
    </div>
    <?=$PPRM["extraButtons"]?>
</div>
<div id="badan" class="attached standard-full">
    <div id="table" class="ui <?=$PPRM['color']?> table"></div>
</div>
<script type="text/javascript">
    var Table;
    var AccordionMenuID = "<?=$PPRM['accordionID']?>";
    var MenuID = "<?=$PPRM['menuID']?>";
    var frmPage = new Formation(<?=$PPRM["uppercase"]?>);
    frmPage.setModel(<?=$PPRM["model"]?>);
    mod_startup = () => {
        // buttons event listener
        $("#btnRefresh").on("click", <?=$PPRM["fnRefresh"]?>);
        $("#btnmRefresh").on("click", <?=$PPRM["fnRefresh"]?>);
        $("#btnmReset").on("click", btnResetColumn_click);
        $("#btnView").on("click", <?=$PPRM["fnView"]?>);
        $("#btnAdd").on("click", <?=$PPRM["fnAdd"]?>);
        $("#btnEdit").on("click", <?=$PPRM["fnEdit"]?>);
        $("#btnDelete").on("click", <?=$PPRM["fnDelete"]?>);

        var w0 = Math.ceil(window.innerHeight),
            w1 = 0; //Math.ceil($("#kepala").outerHeight()),
            w2 = Math.ceil($("#leher").outerHeight()),
            w3 = parseInt($("#badan").css("margin"));

        //console.log([w0,w1,w2,w3]);

        Table = frmPage.xTabulator("table", w0-w1-w2-w3-w3, "cms_table_<?=$PPRM['menuID']?>", "<?=$PPRM['apiList']?>", {<?=$PPRM['tabulatorOption']?>});

        // Ref.load("IPOStatus", "api/?1/domain-list/1");
        <?=$PPRM["jsStartup"]?>
        $('.dropdown').dropdown();
    }
    btnResetColumn_click = () => {
        $("body").modal("myConfirm", "<i class='exclamation triangle icon red'></i> Reset Column", "Reset data grids column settings ?", ()=>{
            localStorage.removeItem("tabulator-cms_table_<?=$PPRM['menuID']?>-columns");
            window.location.reload();
        });
    }
    btnRefresh_click = () => {
        var cp = Table.getPage();
        Table.setPage(cp);
    }
    btnView_click = () => {
        var sel = (Table.getSelectedData())[0]; // get first selected element
        if (sel == undefined) {
            ToastError("No row selected");
            return;
        }
        var parm = {<?=$PPRM['idField']?>: sel.<?=$PPRM['idField']?>};
        XFrame.setCaption("<?=$PPRM['caption']?>").setContent(frmPage.viewCard(parm)).setAction(false).show(false);
    }
    btnAdd_click = () => {
        XFrame.setCaption("Add Record")
            .setContent(frmPage.formAdd("add_rec",2))
            .setConfirmation()
            .setVerifier(true, ()=>{ return frmPage.doVerify(); })
            .setAction(true,()=>{
                var fdata = frmPage.readForm(<?=$PPRM['add_readform_isedit']?>,<?=$PPRM['add_readform_getro']?>);
                /*
                if (checkAdd) {
                    const cea = checkAdd(fdata);
                    if (!cea[0]) {
                        ToastError(cea[1]);
                        return;
                    }
                }
                */
                Loader("Saving new record...");
                //console.log(fdata);
                Api("<?=$PPRM['apiCreate']?>", {body: fdata}).then(
                    data => {
                        LoaderHide();
                        if (data.error == 0) {
                            ToastSuccess("New record saved");
                            btnRefresh_click();
                        } else {
                            FError("Saving record failed", data.message);
                        }
                    },
                    error => {
                        LoaderHide();
                        FError("Saving record error", error);
                    }
                );
            })
            .setAfterShow(()=>{
                $('.dropdown').dropdown();
                <?=$PPRM["addAfterShow"]?>
            })
            .show();
    }
    btnEdit_click = () => {
        var sel = (Table.getSelectedData())[0]; // get first selected element
        if (sel == undefined) {
            ToastError("No row selected");
            return;
        }

        var parm = {<?=$PPRM['idField']?>: sel.<?=$PPRM['idField']?>};
        XFrame.setCaption("Edit Record")
            .setContent(frmPage.formEdit(parm, "edt_rec", 2))
            .setConfirmation()
            .setVerifier(true, ()=>{ return frmPage.doVerify(); })
            .setAction(true,()=>{
                var fdata = frmPage.readForm(<?=$PPRM['edt_readform_isedit']?>,<?=$PPRM['edt_readform_getro']?>);
                /*
                if (checkEdit) {
                    const cea = checkEdit(fdata);
                    if (!cea[0]) {
                        ToastError(cea[1]);
                        return;
                    }
                }
                */
                Loader("Updating record...");
                Api("<?=$PPRM['apiUpdate']?>", {body: fdata}).then(
                    data => {
                        LoaderHide();
                        if (data.error == 0) {
                            ToastSuccess("Record updated");
                            btnRefresh_click();
                        } else {
                            FError("Update record failed", data.message);
                        }
                    },
                    error => {
                        LoaderHide();
                        FError("Update record error", error);
                    }
                );
            })
            .setAfterShow(()=>{
                $('.dropdown').dropdown();
                <?=$PPRM["editAfterShow"]?>
            })
            .show();
    }
    btnDelete_click = () => {
        var prm = {
            check_field: (sel)=>{return true},
            title: "Delete Record",
            get_caption: (sel)=>{return "Delete ["+sel.<?=$PPRM['labelField']?>+"] ?"},
            msg_loader: "Deleting record...",
            api: "<?=$PPRM['apiDelete']?>",
            msg_success: "Record deleted",
            msg_failed: "Delete record failed",
            msg_error: "Delete record error"
        };

        btnAction_click(prm);
    }
    /*
    btnDeleteOriginal_click = () => {
        var sel = (Table.getSelectedData())[0]; // get first selected element
        if (sel == undefined) {
            ToastError("No row selected");
            return;
        }

        $("body").modal("myConfirm", "<i class='exclamation triangle icon red'></i> Delete Record", "Delete ["+sel.<?=$PPRM['labelField']?>+"] ?", ()=>{
            Loader("Deleting record...");
            var fdata = frmPage.formDataTabRow(sel);
            Api("<?//$PPRM['apiDelete']?>", {body: fdata}).then(
                data => {
                    LoaderHide();
                    if (data.error == 0) {
                        ToastSuccess("Record deleted");
                        btnRefresh_click();
                    } else {
                        FError("Delete record failed", data.message);
                    }
                },
                error => {
                    LoaderHide();
                    FError("Delete record error", error);
                }
            );
        });
    }
    */

    btnAction_click = (prm) => {
        var sel = (Table.getSelectedData())[0]; // get first selected element
        if (sel == undefined) {
            ToastError('No row selected');
            return;
        }
        if (prm.hasOwnProperty("check_field")) {
            var ret = prm.check_field(sel);
            if (!ret) return;
        }
        if (!prm.hasOwnProperty("icon")) {
            prm.icon = "exclamation triangle icon red";
        }

        $('body').modal('myConfirm', `<i class='${prm.icon}'></i> ${prm.title}`, prm.get_caption(sel), ()=>{
            Loader(prm.msg_loader);
            var fdata = frmPage.formDataTabRow(sel);
            Api(prm.api, {body: fdata}).then(
                data => {
                    LoaderHide();
                    if (data.error == 0) {
                        ToastSuccess(prm.msg_success);
                        btnRefresh_click();
                    } else {
                        FError(prm.msg_failed, data.message);
                    }
                },
                error => {
                    LoaderHide();
                    FError(prm.msg_error, error);
                }
            );
        });
    }
    <?= $PPRM["extraJS"] ?>
</script>
