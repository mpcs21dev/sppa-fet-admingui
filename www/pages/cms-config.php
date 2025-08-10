<?php
$Model = '{
    id:             {caption:"ID", type:"numeric", formatting: "rownum", autoValue:true, frozen:true},
    participant_id: {title:"Participant ID",caption:"Participant ID<br><span class=\"navy\">(Only letters, numbers and underscore are allowed)</span>", type:"string", placeholder:"Participant ID", readOnly: true, noedit:true, headerFilter:true},
    participant_name:{caption:"Name", type:"string", headerFilter:true},
    record_type:    {caption:"Record Type", type:"string", readOnly: true, noedit:true, noview:true, visible: false, control:"lookup", option:{table:"PartRecType",id:"xkey",text:"xval"}},
    rec_type_str:   {caption:"Record Type", type:"string", readOnly: true, noadd:true, headerFilter:true},
    data:           {caption:"Data", type:"string", visible:false, noadd:true, noedit:true, useTag:true, tagFormat:"json"},
    inserted_at:    {caption: "Created", type: "datetime", autoValue: true, formatter: datetimeFormatter},
    updated_at:     {caption: "Updated", type: "datetime", autoValue: true, formatter: datetimeFormatter}
}';

$Model2 = '{
    id:          {caption:"ID", type:"numeric", formatting: "rownum", autoValue:true, frozen:true},
    rowid:       {caption:"", type:"numeric", noadd:true, noedit:true, noview:true, visible:false},
    clientId:    {caption:"Client ID", type:"string", headerFilter:true},
    clientName:  {caption:"Client Name", type:"string",upperCase:true, headerFilter:true},
    fixFirmId:   {caption:"Fix Firm ID", type:"string",upperCase:true, headerFilter:true},
    fixSourceId: {caption:"Fix Source ID", type:"string", defaultValue:"D", upperCase:true},
    fixMainUrl:  {caption:"Fix Main Url", type:"string", noedit:true, noadd:true},
    fixDrcUrl:   {caption:"Fix DRC Url", type:"string", noedit:true, noadd:true},
    fixMainUrl_user: {caption:"<hr><span style='."'".'color: navy; font-family: serif; font-style:italic; font-size:1.3em;'."'".'>Fix Main URL</span><br>Fix User", type:"string", placeholder:"Fix User", noview:true, visible:false},
    fixMainUrl_pass: {caption:"Fix Password ", type:"string", noview:true, visible:false},
    fixMainUrl_ip:   {caption:"Fix Server IP", type:"string", noview:true, visible:false},
    fixMainUrl_port: {caption:"Fix Port", type:"string", noview:true, visible:false},
    fixMainUrl_sender:{caption:"Sender Comp ID", type:"string", noview:true, visible:false},
    fixMainUrl_target:{caption:"Target Comp Id", type:"string", noview:true, visible:false, defaultValue:"AXECHANGE"},
    fixDrcUrl_user:  {caption:"<hr><span style='."'".'color: navy; font-family: serif; font-style:italic; font-size:1.3em;'."'".'>Fix DRC URL</span><br>Fix DRC User", type:"string", placeholder:"Fix DRC User", noview:true, visible:false},
    fixDrcUrl_pass:  {caption:"Fix DRC Password", type:"string", noview:true, visible:false},
    fixDrcUrl_ip:    {caption:"Fix DRC Server IP", type:"string", noview:true, visible:false},
    fixDrcUrl_port:  {caption:"Fix DRC Port", type:"string", noview:true, visible:false},
    fixDrcUrl_sender:{caption:"DRC Sender Comp ID", type:"string", noview:true, visible:false},
    fixDrcUrl_target:{caption:"DRC Target Comp ID", type:"string", noview:true, visible:false, defaultValue:"AXECHANGE"}
}';

$Model3 = '{
    id:      {caption:"ID", type:"numeric", formatting: "rownum", autoValue:true, frozen:true},
    rowid:   {caption:"", type:"numeric", noadd:true, noedit:true, noview:true, visible:false},
    ftpUrl:  {caption:"FTP Url", type:"string"}
}';

$PRM = array(
    "icon" => "file invoice dollar",
    "caption" => "Config",
    "accordionID" => "",
    "menuID" => "menu-config",
    "apiList" => "api/?1/config/config/list",
    "apiCreate" => "api/?1/config/config/create",
    "apiUpdate" => "api/?1/config/config/update",
    "apiDelete" => "api/?1/config/config/delete",
    "labelField" => "participant_id",
    "extraButtons" => "",
    "jsStartup" => "",
    "addAfterShow" => "",
    "editAfterShow" => "",
    "model" => $Model,
    "fnAdd" => "btnAdd2_click",
    "fnEdit" => "btnEdit2_click",
    "extraJS" => "
        btnAdd2_click = () => {
            XFrame.setCaption('Add Record')
                .setContent(frmPage.formAdd('add_rec',2))
                .setConfirmation()
                .setVerifier(true, ()=>{ return frmPage.doVerify(); })
                .setAction(true,()=>{
                    var fdata = frmPage.readForm(false,true);
                    Loader('Saving new record...');
                    //console.log(fdata);
                    Api('api/?1/config/config/create', {body: fdata}).then(
                        data => {
                            LoaderHide();
                            if (data.error == 0) {
                                ToastSuccess('New record saved');
                                btnRefresh_click();
                                window.parent.refreshDashboard();
                            } else {
                                FError('Saving record failed', data.message);
                            }
                        },
                        error => {
                            LoaderHide();
                            FError('Saving record error', error);
                        }
                    );
                })
                .setAfterShow(()=>{
                    $('.dropdown').dropdown();
                })
                .show();
        }
        btnEdit2_click = () => {
            var sel = (Table.getSelectedData())[0]; // get first selected element
            if (sel == undefined) {
                ToastError('No row selected');
                return;
            }

            var parm = {id: sel.id};
            XFrame.setCaption('Edit Record')
                .setContent(frmPage.formEdit(parm, 'edt_rec', 2))
                .setConfirmation()
                .setVerifier(true, ()=>{ return frmPage.doVerify(); })
                .setAction(true,()=>{
                    var fdata = frmPage.readForm(true,true);
                    Loader('Updating record...');
                    Api('api/?1/config/config/update', {body: fdata}).then(
                        data => {
                            LoaderHide();
                            if (data.error == 0) {
                                ToastSuccess('Record updated');
                                btnRefresh_click();
                                window.parent.refreshDashboard();
                            } else {
                                FError('Update record failed', data.message);
                            }
                        },
                        error => {
                            LoaderHide();
                            FError('Update record error', error);
                        }
                    );
                })
                .setAfterShow(()=>{
                    $('.dropdown').dropdown();
                })
                .show();
        }
    ",
    "add_readform_isedit" => "false",
    "add_readform_getro" => "true",
    "tabulatorOption" => "
        ajaxCallback : (url, params, response) => {
            var resp = response;
            if (resp.data != null) {
                if (Array.isArray(resp.data)) {
                    for (var i=0; i<resp.data.length; i++) {
                        var d1 = JSON.parse(resp.data[i].data);
                        resp.data[i].bbId = d1.bbId;
                        resp.data[i].firmId = d1.firmId;
                        resp.data[i].sourceId = d1.sourceId;
                    }
                }
            }
            return resp;
        }
    "
);

$PRM = array(
    "caption" => "Config",
    "accordionID" => "",
    "menuID" => "menu-config",

    "leftModel" => $Model,
    "leftColor" => "orange",
    "leftLabel" => "Config",
    "leftApiList" => "api/?1/config/config/list",
    "leftButtons" => "
        <button id='btnLRefresh' class='ui mini violet icon button fixedvert'><i class='redo large icon'></i><br><span>Refresh</span></button>
        <button id='btnLView' class='ui mini blue icon button fixedvert'><i class='eye outline large icon'></i><br><span>View</span></button>
        <button id='btnLAdd' class='ui mini blue icon button fixedvert'><i class='plus circle large icon'></i><br><span>Add</span></button>
        <button id='btnLEdit' class='ui mini blue icon button fixedvert'><i class='edit outline large icon'></i><br><span>Edit</span></button>
        <button id='btnLDelete' class='ui mini red icon button fixedvert'><i class='minus circle large icon'></i><br><span>Delete</span></button>",
    "leftUppercase" => true,

    "rightModel" => $Model2,
    "rightColor" => "green",
    "rightLabel" => "Data Editor",
    "rightApiList" => "",
    "rightButtons" => "
        <button id='btnRRefresh' class='ui mini violet icon button fixedvert'><i class='redo large icon'></i><br><span>Refresh</span></button>
        <button id='btnRView' class='ui mini blue icon button fixedvert'><i class='eye outline large icon'></i><br><span>View</span></button>
        <button id='btnRAdd' class='ui mini blue icon button fixedvert'><i class='plus circle large icon'></i><br><span>Add</span></button>
        <button id='btnREdit' class='ui mini blue icon button fixedvert'><i class='edit outline large icon'></i><br><span>Edit</span></button>
        <button id='btnRDelete' class='ui mini red icon button fixedvert'><i class='minus circle large icon'></i><br><span>Delete</span></button>",
    "rightUppercase" => false,

    "labelField" => "", // field for deleting caption
    "jsStartup" => "
        \$('#btnLRefresh').on('click', btnLRefresh_click);
        \$('#btnLView').on('click', btnLView_click);
        \$('#btnLAdd').on('click', btnLAdd_click);
        \$('#btnLEdit').on('click', btnLEdit_click);
        \$('#btnLDelete').on('click', btnLDelete_click);

        \$('#btnRRefresh').on('click', btnRRefresh_click);
        \$('#btnRView').on('click', btnRView_click);
        \$('#btnRAdd').on('click', btnRAdd_click);
        \$('#btnREdit').on('click', btnREdit_click);
        \$('#btnRDelete').on('click', btnRDelete_click);

        Tabll.on('rowClick', function(e, row){
            //e - the click event object
            //row - row component
            //console.log(row.getData());
            leftData = row.getData();
            leftID = leftData.id;
            btnRRefresh_click();
        });

        Ref.load('PartRecType', 'api/?1/config/ref/rec-type');
    ",
    "extraJS" => "
        function btnLRefresh_click() {
            var cp = Tabll.getPage();
            Tabll.setPage(cp);
            leftData = {};
            leftID = 0;
        }
        function btnLView_click() {
            var sel = (Tabll.getSelectedData())[0]; // get first selected element
            if (sel == undefined) {
                ToastError('No row selected');
                return;
            }
            var parm = {id: sel.id};
            XFrame.setCaption('Participant').setContent(frmLeft.viewCard(parm)).setAction(false).show(false);
        }
        function btnLAdd_click() {
            XFrame.setCaption('Add Participant')
                .setContent(frmLeft.formAdd('add_rec',2))
                .setConfirmation()
                .setVerifier(true, ()=>{ return frmLeft.doVerify(); })
                .setAction(true,()=>{
                    var fdata = frmLeft.readForm(false,true);
                    Loader('Saving new record...');
                    //console.log(fdata);
                    Api('api/?1/config/config/create', {body: fdata}).then(
                        data => {
                            LoaderHide();
                            if (data.error == 0) {
                                ToastSuccess('New record saved');
                                btnLRefresh_click();
                            } else {
                                FError('Saving record failed', data.message);
                            }
                        },
                        error => {
                            LoaderHide();
                            FError('Saving record error', error);
                        }
                    );
                })
                .setAfterShow(()=>{
                    $('.dropdown').dropdown();
                })
                .show();
        }
        function btnLEdit_click() {
            var sel = (Tabll.getSelectedData())[0]; // get first selected element
            if (sel == undefined) {
                ToastError('No row selected');
                return;
            }

            var parm = {id: sel.id};
            XFrame.setCaption('Edit Record')
                .setContent(frmLeft.formEdit(parm, 'edt_rec', 2))
                .setConfirmation()
                .setVerifier(true, ()=>{ return frmLeft.doVerify(); })
                .setAction(true,()=>{
                    var fdata = frmLeft.readForm(true,true);
                    Loader('Updating record...');
                    Api('api/?1/config/config/update', {body: fdata}).then(
                        data => {
                            LoaderHide();
                            if (data.error == 0) {
                                ToastSuccess('Record updated');
                                btnLRefresh_click();
                            } else {
                                FError('Update record failed', data.message);
                            }
                        },
                        error => {
                            LoaderHide();
                            FError('Update record error', error);
                        }
                    );
                })
                .setAfterShow(()=>{
                    $('.dropdown').dropdown();
                })
                .show();
        }
        function btnLDelete_click() {
            var sel = (Tabll.getSelectedData())[0]; // get first selected element
            if (sel == undefined) {
                ToastError('No row selected');
                return;
            }

            $('body').modal('myConfirm', \"<i class='exclamation triangle icon red'></i> Delete Record\", \"Delete [\"+sel.participant_id+\"] ?\", ()=>{
                Loader('Deleting record...');
                var fdata = frmLeft.formDataTabRow(sel);
                Api('api/?1/config/config/delete', {body: fdata}).then(
                    data => {
                        LoaderHide();
                        if (data.error == 0) {
                            ToastSuccess('Record deleted');
                            btnLRefresh_click();
                        } else {
                            FError('Delete record failed', data.message);
                        }
                    },
                    error => {
                        LoaderHide();
                        FError('Delete record error', error);
                    }
                );
            });
        }

        function btnRRefresh_click() {
            var xel = (Tabll.getSelectedData())[0]; // get first selected element
            if (xel == undefined) {
                ToastError('No row selected');
                return;
            } else {
                leftID = xel.id;
                leftData = xel;
            }
            const tipe = leftData.record_type;
            if (tipe == 'PART') {
                frmRight.setModel(".$Model2.");
                \$id('btnRAdd').style.display = 'block';
                \$id('btnRDelete').style.display = 'block';
            } else {
                frmRight.setModel(".$Model3.");
                \$id('btnRAdd').style.display = 'none';
                \$id('btnRDelete').style.display = 'none';
            }
            var rows = [];
            if (tipe == 'PART') {
                var obj;
                try {
                    obj = JSON.parse(leftData.data);
                    if (Array.isArray(obj)) {
                        for (var i=0; i<obj.length; i++) {
                            var o = JSON.parse(JSON.stringify(obj[i]));
                            o['id'] = 1+i;
                            o['rowid'] = leftID;
                            rows.push(o);
                        }
                    } else {
                        rows = [];
                    }
                } catch(err) {
                    rows = [];
                }
                Tablr.setData(rows);
            } else {
                var obj;
                try {
                    obj = JSON.parse(leftData.data);
                    obj['id'] = 1;
                    obj['rowid'] = leftID;
                    rows.push(obj);
                } catch(err) {
                    rows = [{ftpUrl:''}];
                    //Tablr.setData([{ftpUrl:''}]);
                }
                Tablr.setData(rows);
            }
            Tablr = frmRight.xTabulator('table_right', w0 / 2 - 27, 'cms_right', '', {layout: 'fitDataStretch', index: 'id', initialData: rows});
        }
        function btnRView_click() {
            var xel = (Tabll.getSelectedData())[0]; // get first selected element
            if (xel == undefined) {
                ToastError('No row selected');
                return;
            } else {
                leftID = xel.id;
                leftData = xel;
            }
            var sel = (Tablr.getSelectedData())[0]; // get first selected element
            if (sel == undefined) {
                ToastError('No row selected');
                return;
            }
            var parm = {id: sel.id};
            XFrame.setCaption('Detail').setContent(frmRight.viewCard(parm)).setAction(false).show(false);
        }
        function btnRAdd_click() {
            var xel = (Tabll.getSelectedData())[0]; // get first selected element
            if (xel == undefined) {
                ToastError('No row selected');
                return;
            } else {
                leftID = xel.id;
                leftData = xel;
            }
            XFrame.setCaption('Add Data')
                .setContent(frmRight.formAdd('add_rec',2))
                .setConfirmation()
                .setVerifier(true, ()=>{ return frmRight.doVerify(); })
                .setAction(true,()=>{
                    var odata = frmRight.readForm(false,false,true);
                    odata['id'] = 1+frmRight.Data.length;
                    odata['rowid'] = leftID;
                    //odata['fixMainUrl'] = '';
                    var url1 = odata['fixMainUrl_user'] + ':' +
                                odata['fixMainUrl_pass'] + '@' +
                                odata['fixMainUrl_ip'] + ':' +
                                odata['fixMainUrl_port'] + '?senderCompId=' +
                                odata['fixMainUrl_sender'] + '&targetCompId=' +
                                odata['fixMainUrl_target'];
                    odata['fixMainUrl'] = 'fix5://'+url1;
                    var url2 = odata['fixDrcUrl_user'] + ':' +
                                odata['fixDrcUrl_pass'] + '@' +
                                odata['fixDrcUrl_ip'] + ':' +
                                odata['fixDrcUrl_port'] + '?senderCompId=' +
                                odata['fixDrcUrl_sender'] + '&targetCompId=' +
                                odata['fixDrcUrl_target'];
                    odata['fixDrcUrl'] = 'fix5://'+url2;
                    frmRight.Data.push(odata);

                    var jeje = [];
                    for (var i=0; i<frmRight.Data.length; i++) {
                        var dati = frmRight.Data[i];
                        var oje = {};
                        oje['clientId'] = dati.clientId;
                        oje['clientName'] = dati.clientName;
                        oje['fixFirmId'] = dati.fixFirmId;
                        oje['fixSourceId'] = dati.fixSourceId;
                        oje['fixMainUrl'] = dati.fixMainUrl;
                        oje['fixDrcUrl'] = dati.fixDrcUrl;
                        jeje.push(oje);
                    }
                    leftData.data = JSON.stringify(jeje);

                    var fdata = frmLeft.formDataTabRow(leftData);
                    fdata.delete('rec_type_str');
                    Loader('Saving new record...');
                    //console.log(fdata);
                    Api('api/?1/config/config/update', {body: fdata}).then(
                        data => {
                            LoaderHide();
                            if (data.error == 0) {
                                ToastSuccess('New record saved');
                                btnRRefresh_click();
                            } else {
                                FError('Saving record failed', data.message);
                            }
                        },
                        error => {
                            LoaderHide();
                            FError('Saving record error', error);
                        }
                    );
                })
                .setAfterShow(()=>{
                    $('.dropdown').dropdown();
                })
                .show();
        }
        function btnREdit_click() {
            var xel = (Tabll.getSelectedData())[0]; // get first selected element
            if (xel == undefined) {
                ToastError('No row selected');
                return;
            } else {
                leftID = xel.id;
                leftData = xel;
            }
            var sel = (Tablr.getSelectedData())[0]; // get first selected element
            if (sel == undefined) {
                ToastError('No row selected');
                return;
            }

            var parm = {id: sel.id};
            var idx = frmRight.indexById(parm);
            if (idx !== false) {
                var arrurl = frmRight.Data[idx].fixMainUrl.split(/[:@?&/=]/);
                frmRight.Data[idx].fixMainUrl_user = arrurl[3] ?? '';
                frmRight.Data[idx].fixMainUrl_pass = arrurl[4] ?? '';
                frmRight.Data[idx].fixMainUrl_ip = arrurl[5] ?? '';
                frmRight.Data[idx].fixMainUrl_port = arrurl[6] ?? '';
                frmRight.Data[idx].fixMainUrl_sender = arrurl[8] ?? '';
                frmRight.Data[idx].fixMainUrl_target = arrurl[10] ?? '';
                arrurl = frmRight.Data[idx].fixDrcUrl.split(/[:@?&/=]/);
                frmRight.Data[idx].fixDrcUrl_user = arrurl[3] ?? '';
                frmRight.Data[idx].fixDrcUrl_pass = arrurl[4] ?? '';
                frmRight.Data[idx].fixDrcUrl_ip = arrurl[5] ?? '';
                frmRight.Data[idx].fixDrcUrl_port = arrurl[6] ?? '';
                frmRight.Data[idx].fixDrcUrl_sender = arrurl[8] ?? '';
                frmRight.Data[idx].fixDrcUrl_target = arrurl[10] ?? '';
            }
            XFrame.setCaption('Edit Record')
                .setContent(frmRight.formEdit(parm, 'edt_rec', 2))
                .setConfirmation()
                .setVerifier(true, ()=>{ return frmRight.doVerify(); })
                .setAction(true,()=>{
                    var adata = frmRight.readForm(true,true,true);
                    var url1 = adata['fixMainUrl_user'] + ':' +
                                adata['fixMainUrl_pass'] + '@' +
                                adata['fixMainUrl_ip'] + ':' +
                                adata['fixMainUrl_port'] + '?senderCompId=' +
                                adata['fixMainUrl_sender'] + '&targetCompId=' +
                                adata['fixMainUrl_target'];
                    adata['fixMainUrl'] = 'fix5://'+url1;
                    var url2 = adata['fixDrcUrl_user'] + ':' +
                                adata['fixDrcUrl_pass'] + '@' +
                                adata['fixDrcUrl_ip'] + ':' +
                                adata['fixDrcUrl_port'] + '?senderCompId=' +
                                adata['fixDrcUrl_sender'] + '&targetCompId=' +
                                adata['fixDrcUrl_target'];
                    adata['fixDrcUrl'] = 'fix5://'+url2;
                    //adata['rowid'] = leftID;
                    frmRight.updateData(adata,['id']);
                    if (leftData.record_type == 'PART') {
                        var jeje = [];
                        for (var i=0; i<frmRight.Data.length; i++) {
                            var dati = frmRight.Data[i];
                            var oje = {};
                            oje['clientId'] = dati.clientId;
                            oje['clientName'] = dati.clientName;
                            oje['fixFirmId'] = dati.fixFirmId;
                            oje['fixSourceId'] = dati.fixSourceId;
                            oje['fixMainUrl'] = dati.fixMainUrl;
                            oje['fixDrcUrl'] = dati.fixDrcUrl;
                            jeje.push(oje);
                        }
                        leftData.data = JSON.stringify(jeje);
                    } else {
                        var oje = {};
                        oje['ftpUrl'] = frmRight.Data[0].ftpUrl;
                        leftData.data = JSON.stringify(oje);
                    }

                    var fdata = frmLeft.formDataTabRow(leftData);
                    fdata.delete('rec_type_str');
                    Loader('Updating record...');
                    Api('api/?1/config/config/update', {body: fdata}).then(
                        data => {
                            LoaderHide();
                            if (data.error == 0) {
                                ToastSuccess('Record updated');
                                frmLeft.updateData(leftData, ['id']);
                                btnRRefresh_click();
                            } else {
                                FError('Update record failed', data.message);
                            }
                        },
                        error => {
                            LoaderHide();
                            FError('Update record error', error);
                        }
                    );
                })
                .setAfterShow(()=>{
                    $('.dropdown').dropdown();
                })
                .show();
        }
        function btnRDelete_click() {
            var xel = (Tabll.getSelectedData())[0]; // get first selected element
            if (xel == undefined) {
                ToastError('No row selected');
                return;
            } else {
                leftID = xel.id;
                leftData = xel;
            }
            var sel = (Tablr.getSelectedData())[0]; // get first selected element
            if (sel == undefined) {
                ToastError('No row selected');
                return;
            }

            $('body').modal('myConfirm', \"<i class='exclamation triangle icon red'></i> Delete Record\", \"Delete [\"+sel.clientName+\"] ?\", ()=>{
                Loader('Deleting record...');
                var data = frmRight.formDataTabRow(sel,true);
                frmRight.deleteData(sel,['id']);

                var jeje = [];
                for (var i=0; i<frmRight.Data.length; i++) {
                    var dati = frmRight.Data[i];
                    var oje = {};
                    oje['clientId'] = dati.clientId;
                    oje['clientName'] = dati.clientName;
                    oje['fixFirmId'] = dati.fixFirmId;
                    oje['fixSourceId'] = dati.fixSourceId;
                    oje['fixMainUrl'] = dati.fixMainUrl;
                    oje['fixDrcUrl'] = dati.fixDrcUrl;
                    jeje.push(oje);
                }
                leftData.data = JSON.stringify(jeje);

                var fdata = frmLeft.formDataTabRow(leftData);
                Api('api/?1/config/config/update', {body: fdata}).then(
                    data => {
                        LoaderHide();
                        if (data.error == 0) {
                            ToastSuccess('Record deleted');
                            btnRRefresh_click();
                        } else {
                            FError('Delete record failed', data.message);
                        }
                    },
                    error => {
                        LoaderHide();
                        FError('Delete record error', error);
                    }
                );
            });
        }
    "
);
include_once "pages/cms-genform2vr.php";
