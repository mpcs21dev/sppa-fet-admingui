<!--
<div id="kepala" class="ui top attached segment">
    <div class="ui header"><h1><i class="quote right icon"></i> Quotes</h1></div>
</div>
-->
<div id="leher" class="attached standard">
    <div class='ui rounded' style='margin-bottom: 5px;'>
        <span class='ui small text'>Participant</span>
        <span class='padder2'></span>
        <select class='ui mini search dropdown' id='cbxPart'>
        </select>
    </div>
    <div class="ui grid">
        <div class="eight wide column">
            <div id="kiri" class="attached">
                <div class="ui mini orange tag label"><h5>Message</h5></div>
                <button class="ui mini violet icon button" id="btnRefresh" data-tooltip="Refresh" data-position="bottom left"><i class="redo icon"></i></button>
            </div>
            <div id="table_left" class="ui orange table"></div>
        </div>
        <div class="eight wide column">
            <div id="kanan" class="attached">
                <div class="ui mini green tag label"><h5>Quotes</h5></div>
                <div class="ui buttons">
                    <button class="ui mini violet icon button" id="btnRefresh" data-tooltip="Refresh" data-position="bottom left"><i class="redo icon"></i></button>
                </div>
            </div>
            <div id="table_right" class="ui green table"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var Tabll;
    var Tablr;
    var AccordionMenuID = "";
    var MenuID = "mnu-transaction";
    var frmLeft = new Formation();
    var frmRight = new Formation();

    var leftData = null;
    var leftID = 0;

    frmRight.setModel({
        id: {caption: "ID", title: "No", type: "numeric", autoValue: true, formatter: autoNumFormatter, frozen: true},
        trx_id: {caption: "Trx ID", type: "numeric"},
        in_out_type: {caption: "In / Out", type:"numeric"},
        msg_type: {caption: "Msg Type", type: "string"},
        buyer_id: {caption: "Buyer", type: "string"},
        seller_id: {caption: "Seller", type: "string"},
        data: {caption: "Data", type: "string"},
        inserted_at: {caption: "Created at", type: "datetime", autoValue: true},
        updated_at: {caption: "Updated at", type: "datetime", autoValue: true}
    });
    frmLeft.setModel({
        id: {caption: "ID", title: "No", type: "numeric", autoValue: true, formatter: autoNumFormatter, frozen: true},
        record_date: {caption: "Rec Date", type: "numeric"},
        record_type: {caption: "Rec Type", type:"string"},
        cln_order_id: {caption: "Client OID", type: "string"},
        mkt_order_id: {caption: "Market OID", type: "string"},
        buyer_id: {caption: "Buyer", type: "string"},
        seller_id: {caption: "Seller", type: "string"},
        status: {caption: "Status", type: "numeric"},
        inserted_at: {caption: "Created at", type: "datetime", autoValue: true},
        updated_at: {caption: "Updated at", type: "datetime", autoValue: true}
    });

    init_table = (urll="api/?1/trx/message/list/",urlr="api/?1/trx/transaction/list/") => {
        var w0 = Math.ceil(window.innerHeight),
            w1 = Math.ceil($("#leher").outerHeight()),
            w2 = 0; //parseInt($("#leher").css("margin")),
            k1 = Math.ceil($("#kanan").outerHeight());
        Tabll = frmLeft.xTabulator("table_left", w0-w1-k1-w2-w2-w2, "cms_left", urll);
        Tablr = frmRight.xTabulator("table_right", w0-w1-k1-w2-w2-w2, "cms_right", urlr);
    }

    mod_startup = () => {
        init_table();

        /*
        Tabll.on("rowClick", function(e, row){
            //leftData = row.getData();
            //leftID = leftData.ID;
        });
        */

        Api("api/?1/config/config/listall").then(
            data => {
                if (data.error == 0) {
                    if (data.data != null) {
                        if (Array.isArray(data.data)) {
                            var cbs = [];
                            for (var i=0; i<data.data.length; i++) {
                                var d = data.data[i];
                                var o = {
                                    name: d.participant_id,
                                    value: d.participant_id
                                };
                                //if (i==0) o["selected"]=true;
                                cbs.push(o);
                            }
                            $('#cbxPart').dropdown({
                                values: cbs,
                                onChange: (xval,tex,sel) => {
                                    var val = xval.toLowerCase();
                                    //init_table("api/?1/trx/message/list/"+val,"api/?1/trx/transaction/list/"+val);
                                    Tabll.setData("api/?1/trx/message/list/"+val);
                                    Tablr.setData("api/?1/trx/transaction/list/"+val);
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
</script>