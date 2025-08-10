<div id="eventBoard" class="board">
    <div id="lehere" class="">
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
        <!--
        <div class="padder2"></div>
        <div class="ui buttons">
            <button class="ui mini blue button" id="btnEView"><i class="eye outline icon"></i> View</button>
        </div>
        -->
    </div>
    <div id="badane" class="attached standard-full flexrow">
        <div id="table_event" class="ui orange table"></div>
        <div id="detail_event" class="ui violet table" style="display:none"></div>
    </div>
</div>
<script type="text/javascript">
    XEvent = {
        Table: null,
        MenuID: 'mnu-event',
        frmPage: new Formation(),
        initialFilter: [{field:'',type:'',value:''}],
        initialSort: [{column:'id',dir:'desc'}],
        lastData: null,
        lastID: 0,
        url: 'api/?1/wsc/event/latest',
        refresh: function() {
            //var cp = this.Tabll.getPage();
            //this.Tabll.setPage(cp);
            this.Table.setData();
        },
        startup: function(){
            meee = this;
            $('#mnuERefresh').dropdown();
            this.frmPage.setModel({
                id: {caption: "ID", title: "No", type: "numeric", autoValue: true},
                log_type: {caption: "Log Type", type: "string", headerFilter: true},
                app_type: {caption: "App Type", type:"string", headerFilter: true},
                app_id: {caption: "App ID", type:"string", headerFilter: true},
                data: {caption: "Data", type: "string", headerFilter: true},
                inserted_at: {caption: "Created at", type: "datetime", autoValue: true, headerFilter:"input", headerFilterFunc:">="},
            });
            var w0 = Math.ceil(window.innerHeight),
                w1 = Math.ceil($("#menubar").outerHeight()),
                w2 = $id("lehere").offsetHeight; //parseInt($id("leher-1").height),
                //k1 = Math.ceil($("#kanan").outerHeight());
            //console.log([w0,w1,w2,k1]);
            const tinggi = w0-w1-50;
            this.Table = this.frmPage.xTabulator("table_event", tinggi, "cms_event", this.url, {
                initialSort:this.initialSort,
                initialHeaderFilter: [{field:"inserted_at", value:"<?=date("Y-m-d 00:00:00")?>"}]
            });

            $("#btnERefresh").on("click", ()=>{ meee.refresh(); });

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
        }
    };
</script>