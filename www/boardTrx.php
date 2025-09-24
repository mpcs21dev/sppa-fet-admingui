<div id="trxBoard" class="board">
    <div id="leher" class="attached">
        <div id='leher-1' class='ui rounded attached menu titler' style='margin-bottom: 5px;'>
            <span class='ui small text'>Participant</span>
            <span class='padder2'></span>
            <select class='ui mini search dropdown' id='cbxPart'></select>
            <div class="padder2"></div>
            <div class="padder2"></div>
            <div class="ui">Filter Date</div>
            <div class="padder2"></div>
            <div class="ui finput">
                <div>Date From</div>
                <input class="donly" id="trxfil-datefrom" type="date">
            </div>
            <div class="padder2"></div>
            <div class="ui finput">
                <div>Date To</div>
                <input class="donly" id="trxfil-dateto" type="date">
            </div>
            <div class="padder2"></div>
            <button class="ui mini brown button" id="trxfilBtnApply" data-tooltip="Apply Filter" data-position="bottom center"><i class="redo icon"></i> Apply</button>
            <div class="padder2"></div>
            <button class="ui mini blue button" id="trxfilBtnToday" data-tooltip="Today date" data-position="bottom center"><i class="redo icon"></i> Today</button>
        </div>
        <div class='laycol'>
            <div class='layrow'>
                <div class="vertical-text border-only orange">RFO</div>
                <div class="toolbar orange"><div class='ui vertical icon buttons'>
                    <button id='btnLRefresh' class='ui mini violet icon button fixedvert'><i class='redo large icon'></i><br><span>Refresh</span></button>
                    <button id='btnLView' class='ui mini blue icon button fixedvert'><i class='eye outline large icon'></i><br><span>View</span></button>
                    <button id='btnLResend' class='ui mini yellow icon button fixedvert'><i class='share square large icon'></i><br><span>Resend</span></button>
                    <!--
                    <button id='btnLAdd' class='ui mini blue icon button fixedvert'><i class='plus circle large icon'></i><br><span>Add</span></button>
                    <button id='btnLEdit' class='ui mini blue icon button fixedvert'><i class='edit outline large icon'></i><br><span>Edit</span></button>
                    <button id='btnLDelete' class='ui mini red icon button fixedvert'><i class='minus circle large icon'></i><br><span>Delete</span></button>
                    -->
                </div></div>
                <div id='table_left' class='ui orange table fixedmargin'></div>
            </div>
            <div class='separator'></div>
            <div class='layrow'>
                <div class="vertical-text border-only green">Message</div>
                <div class="toolbar green"><div class='ui vertical icon buttons'>
                    <button id='btnRRefresh' class='ui mini violet icon button fixedvert'><i class='redo large icon'></i><br><span>Refresh</span></button>
                    <button id='btnRView' class='ui mini blue icon button fixedvert'><i class='eye outline large icon'></i><br><span>View</span></button>
                    <!--
                    <button id='btnRAdd' class='ui mini blue icon button fixedvert'><i class='plus circle large icon'></i><br><span>Add</span></button>
                    <button id='btnREdit' class='ui mini blue icon button fixedvert'><i class='edit outline large icon'></i><br><span>Edit</span></button>
                    <button id='btnRDelete' class='ui mini red icon button fixedvert'><i class='minus circle large icon'></i><br><span>Delete</span></button>
                    -->
                </div></div>
                <div id='table_right' class='ui green table fixedmargin'></div>
                <div id="detail_message" class="ui green table" style="display:none"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    Trx = {
        Tabll: null,
        Tablr: null,
        MenuID: 'mnu-transaction',
        frmLeft: new Formation(),
        frmRight: new Formation(),
        leftInitialSort: [{column:'inserted_at',dir:'desc'}],
        leftData: null,
        leftID: 0,
        partID: '',
        rendered: false,
        resendUrl: 'api/?1/trx/resend/',
        resend: function(row) {
            var lanjut = true;
            if (row.record_date+"" != (new Date()).format(noseparator)) lanjut = false;
            if (row.status != 10) lanjut = false;
            if (row.initiator != 1) lanjut = false;
            if (row.resend != 0) lanjut = false;
            if (!lanjut) {
                ToastError("Resend failed. Can not resend this trx.");
                return;
            }
            var obj = {
                participantId: row.partid,
                clientId: row.cln_user_id,
                clnOrderId: row.cln_order_id
            };
            $('body').modal('myConfirm', "<i class='exclamation triangle icon red'></i> Resend Order", "Continue resend selected order?", ()=>{
                Loader("Resending order...");
                Api(this.resendUrl+row.cln_order_id, {body: JSON.stringify(obj)}).then(
                    oke => {
                        LoaderHide();
                        if (oke.error == 0) {
                            ToastSuccess("Resend command sent");
                        } else {
                            ToastError(oke.message);
                            console.log(oke);
                        }
                    },
                    err => {
                        LoaderHide();
                        ToastError("Resend order error");
                        console.log(err);
                    }
                );
            });
        },
        init_model: function() {
            this.frmLeft.setModel({
                id: {caption: "ID", title: "No", type: "numeric", autoValue: true, formatter: "rownum", width:50},
                partid: {caption: "partid", type: "string", visible: false, noview:true,noadd:true,noedit:true},
                participant: {caption: "SPPA Firm ID", type:"string", headerFilter:true, width:100},
                record_date: {caption: "Rec Date", type: "numeric", width:100},
                status_enum: {caption: "Status", type: "string", headerFilter: true, width:120, formatter: (c)=>{
                    const val = c.getValue();
                    let lmn = c.getElement();
                    switch (val) {
                        case 'RFO STATUS NEW':
                            lmn.style.backgroundColor = "#fff";
                            break;
                
                        case 'RFO STATUS AI':
                            lmn.style.backgroundColor = "#ffe";
                            break;
                
                        case 'RFO STATUS AJ':
                            lmn.style.backgroundColor = "#fef";
                            break;
                
                        case 'RFO STATUS AE':
                            lmn.style.backgroundColor = "#aaa";
                            break;
                    
                        case 'RFO STATUS AG':
                            lmn.style.backgroundColor = "#eff";
                            break;
                
                        case 'RFO STATUS J':
                            lmn.style.backgroundColor = "#777";
                            lmn.style.color = "#ff7";
                            break;
                
                        default:
                            break;
                    }
                    return val;
                }},
                initiator_: {caption: "Initiator", type: "string", headerFilter: true, width:100},
                responder: {caption: "Responder", type: "string", headerFilter: true, width:100},
                order_side: {caption: "Order Side", type: "string", headerFilter: true, width:100},
                order_id: {caption: "OrderID", type: "string", headerFilter: true, width:100},
                cln_order_id: {caption: "Client OID", type: "string", headerFilter: true, width:100},
                mkt_order_id: {caption: "Market OID", type: "string", headerFilter: true, width:100},
                cln_user_id: {caption: "Cln UserID", type: "string", headerFilter: true, width:100},
                cln_party_id: {caption: "Cln PartyID", type: "string", headerFilter: true, width:100},
                trd_user_id: {caption: "Trd UserID", type: "string", headerFilter: true, width:100},
                trd_party_id: {caption: "Trd PartyID", type: "string", headerFilter: true, width:100},
                trade_id: {caption: "Trade ID", type:"string", headerFilter:true, width:100},
                initiator: {caption: "Initiator", type:"boolean", visible: false, headerHozAlign:"center", hozAlign:"center", formatter:boolFormatter, headerFilter: true},
                resend: {caption: "Resend", type:"boolean", headerHozAlign:"center", hozAlign:"center", formatter:boolFormatter, headerFilter: true, width:70},
                status: {caption: "Status", type: "numeric", visible:false, noview:true},
                inserted_at: {caption: "Created at", type: "datetime", autoValue: true, width:150},
                updated_at: {caption: "Updated at", type: "datetime", autoValue: true, width:150}
            });
            this.frmRight.setModel({
                id: {caption: "ID", title: "No", type: "numeric", autoValue: true, formatter: "rownum", width:50},
                parent_id: {caption: "Parent ID", type: "numeric", visible: false},
                direction: {caption: "Direction", type:"string", headerFilter: true, width:100},
                record_type: {caption: "Rec Type", type:"string", headerFilter: true, width:100},
                message_type: {caption: "Msg Type", type: "string", headerFilter: true, width:100},
                data: {caption: "Data", type: "string", useTag:true, tagFormatField:"record_type", width:200},
                inserted_at: {caption: "Created at", type: "datetime", autoValue: true, width:120},
                updated_at: {caption: "Updated at", type: "datetime", autoValue: true, width:120}
            });
        },
        init_table: function(urll="api/?1/trx/transaction/list/ALL",urlr="api/?1/trx/message/list/") {
            that = this;
            var w0 = Math.ceil(window.innerHeight),
                w1 = Math.ceil($("#menubar").outerHeight()),
                w2 = $id("leher-1").offsetHeight; //parseInt($id("leher-1").height),
                k1 = Math.ceil($("#kanan").outerHeight());
            const tinggi = w0 / 2 - 45;
            var df = (new Date()).format("isoDate");
            var dt = (new Date()).format("isoDate");
            $id("trxfil-datefrom").value=df;
            $id("trxfil-dateto").value=dt;
            var url1 = urll+"/"+df+"/"+dt;
            this.Tabll = this.frmLeft.xTabulator("table_left", tinggi, "board_trx_trx", url1, {
                initialSort:this.leftInitialSort,
                //initialHeaderFilter: [{field:"record_date", value:<?=date("Ymd")?>}]
                /*
                rowFormatter: (row) => {
                    var data = row.getData();
                    switch (data.status){
                        case 0:
                            row.getElement().style.backgroundColor = '#fff';
                            break;
                        case 1:
                            row.getElement().style.backgroundColor = '#77f';
                            break;
                        case 2:
                            row.getElement().style.backgroundColor = '#7ff';
                            break;
                        case 3:
                            row.getElement().style.backgroundColor = '#777';
                            break;
                    }
                }
                */
            });
            this.Tablr = this.frmRight.xTabulator("table_right", tinggi, "board_trx_msg", urlr);

            this.Tabll.on('tableBuilt', function(){
                let af = "";
                try {
                    af = this.Tabll.getHeaderFilterValue("record_date");
                } catch(e) {}
                try {
                    if (af=="") this.Tabll.setHeaderFilterValue("record_date",(new Date()).format("noseparator"));
                } catch(e) {}
            });

            this.Tabll.on('rowSelected', function(row){
                //e - the click event object
                //row - row component
                that.leftData = row.getData();
                that.leftID = that.leftData.id;
                that.partID = that.leftData.partid;
                that.Tablr.setData('api/?1/trx/message/list/'+that.partID+'/'+that.leftID);
            });
            /*
            this.Tabll.on('rowDeselected', function(row){
                //e - the click event object
                //row - row component
                that.leftData = {}; //row.getData();
                that.leftID = 0; //that.leftData.id;
                that.Tablr.setData('api/?1/trx/message/list/');
            });
            */
            this.Tabll.on("rowDblClick", function(e, row){
                //e - the click event object
                //row - row component
                that.leftID = row.id;
                that.leftData = row;
                that.leftView();
            });
            this.Tabll.on('dataLoaded', function(data){
                /*
                if (that.leftID != 0) {
                    that.Tabll.selectRow(that.Tabll.getRows().filter(row => row.getData().id == that.leftID));
                }
                */
            });
            this.Tablr.on("rowSelected", function(row){
                var he = $id("table_right").offsetHeight;
                var data = row.getData();
                var de = $id("detail_message");
                de.style.display = "block";
                de.style.height = he+"px";
                if (data.record_type == 'XML') {
                    de.innerHTML = vxml(data,"data");
                } else {
                    de.innerHTML = vjson(data,"data");
                }
            });
            this.Tablr.on("rowDeselected", function(row){
                var de = $id("detail_message");
                de.style.display = "none";
            });
        },
        leftRefresh: function(mode="") {
            if (!this.rendered) return;
            if (mode == "apply") {
                var p = $("#cbxPart").dropdown("get value");
                var url = "api/?1/trx/transaction/list/"+p;
                var df = $id("trxfil-datefrom").value;
                var dt = $id("trxfil-dateto").value;
                if (df != "") url += "/"+df;
                if (df != "" && dt != "") url += "/"+dt;
                this.Tabll.setData(url);
            } else if (mode == "today") {
                var p = $("#cbxPart").dropdown("get value");
                var df = (new Date()).format("isoDate");
                $id("trxfil-datefrom").value = df;
                $id("trxfil-dateto").value = df;
                var url = "api/?1/trx/transaction/list/"+p+"/"+df+"/"+df;
                this.Tabll.setData(url);
            } else this.Tabll.setData();
        },
        rightRefresh: function() {
            if (this.leftID > 0) {
                this.Tablr.setData('api/?1/trx/message/list/'+this.partID+'/'+this.leftID);
            } else {
                this.Tablr.setData('api/?1/trx/message/list/');
            }
        },
        leftView: function() {
            var sel = (this.Tabll.getSelectedData())[0]; // get first selected element
            if (sel == undefined) {
                ToastError("No row selected");
                return;
            }
            var parm = {id: sel.id, partid: sel.partid};
            XFrame.setCaption("RFO").setContent(this.frmLeft.viewCard(parm)).setAction(false).show(false);
        },
        leftResend: function() {
            var sel = (this.Tabll.getSelectedData())[0]; // get first selected element
            if (sel == undefined) {
                ToastError("No row selected");
                return;
            }
            //var parm = {id: sel.id, partid: sel.partid};
            //XFrame.setCaption("RFO").setContent(this.frmLeft.viewCard(parm)).setAction(false).show(false);
            this.resend(sel);
        },
        rightView: function() {
            var sel = (this.Tablr.getSelectedData())[0]; // get first selected element
            if (sel == undefined) {
                ToastError("No row selected");
                return;
            }
            var parm = {id: sel.id};
            XFrame.setCaption("Message").setContent(this.frmRight.viewCard(parm)).setAction(false).show(false);
        },
        startup: function() {
            if (this.rendered) return;
            let that = this;
            try {
                this.init_model();
                this.init_table();

                $("#trxfilBtnApply").on("click", ()=>{ that.leftRefresh("apply"); });
                $("#trxfilBtnToday").on("click", ()=>{ that.leftRefresh("today"); });

                $("#btnLRefresh").on("click", ()=>{ that.leftRefresh(); });
                $("#btnLView").on("click", ()=>{ that.leftView(); });
                $("#btnRRefresh").on("click", ()=>{ that.rightRefresh(); });
                $("#btnRView").on("click", ()=>{ that.rightView(); });
                $("#btnLResend").on("click", ()=>{ that.leftResend(); });
                Api("api/?1/config/config/listall-noftp").then(
                    data => {
                        if (data.error == 0) {
                            if (data.data != null) {
                                if (Array.isArray(data.data)) {
                                    var cbs = [];
                                    const p = {
                                        name: 'ALL',
                                        value: 'ALL',
                                        selected: true
                                    };
                                    //if (i==0) o["selected"]=true;
                                    cbs.push(p);
                                    for (var i=0; i<data.data.length; i++) {
                                        var d = data.data[i];
                                        var o = {
                                            name: d.participant_name,
                                            value: d.participant_id
                                        };
                                        //if (i==0) o["selected"]=true;
                                        cbs.push(o);
                                    }
                                    //console.log(cbs);
                                    $('#cbxPart').dropdown({
                                        values: cbs,
                                        onChange: function(xval,tex,sel) {
                                            that.partID = xval.toLowerCase();
                                            //init_table("api/?1/trx/message/list/"+val,"api/?1/trx/transaction/list/"+val);
                                            var df = $id("trxfil-datefrom").value;
                                            var dt = $id("trxfil-dateto").value;
                                            var url1 = "api/?1/trx/transaction/list/"+that.partID;
                                            if (df != "") url1 += "/"+df
                                            if (df != "" && dt != "") url1 += "/"+dt
                                            that.Tabll.setData(url1);
                                            //that.Tablr.setData("api/?1/trx/transaction/list/"+val);
                                            
                                        }
                                    });
                                }
                            }
                        } else {
                            //SwalToast('error','Error Get Challange');
                            ToastError("Error readng config table");
                            console.log(data);
                        }
                    },
                    error => {
                        //SwalToast('error','Get Challange: Server Error');
                        ToastError("Config List: Server Error");
                        console.log({status: "Config List: Server Error", detail: error});
                    }
                );
                this.rendered = true;
            } catch (err) {
                ToastError(err);
            }
        }
    };
</script>
