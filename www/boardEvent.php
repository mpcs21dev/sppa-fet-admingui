<div id="eventBoard" class="board">
    <div id="lehere" class="ui attached menu rounded titler">
        <div class="ui buttons">
            <button class="ui mini violet button" id="btnERefresh" data-tooltip="Refresh" data-position="bottom left"><i class="redo icon"></i> Refresh</button>
            <!--
            <div id='mnuERefresh' class="ui mini violet dropdown icon button">
                <i class="dropdown icon"></i>
                <div class="ui vertical menu m180">
                    <div id="btnmERefresh" class="mini item"><i class="redo icon"></i> Refresh Data</div>
                    <div id="btnmEReset" class="mini item"><i class="cog icon"></i> Reset Column Settings</div>
                </div>
            </div>
            -->
        </div>
        <div class="padder2"></div>
        <div class="padder2"></div>
        <div class="ui">Filter Date</div>
        <div class="padder2"></div>
        <div class="ui finput">
            <div>Date From</div>
            <input id="evefil-datefrom" type="datetime-local">
        </div>
        <div class="padder2"></div>
        <div class="ui finput">
            <div>Date To</div>
            <input id="evefil-dateto" type="datetime-local">
        </div>
        <div class="padder2"></div>
        <button class="ui mini brown button" id="evefilBtnApply" data-tooltip="Apply Filter" data-position="bottom center"><i class="redo icon"></i> Apply</button>
        <div class="padder2"></div>
        <button class="ui mini blue button" id="evefilBtnToday" data-tooltip="Today date" data-position="bottom center"><i class="redo icon"></i> Today</button>
    </div>
    <div id="badane" class="attached standard-full flexrow">
        <div id="table_event" class="ui orange table"></div>
        <div id="detail_event" class="ui violet table" style="display:none"></div>
    </div>
</div>
<script type="text/javascript">
    function fmt_appidx(cell) {
        var value = cell.getValue();
        if (value=="") return value;
        var hasil = value;
        var arv = value.split("-");
        if (arv.length == 2) {
            var cn = Dash.getClientName(arv[0], arv[1]);
            if (cn != "") hasil = arv[0] + " - " + cn + " - " + arv[1];
        }
        return hasil;
    }
    XEvent = {
        Table: null,
        MenuID: 'mnu-event',
        frmPage: new Formation(),
        initialFilter: [{field:'',type:'',value:''}],
        initialSort: [{column:'id',dir:'desc'}],
        lastData: null,
        lastID: 0,
        rendered: false,
        url: 'api/?1/wsc/event/latest',
        refresh: function(reset="") {
            if (!this.rendered) return;
            //var cp = this.Tabll.getPage();
            //this.Tabll.setPage(cp);
            if (reset == "apply") {
                var df = $id("evefil-datefrom").value+":00";
                var dt = $id("evefil-dateto").value+":59";
                var url1 = this.url
                if (df != ":00") url1 += "/"+df;
                if (dt != ":59") url1 += "/"+dt;
                this.Table.setData(url1);
            } else if (reset == "today") {
                var df = (new Date()).format("isoDate")+"T00:00";
                var dt = (new Date()).format("isoDate")+"T23:59";
                $id("evefil-datefrom").value=df;
                $id("evefil-dateto").value=dt;
                df += ":00";
                dt += ":59";
                var url1 = this.url+"/"+df+"/"+dt;
                this.Table.setData(url1);
            } else this.Table.setData();
        },
        startup: function(){
            if (this.rendered) return;
            let meee = this;
            try {
                $('#mnuERefresh').dropdown();
                this.frmPage.setModel({
                    id: {caption: "ID", title: "No", type: "numeric", autoValue: true, width: 50},
                    log_type: {caption: "Log Type", type: "string", headerFilter: true, width: 100, editable:false, editor:'list', editorParams:{values:['ERR','EVNT','INFO','ORD','STAT']}, headerFilter:"list", headerFilterParams:{values:["ERR","EVNT","INFO","ORD","STAT"],clearable:true}},
                    app_type: {caption: "App Type", type:"string", headerFilter: true, width: 100, editable:false, editor:'list', editorParams:{values:['ADM','FIX','FTP']}, headerFilter:'list', headerFilterParams:{values:['ADM','FIX','FTP'],clearable:true}},
                    app_id: {caption: "App ID", type:"string", headerFilter: true, width: 100},
                    app_idx: {caption: "3rd Party", type:"string", formatter: fmt_appidx, width: 150},
                    data: {caption: "Data", type: "string", headerFilter: true, width: 200},
                    inserted_at: {caption: "Created at", type: "datetime", autoValue: true, width: 150},
                });
                var w0 = Math.ceil(window.innerHeight),
                    w1 = Math.ceil($("#menubar").outerHeight()),
                    w2 = $id("lehere").offsetHeight; //parseInt($id("leher-1").height),
                    //k1 = Math.ceil($("#kanan").outerHeight());
                //console.log([w0,w1,w2,k1]);
                const tinggi = w0-w1-50;
                var df = (new Date()).format("isoDate")+"T00:00";
                var dt = (new Date()).format("isoDate")+"T23:59";
                $id("evefil-datefrom").value=df;
                $id("evefil-dateto").value=dt;
                df += ":00";
                dt += ":59";
                var url1 = this.url+"/"+df+"/"+dt;
                this.Table = this.frmPage.xTabulator("table_event", tinggi, "cms_event", url1, {
                    initialSort:this.initialSort,
                    //initialHeaderFilter: [{field:"inserted_at", value:"<?=date("Y-m-d 00:00:00")?>"}]
                    layout: "fitData",
                });

                $("#btnERefresh").on("click", ()=>{ meee.refresh(); });
                $("#evefilBtnApply").on("click", ()=>{ meee.refresh("apply"); });
                $("#evefilBtnToday").on("click", ()=>{ meee.refresh("today"); });

                this.Table.on("rowSelected", function(row){
                    var data = row.getData();
                    //leftID = leftData.ID;
                    //console.log(data);
                    var de = $id("detail_event");
                    de.style.display = "block";
                    de.innerHTML = vjson(data,"data");
                });
                this.Table.on("rowDeselected", function(row){
                    //var data = row.getData();
                    //leftID = leftData.ID;
                    //console.log(data);
                    var de = $id("detail_event");
                    de.style.display = "none";
                    //de.innerHTML = vjson(data,"data");
                });
                this.rendered = true;
            } catch (err) {
                ToastError(err);
            }
        }
    };
</script>