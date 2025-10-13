TSync = {
    interval: 4040,
    active: true,
    lastId: 0,
    syncUrl: "api/?1/wsc/wsc/latest",
    metrUrl: "api/?1/wsc/metric/latest",
    connUrl: "api/?1/wsc/connection/latest",
    statUrl: "api/?1/wsc/stat/latest",
    xbj: null,
    xdate: "",
    IID: null,
    table_active: "_log",
    Table_log: null, Table_box: null, Table_login: null, Table_stat: null, Table_err: null,
    frm_wsc_log: new Formation(), frm_wsc_box: new Formation(), frm_wsc_login: new Formation(), frm_wsc_stat: new Formation(), frm_wsc_err: new Formation(),
    url_wsc_log:   "api/?1/wsc/log/latest",
    url_wsc_box:   "api/?1/wsc/metric/latest",
    url_wsc_login: "api/?1/wsc/connection/latest",
    url_wsc_stat:  "api/?1/wsc/stat/latest",
    url_wsc_err:   "api/?1/wsc/err/latest",
    init_log: false, init_box: false, init_login: false, init_stat: false, init_err: false,
    info: function(data) {
        var obj = {};
        obj["active"] = this.active;
        obj["interval"] = this.interval;
        obj["lastID"] = this.lastId;
        obj["data"] = data;
        obj["timestamp"] = this.xdate;
        return JSON.stringify(obj, null, 2);
    },
    render: function() {
        var div1 = document.createElement("div"); div1.className = "sync_outer_box";
        var html = `
            <div id="sync_left" class="ui red table sync_box">
                <div><div>sync active</div><div><input type="checkbox" id="sync_active"></div></div>
                <div><div>interval</div><div><input type="number" id="sync_interval"></div></div>
                <div><div>lastId</div><div><input type="number" id="sync_lastId"></div></div>
                <fieldset class="grow">
                    <legend>Sync Info</legend>
                    <pre id="syncInfo"></pre>
                </fieldset>
                <fieldset>
                    <legend>Sync Error</legend>
                    <pre id="errInfo"></pre>
                </fieldset>
            </div>
            <div id="sync_right" class="ui blue table sync_box">
                <div id="sync_right_tool">
                    <select id="sync_table" style="flex:1 0 0;">
                        <option value="_log" selected>wsc_log</option>
                        <option value="_box">wsc_box</option>
                        <option value="_login">wsc_login</option>
                        <option value="_stat">wsc_stat</option>
                        <option value="_err">wsc_err</option>
                    </select>
                    <button id="btnSyncRefresh">Refresh</button>
                    <button id="btnSyncView">View</button>
                </div>
                <div id="sync_grid_log"></div>
                <div id="sync_grid_box" class="board"></div>
                <div id="sync_grid_login" class="board"></div>
                <div id="sync_grid_stat" class="board"></div>
                <div id="sync_grid_err" class="board"></div>
            </div>
        `;
        div1.innerHTML = html;
        $id("syncBoard").appendChild(div1);

    },
    fillControl: function(id="all"){
        if (id=="all") {
            $id("sync_active").checked = this.active;
            $id("sync_interval").value = this.interval;
            $id("sync_lastId").value = this.lastId;
        } else {
            if (id=="active")
                $id("sync_"+id).checked = this[id];
            else
                $id("sync_"+id).value = this[id];
        }
    },
    readControl: function(id="all"){
        //console.log("readControl", id, $id("sync_active").checked);
        if (!this.active && $id("sync_active").checked) this.doit();
        if (id=="all") {
            this.active = $id("sync_active").checked;
            this.interval = $id("sync_interval").value;
            this.lastId = $id("sync_lastId").value;
        } else {
            if (id=="active")
                this[id] = $id("sync_"+id).checked;
            else
                this[id] = $id("sync_"+id).value;
        }
    },
    changeIcon: function(reset=false){
        if (Task.Active != 'tsync') {
            $id('icon_sync').className = 'red bug icon';
        }
        if (reset) {
            $id('icon_sync').className = 'sync icon';
        }
    },
    doit: function(id=0) {
        if (id>0) this.lastId = id;
        self = this;
        function doit_repeat() {
            Api(self.syncUrl+'/'+self.lastId).then(
                data => {
                    if (data.error == 0) {
                        //console.log(data);
                        //$id('syncInfo').innerText = self.info(data);
                        //let dats = {};
                        if (Array.isArray(data.diskFree)) {
                            $id("devshm").innerText = data.diskFree[3];
                            $id("devshm").dataset.title = "MEM-DB";
                            $id("devshm").dataset.content = "[LIMIT = "+data.diskFree[1]+"] "+
                                "[USED = "+data.diskFree[2]+"] "+
                                "[FREE = "+data.diskFree[3]+"]";
                            $('#devshm').popup();
                        }
                        let rtrx = false;
                        let reve = false;
                        const x = data.data.length;
                        for (var i=0; i<x; i++) {
                            const row = data.data[i];
                            try {
                                self.xbj = JSON.parse(row.msg);
                            } catch(err) {
                                continue;
                            }

                            if (self.xbj.logType == "METR") {
                                Dash.Ses.updateMetr(self.xbj,"doit");
                            }
                            if (self.xbj.logType == "STAT") {
                                Dash.Ses.updateStat(self.xbj,"doit");
                            }
                            if (self.xbj.logType == "EVNT" && self.xbj.appType == "FIX" && self.xbj.data.description.substring(0,15) == "FIX Client logon") {
                                Dash.Ses.updateConn(self.xbj,"doit");
                                //console.log(JSON.parse(row.msg));
                            }
                            if ((self.xbj.logType=='INFO') && (self.xbj.appType=='FTP') && (self.xbj.appId=='FTP-XML')) rtrx = true;
                            if ((self.xbj.logType!='METR') && (self.xbj.logType!='STAT') && (self.xbj.logType!='WSC')) reve = true;

                            //FTP STAT --> log_type='INFO' app_type='FTP' app_id='FTP-STAT'

                            self.lastId = row.id;
                        }
                        if (reve) XEvent.refresh();
                        if (rtrx) Trx.leftRefresh();
                        self.xdate = (new Date()).format("localShortTime");
                        $id('syncInfo').innerText = self.info(self.xbj);
                        self.fillControl();
                    } else {
                        //FError("Fetch event failed", data.message);
                        let obj = {
                            error: 'Fetch socket failed',
                            message: data.message,
                            date: (new Date()).format("localShortTime")
                        };
                        $id('errInfo').innerText = JSON.stringify(obj,null,2);
                        self.changeIcon();
                    }
                    if (self.active) {
                        setTimeout(doit_repeat, self.interval);
                    }
                },
                error => {
                    //FError("Fetch event error", error);
                    let obj = {
                        error: 'Fetch socket error',
                        message: error,
                        date: (new Date()).format("localShortTime")
                    };
                    $id('errInfo').innerText = JSON.stringify(obj,null,2);
                    self.changeIcon();
                }
            );
        }
        doit_repeat();
    },
    storedStat: function(){
        Api(this.statUrl).then(oke => {
            if (oke.error == 0) {
                for (var i=0; i<oke.data.length; i++) {
                    let dx = oke.data[i];
                    const appid = dx.appId;
                    if (appid == "FTP-STAT") {
                        var fix = Dash.Ses.configByName('FTP');
                        var fid = Dash.Ses.box[fix]["id"];
                        try {
                            $id("rfo_request_"+fid).innerText = dx["rfoRequest"];
                            $id("rfo_valid_"+fid).innerText = dx["send"];
                            $id("rfo_failure_"+fid).innerText = dx["error"];
                        } catch (err) {
                            console.log({err,dx});
                        }
                        continue;
                    }
                    delete dx["id"];
                    delete dx["appId"];
                    delete dx["lastUpdate"];
                    const dk = Object.keys(dx);
                    for (var k=0; k<dk.length; k++) {
                        if (dk[k] == "send") continue;
                        try {
                            var dval = dx[dk[k]] ?? "";
                            dval = dval == "" ? 0 : dval;
                            if (dval == "") dval = 0;
                            var dnod = $id(dk[k]+'_'+appid);
                            if (dnod) dnod.innerText = dval;
                        } catch(e) {
                            console.log('updateStat-error',e,dk[k],dx[dk[k]]);
                        }
                    }
                }
            } else {
                ToastError("Error reading stat table");
                console.log(oke);
            }
        }, err => {
            ToastError("Client Stat: Server Error");
            console.log({status: "Client Stat: Server Error", detail: err});
        });
    },
    storedConnection: function(){
        Api(this.connUrl).then(
            oke => {
                if (oke.error == 0) {
                    for (var i=0; i<oke.data.length; i++) {
                        try {
                            $id("clientId_"+oke.data[i].appId).className = oke.data[i].login == "1" ? 'send':'error';
                        } catch(e) {
                            console.log('updateConn-error',e,'clientId_'+this.appId,oke.data[i]);
                        }
                    }
                } else {
                    let obj = {
                        error: 'Fetch previous values failed',
                        message: oke.message,
                        date: (new Date()).format("localShortTime")
                    };
                    $id('errInfo').innerText = JSON.stringify(obj,null,2);
                    this.changeIcon();
                }
            },
            err => {
                let obj = {
                    error: 'Fetch prev values error',
                    message: err,
                    date: (new Date()).format("localShortTime")
                };
                $id('errInfo').innerText = JSON.stringify(obj,null,2);
                this.changeIcon();
            }
        );
    },
    storedMetric: function(){
        self = this;
        Api(self.metrUrl).then(
            data => {
                if (data.error == 0) {
                    //console.log(data);
                    for (var i=0; i<data.count; i++) {
                        let row = data.data[i];
                        let obj = {};
                        obj["appId"] = row["appId"];
                        delete row["appId"];
                        delete row["id"];
                        delete row["lastUpdate"];                            
                        obj["data"] = row;
                        //console.log(obj);
                        Dash.Ses.updateMetr(obj,"currentBox");
                    }
                } else {
                    //FError("Fetch latest metrics failed", data.message);
                    let obj = {
                        error: 'Fetch previous values failed',
                        message: data.message,
                        date: (new Date()).format("localShortTime")
                    };
                    $id('errInfo').innerText = JSON.stringify(obj,null,2);
                    self.changeIcon();
                }
            },
            error => {
                //FError("Fetch latest metrics error", error);
                let obj = {
                    error: 'Fetch prev values error',
                    message: error,
                    date: (new Date()).format("localShortTime")
                };
                $id('errInfo').innerText = JSON.stringify(obj,null,2);
                self.changeIcon();
            }
        );
    },
    startup: function() {
        this.interval = SyncInterval;
        this.render();
        this.fillControl();
        
        $("#sync_active").on("click", ()=>{this.readControl("active")});
        $("#sync_interval").on("input", ()=>{this.readControl("interval")});
        $("#sync_lastId").on("input", ()=>{this.readControl("lastId")});

        $("#btnSyncRefresh").on("click", ()=>{
            var xval = $("#sync_table").dropdown("get value");
            if (!this["init"+xval]) {
                const tinggi = $id("sync_right").offsetHeight - $id("sync_right_tool").offsetHeight;
                this["Table"+xval] = this["frm_wsc"+xval].xTabulator("sync_grid"+xval, tinggi, "sync_table"+xval, this["url_wsc"+xval]);
                this["init"+xval] = true;
            } else {
                this["Table"+xval].getData();
            }
        });

        $("#btnSyncView").on("click", ()=>{
            var xval = $("#sync_table").dropdown("get value");
            var xtex = $("#sync_table").dropdown("get text");
            var sel = (this["Table"+xval].getSelectedData())[0]; // get first selected element
            if (sel == undefined) {
                ToastError("No row selected");
                return;
            }
            var parm = {id: sel.id};
            XFrame.setCaption(xtex).setContent(this["frm_wsc"+xval].viewCard(parm)).setAction(false).show(false);
        });

        this.fillControl();
        var self = this;
        $("#sync_table").dropdown({
            onChange: function(xval,tex,sel){
                $id("sync_grid"+self.table_active).style.display = "none";
                $id("sync_grid"+xval).style.display = "block";
                self.table_active = xval;

                const tinggi = $id("sync_right").offsetHeight - $id("sync_right_tool").offsetHeight;
                if (!self["init"+xval]) {
                    self["Table"+xval] = self["frm_wsc"+xval].xTabulator("sync_grid"+xval, tinggi, "sync_table"+xval, self["url_wsc"+xval]);
                    self["init"+xval] = true;
                }
            }
        });
        this.frm_wsc_log.setModel({
            id: {caption: "ID", title: "No", type: "numeric", autoValue: true, formatter: autoNumFormatter},
            msg: {caption: "Message", type: "string"},
            tgl: {caption: "Last Update", type: "datetime"}
        });
        this.frm_wsc_box.setModel({
            id: {caption: "ID", title: "No", type: "numeric", autoValue: true, formatter: autoNumFormatter},
            appId: {caption: "AppID", type: "string"},
            totalCpu: {caption: "CPU", type: "string"},
            userPercent: {caption: "CPU User", type: "string"},
            systemPercent: {caption: "CPU Sys", type: "string"},
            idlePercent: {caption: "CPU Idle", type: "string"},
            totalMemory: {caption: "RAM", type: "string"},
            userMemory: {caption: "RAM User", type: "string"},
            systemMemory: {caption: "RAM Sys", type: "string"},
            idleMemory: {caption: "RAM Idle", type: "string"},
            lastUpdate: {caption: "Last Update", type: "datetime"}
        });
        this.frm_wsc_login.setModel({
            id: {caption: "ID", title: "No", type: "numeric", autoValue: true, formatter: autoNumFormatter},
            appId: {caption: "AppID", type: "string"},
            login: {caption: "Login", type: "numeric"},
            lastUpdate: {caption: "Last Update", type: "datetime"}
        });
        this.frm_wsc_stat.setModel({
            id: {caption: "ID", title: "No", type: "numeric", autoValue: true, formatter: autoNumFormatter},
            appId: {caption: "AppID", type: "string"},
            rfoRequest: {caption: "RFO", type: "numeric"},
            approved: {caption: "Approved", type: "numeric"},
            rejected: {caption: "Rejected", type: "numeric"},
            initiator: {caption: "Initiator", type: "numeric"},
            trade: {caption: "Trade", type: "numeric"},
            error: {caption: "Error", type: "numeric"},
            send: {caption: "Send", type: "numeric"},
            tgl: {caption: "Last Update", type: "datetime"}
        });
        this.frm_wsc_err.setModel({
            id: {caption: "ID", title: "No", type: "numeric", autoValue: true, formatter: autoNumFormatter},
            msg: {caption: "Message", type: "string"},
            sql: {caption: "SQL", type: "string"},
            prm: {caption: "Params", type: "string"},
            tgl: {caption: "Last Update", type: "datetime"}
        });

        this.storedMetric();
        this.storedStat();
        this.storedConnection();
    }
};
