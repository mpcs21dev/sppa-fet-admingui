<div id="trxBoard" class="board">
    <div id="leher" class="attached">
        <div id='leher-1' class='ui rounded' style='margin-bottom: 5px;'>
            <span class='ui small text'>Participant</span>
            <span class='padder2'></span>
            <select class='ui mini search dropdown' id='cbxPart'></select>
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
        resendUrl: 'api/?1/trx/resend/',
        resend: function(row) {
            var obj = {RfoResendRequest: {
                participantId: row.partid,
                clientId: row.cln_user_id,
                clnOrderId: row.cln_order_id
            }};
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
                id: {caption: "ID", title: "No", type: "numeric", autoValue: true, formatter: "rownum"},
                partid: {caption: "partid", type: "string", visible: false, noview:true,noadd:true,noedit:true},
                participant: {caption: "Participant", type:"string", headerFilter:true},
                record_date: {caption: "Rec Date", type: "numeric", headerFilter: true, editor:"number",headerFilterLiveFilter:false},
                status_enum: {caption: "Status", type: "string", headerFilter: true, formatter: (c)=>{
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
                order_side: {caption: "Order Side", type: "string", headerFilter: true},
                order_id: {caption: "OrderID", type: "string", headerFilter: true},
                cln_order_id: {caption: "Client OID", type: "string", headerFilter: true},
                mkt_order_id: {caption: "Market OID", type: "string", headerFilter: true},
                cln_user_id: {caption: "Cln UserID", type: "string", headerFilter: true},
                cln_party_id: {caption: "Cln PartyID", type: "string", headerFilter: true},
                trd_user_id: {caption: "Trd UserID", type: "string", headerFilter: true},
                trd_party_id: {caption: "Trd PartyID", type: "string", headerFilter: true},
                trade_id: {caption: "Trade ID", type:"string", headerFilter:true},
                initiator: {caption: "Initiator", type: "numeric", headerFilter: true},
                status: {caption: "Status", type: "numeric", visible:false, noview:true},
                inserted_at: {caption: "Created at", type: "datetime", autoValue: true},
                updated_at: {caption: "Updated at", type: "datetime", autoValue: true}
            });
            this.frmRight.setModel({
                id: {caption: "ID", title: "No", type: "numeric", autoValue: true, formatter: "rownum"},
                parent_id: {caption: "Parent ID", type: "numeric", visible: false},
                direction: {caption: "Direction", type:"string", headerFilter: true},
                record_type: {caption: "Rec Type", type:"string", headerFilter: true},
                message_type: {caption: "Msg Type", type: "string", headerFilter: true},
                data: {caption: "Data", type: "string", useTag:true, tagFormatField:"record_type"},
                inserted_at: {caption: "Created at", type: "datetime", autoValue: true},
                updated_at: {caption: "Updated at", type: "datetime", autoValue: true}
            });
        },
        init_table: function(urll="api/?1/trx/transaction/list/ALL",urlr="api/?1/trx/message/list/") {
            that = this;
            var w0 = Math.ceil(window.innerHeight),
                w1 = Math.ceil($("#menubar").outerHeight()),
                w2 = $id("leher-1").offsetHeight; //parseInt($id("leher-1").height),
                k1 = Math.ceil($("#kanan").outerHeight());
            const tinggi = w0 / 2 - 45;
            this.Tabll = this.frmLeft.xTabulator("table_left", tinggi, "board_trx_trx", urll, {
                layout: "fitDataFill", 
                initialSort:this.leftInitialSort,
                initialHeaderFilter: [{field:"record_date", value:<?=date("Ymd")?>}]
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
            this.Tablr = this.frmRight.xTabulator("table_right", tinggi, "board_trx_msg", urlr, {layout: "fitDataFill"});

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
        leftRefresh: function() {
            this.Tabll.setData();
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
            this.init_model();
            this.init_table();
            that = this;
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
                                        name: d.participant_id,
                                        value: d.participant_name
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
                                        that.Tabll.setData("api/?1/trx/transaction/list/"+that.partID);
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
        }
    };
</script>
