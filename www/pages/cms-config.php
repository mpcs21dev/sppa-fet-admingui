<?php
$Model = '{
    id:             {caption:"ID", type:"numeric", formatting: "rownum", autoValue:true, frozen:true},
    participant_id: {caption:"SPPA Firm ID",posttext:"(Only letters, numbers and underscore are allowed)", type:"string", placeholder:"Participant ID", readOnly: true, noedit:true, headerFilter:true},
    participant_name:{caption:"Firm Name", type:"string", headerFilter:true},
    record_type:    {caption:"Record Type", type:"string", readOnly: true, noedit:true, noview:true, visible: false, control:"lookup", option:{table:"PartRecType",id:"xkey",text:"xval"}},
    rec_type_str:   {caption:"Record Type", type:"string", readOnly: true, noadd:true, headerFilter:true},
    data:           {caption:"Data", type:"string", visible:false, noadd:true, noedit:true, useTag:true, tagFormat:"json"},
    inserted_at:    {caption: "Created", type: "datetime", autoValue: true, formatter: datetimeFormatter},
    updated_at:     {caption: "Updated", type: "datetime", autoValue: true, formatter: datetimeFormatter}
}';
// @#?&=+%/
$Model2 = '{
    id:          {caption:"ID", type:"numeric", formatting: "rownum", autoValue:true, frozen:true},
    rowid:       {caption:"", type:"numeric", noadd:true, noedit:true, noview:true, visible:false},
    clientId:    {caption:"3rd Party ID", type:"string", headerFilter:true,verify:(v)=>{return FieldVerifier(v.length<1,"Field [3rd Party ID] blank.")}},
    clientName:  {caption:"3rd Party User Name", type:"string",upperCase:true, headerFilter:true,verify:(v)=>{return FieldVerifier(v.length<1,"Field [3rd Party User Name] blank.")}},
    fixFirmId:   {caption:"Fix SPPA Firm ID", type:"string",upperCase:true, headerFilter:true,verify:(v)=>{return FieldVerifier(v.length<1,"Field [SPPA Firm ID] blank.")}},
    fixSourceId: {caption:"Fix Source ID", type:"string", defaultValue:"D", upperCase:true,verify:(v)=>{return FieldVerifier(v.length<1,"Field [FIX Source ID] blank.")}},
    fixMainUrl:  {caption:"Fix Main Url", type:"string", noedit:true, noadd:true},
    fixDrcUrl:   {caption:"Fix DRC Url", type:"string", noedit:true, noadd:true},
    fixMainUrl_user: {caption:"Fix User", type:"string", pretext:"<hr>Fix Main URL", placeholder:"SPPA User Name", upperCase:true, noview:true, visible:false,verify:(v)=>{return FieldVerifier(v.length<1,"Field [FIX User] blank.")}},
    fixMainUrl_pass: {caption:"SPPA Password", posttext:"( Do not use these chars for password: @#?&=:+%/\' )", allowHTML:"all", type:"string", noview:true, placeholder:"SPPA Password", visible:false,verify:(v)=>{return FieldVerifier(v.length<1,"Field [SPPA Password] blank.")}},
    fixMainUrl_ip:   {caption:"Fix Server IP", type:"string", noview:true, visible:false, defaultValue:"172.61.2.34",verify:(v)=>{return FieldVerifier(v.length<1,"Field [FIX Server IP] blank.")}},
    fixMainUrl_port: {caption:"Fix Server Port", type:"string", noview:true, visible:false, defaultValue:"11000",verify:(v)=>{return FieldVerifier(v.length<1,"Field [FIX Server Port] blank.")}},
    fixMainUrl_sender:{caption:"User Comp ID", posttext:"(Only letters allowed)", upperCase:true, type:"string", noview:true, visible:false,verify:(v)=>{return FieldVerifier(v.length<1,"Field [SPPA User Comp ID] blank.")}},
    fixMainUrl_target:{caption:"Target Comp Id", posttext:"(Only letters allowed)", type:"string", noview:true, visible:false, defaultValue:"AXECHANGE",verify:(v)=>{return FieldVerifier(v.length<1,"Field [SPPA Target Comp ID] blank.")}},
    fixDrcUrl_user:  {caption:"SPPA DRC User Name", pretext:"<hr>Fix DRC URL", type:"string", placeholder:"SPPA DRC User Name", upperCase:true, noview:true, visible:false,verify:(v)=>{return FieldVerifier(v.length<1,"Field [SPPA DRC User Name] blank.")}},
    fixDrcUrl_pass:  {caption:"SPPA DRC Password", posttext:"( Do not use these chars for password: @#?&=:+%/\' )", allowHTML:"all", type:"string", noview:true, visible:false,verify:(v)=>{return FieldVerifier(v.length<1,"Field [SPPA DRC Password] blank.")}},
    fixDrcUrl_ip:    {caption:"Fix DRC Server IP", type:"string", noview:true, visible:false, defaultValue:"172.61.2.36",verify:(v)=>{return FieldVerifier(v.length<1,"Field [SPPA DRC Server IP] blank.")}},
    fixDrcUrl_port:  {caption:"Fix DRC Port", type:"string", noview:true, visible:false, defaultValue:"11000",verify:(v)=>{return FieldVerifier(v.length<1,"Field [SPPA DRC Server Port] blank.")}},
    fixDrcUrl_sender:{caption:"DRC User Comp ID", posttext:"(Only letters allowed)", upperCase:true, type:"string", noview:true, visible:false,verify:(v)=>{return FieldVerifier(v.length<1,"Field [DRC User Comp ID] blank.")}},
    fixDrcUrl_target:{caption:"DRC Target Comp ID", posttext:"(Only letters allowed)", upperCase:true, type:"string", noview:true, visible:false, defaultValue:"AXECHANGE",verify:(v)=>{return FieldVerifier(v.length<1,"Field [DRC Target Comp ID] blank.")}}
}';

$Model3 = '{
    id:      {caption:"ID", type:"numeric", formatting: "rownum", autoValue:true, frozen:true},
    rowid:   {caption:"", type:"numeric", noadd:true, noedit:true, noview:true, visible:false},
    ftpUrl:  {caption:"FTP Url", type:"string"}
}';

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
        Ref.load('DefaultValue', 'api/?1/config/ref/default-value');
    ",
    "extraJS" => "
        function countClientId(cid) {
            var hasil = 0;
            for (let i=0; i<frmLeft.Data.length; i++) {
                var di = frmLeft.Data[i];
                if (di['record_type'] != 'PART') continue;
                var ci = JSON.parse(di.data);
                for (let k=0; k<ci.length; k++) {
                    var ck = ci[k];
                    if (cid == ck['clientId']) hasil ++;
                }
            }
            return hasil;
        }
        function countUrlPart(coid,part) {
            var xpart = {
                'fix-user':3,
                'fix-pass':4,
                'fix-ip':5,
                'fix-port':6,
                'fix-sender':8,
                'fix-target':10
            };
            var hasil = 0;
            for (let i=0; i<frmLeft.Data.length; i++) {
                var di = frmLeft.Data[i];
                if (di['record_type'] != 'PART') continue;
                var ci = JSON.parse(di['data']);
                for (let k=0; k<ci.length; k++) {
                    var ck = ci[k];
                    var arrurl = ck.fixMainUrl.split(/[:@?&/=]/);
                    var sender = arrurl[xpart[part]] ?? '';
                    if (coid == sender) hasil ++;
                }
            }
            return hasil;
        }
        function countCompId(coid) {
            return countUrlPart(coid,'fix-sender');
            /*
            var hasil = 0;
            for (let i=0; i<frmLeft.Data.length; i++) {
                var di = frmLeft.Data[i];
                if (di['record_type'] != 'PART') continue;
                var ci = JSON.parse(di['data']);
                for (let k=0; k<ci.length; k++) {
                    var ck = ci[k];
                    var arrurl = ck.fixMainUrl.split(/[:@?&/=]/);
                    var sender = arrurl[8] ?? '';
                    if (coid == sender) hasil ++;
                }
            }
            return hasil;
            */
        }
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
                    $('#fld-record_type').dropdown('set selected', 'PART');
                    document.getElementById('fld-participant_id').addEventListener('input', function(ev) {
                    	includeChars(ev,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_');
                    });
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
                    document.getElementById('fld-participant_id').addEventListener('input', function(ev) {
                    	includeChars(ev,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_');
                    });
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
                \$id('btnRRefresh').style.display = 'block';
                \$id('btnRView').style.display = 'block';                
                \$id('btnREdit').style.display = 'block';
            } else if (tipe == 'FTP') {
                frmRight.setModel(".$Model3.");
                \$id('btnRAdd').style.display = 'none';
                \$id('btnRDelete').style.display = 'none';
                \$id('btnRRefresh').style.display = 'block';
                \$id('btnRView').style.display = 'block';                
                \$id('btnREdit').style.display = 'block';
            } else {
                \$id('btnRAdd').style.display = 'none';
                \$id('btnRDelete').style.display = 'none';                
                \$id('btnRRefresh').style.display = 'none';
                \$id('btnRView').style.display = 'none';                
                \$id('btnREdit').style.display = 'none';
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
                .setContent(frmRight.formAdd('add_rec',2,true))
                .setConfirmation('Save Data','Are you sure?',true,()=>{
                    var odata = frmRight.readForm(false,false,true);
                    var newCID = odata['clientId'];
                    var newFUSR = odata['fixMainUrl_user'];
                    var newCOID = odata['fixMainUrl_sender'];
                    var countCID = countClientId(newCID);
                    var countCOID = countCompId(newCOID);
                    var countFUSR = countUrlPart(newFUSR,'fix-user');
                    if (countFUSR > 0) {
                        alert('ERROR :: Duplicate FIX User detected.');
                        return false;
                    }
                    if (countCID > 0) {
                        alert('ERROR :: Duplicate 3rd Party ID detected.');
                        return false;
                    }
                    if (countCOID > 0) {
                        alert('ERROR :: Duplicate Client CompID detected.');
                        return false;
                    }
                    return true;                    
                })
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
                    fdata.append('SEND_operation','NEW');
                    fdata.append('SEND_participantId',leftData.participant_id);
                    fdata.append('SEND_userId',odata.clientId);
                    //fdata.append('SEND_userId',odata.fixMainUrl_user);
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
                    document.getElementById('fld-fixMainUrl_pass').addEventListener('input', function(ev) {
                    	excludeChars(ev,'@#?&:=+%/\'');
                    });
                    document.getElementById('fld-fixDrcUrl_pass').addEventListener('input', function(ev) {
                    	excludeChars(ev,'@#?&:=+%/\'');
                    });
                    document.getElementById('fld-fixMainUrl_sender').addEventListener('input', function(ev) {
                    	includeChars(ev);
                    });
                    document.getElementById('fld-fixDrcUrl_sender').addEventListener('input', function(ev) {
                    	includeChars(ev);
                    });
                    document.getElementById('fld-fixMainUrl_port').addEventListener('input', function(ev) {
                        includeChars(ev,'1234567890');
                    });
                    document.getElementById('fld-fixMainUrl_ip').addEventListener('input', function(ev) {
                        includeChars(ev,'1234567890.:');
                    });
                    document.getElementById('fld-fixDrcUrl_port').addEventListener('input', function(ev) {
                        includeChars(ev,'1234567890');
                    });
                    document.getElementById('fld-fixDrcUrl_ip').addEventListener('input', function(ev) {
                        includeChars(ev,'1234567890.:');
                    });
                    
                    \$id('fld-fixMainUrl_ip').value = Ref.find('DefaultValue','xkey','MAIN-SERVER-IP','xval');
                    \$id('fld-fixMainUrl_port').value = Ref.find('DefaultValue','xkey','MAIN-SERVER-PORT','xval');
                    \$id('fld-fixMainUrl_target').value = Ref.find('DefaultValue','xkey','MAIN-TARGET-COMPID','xval');
                    \$id('fld-fixDrcUrl_ip').value = Ref.find('DefaultValue','xkey','DRC-SERVER-IP','xval');
                    \$id('fld-fixDrcUrl_port').value = Ref.find('DefaultValue','xkey','DRC-SERVER-PORT','xval');
                    \$id('fld-fixDrcUrl_target').value = Ref.find('DefaultValue','xkey','DRC-TARGET-COMPID','xval');
                    \$id('fld-fixSourceId').value = Ref.find('DefaultValue','xkey','FIXSOURCEID','xval');

                    \$id('fld-fixMainUrl_ip').readOnly = true;
                    \$id('fld-fixMainUrl_port').readOnly = true;
                    \$id('fld-fixMainUrl_target').readOnly = true;
                    \$id('fld-fixDrcUrl_ip').readOnly = true;
                    \$id('fld-fixDrcUrl_port').readOnly = true;
                    \$id('fld-fixDrcUrl_target').readOnly = true;
                    \$id('fld-fixSourceId').readOnly = true;
                })
                .show();
        }
        function btnREdit_ftp(sel) {
            //
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

            if (leftData.record_type == 'FTP') {
                btnREdit_ftp(sel);
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
                .setConfirmation('Save Data','Are you sure?',true,()=>{
                    var odata = frmRight.readForm(true,true,true);
                    var oldCID = \$id('fld-clientId').dataset.fieldValue;
                    var newCID = odata['clientId'];
                    var oldFUSR = \$id('fld-fixMainUrl_user').dataset.fieldValue;
                    var newFUSR = odata['fixMainUrl_user'];
                    var oldCOID = \$id('fld-fixMainUrl_sender').dataset.fieldValue;
                    var newCOID = odata['fixMainUrl_sender'];
                    if (oldCID != newCID) {
                        var countCID = countClientId(newCID);
                        if (countCID > 0) {
                            alert('ERROR :: Duplicate 3rd Party ID detected.');
                            return false;
                        }
                    }
                    if (oldFUSR != newFUSR) {
                        var countFUSR = countUrlPart(newFUSR,'fix-user');
                        if (countFUSR > 0) {
                            alert('ERROR :: Duplicate FIX User detected.');
                            return false;
                        }
                    }
                    if (oldCOID != newCOID) {
                        var countCOID = countCompId(newCOID);
                        if (countCOID > 0) {
                            alert('ERROR :: Duplicate Client CompID detected.');
                            return false;
                        }                    
                    }
                    return true;
                })
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
                    fdata.append('SEND_operation','EDIT');
                    fdata.append('SEND_participantId',leftData.participant_id);
                    fdata.append('SEND_userId',adata.clientId);
                    //fdata.append('SEND_userId',adata.fixMainUrl_user);
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
                    \$id('fld-clientId').readOnly = true;
                    document.getElementById('fld-fixMainUrl_pass').addEventListener('input', function(ev) {
                    	excludeChars(ev,'@#?&=:+%/\'');
                    });
                    document.getElementById('fld-fixDrcUrl_pass').addEventListener('input', function(ev) {
                    	excludeChars(ev,'@#?&=:+%/\'');
                    });
                    document.getElementById('fld-fixMainUrl_sender').addEventListener('input', function(ev) {
                    	includeChars(ev);
                    });
                    document.getElementById('fld-fixDrcUrl_sender').addEventListener('input', function(ev) {
                    	includeChars(ev);
                    });

                    document.getElementById('fld-fixMainUrl_port').addEventListener('input', function(ev) {
                        includeChars(ev,'1234567890');
                    });
                    document.getElementById('fld-fixMainUrl_ip').addEventListener('input', function(ev) {
                        includeChars(ev,'1234567890.:');
                    });
                    document.getElementById('fld-fixDrcUrl_port').addEventListener('input', function(ev) {
                        includeChars(ev,'1234567890');
                    });
                    document.getElementById('fld-fixDrcUrl_ip').addEventListener('input', function(ev) {
                        includeChars(ev,'1234567890.:');
                    });
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
                fdata.append('SEND_operation','DELETE');
                fdata.append('SEND_participantId',leftData.participant_id);
                fdata.append('SEND_userId',sel.clientId);
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
