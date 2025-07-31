<div id="syncBoard" class="board">
    <pre id="syncInfo"></pre>
    <pre id="errInfo"></pre>
</div>
<script type="text/javascript">
    TSync = {
        interval: 3000,
        active: true,
        lastId: 0,
        syncUrl: "api/?1/wsc/wsc/latest",
        metrUrl: "api/?1/wsc/metric/latest",
        connUrl: "api/?1/wsc/connection/latest",
        xbj: null,
        xdate: "",
        IID: null,
        info: function(data) {
            var obj = {};
            obj["active"] = this.active;
            obj["interval"] = this.interval;
            obj["lastID"] = this.lastId;
            obj["data"] = data;
            obj["timestamp"] = this.xdate;
            return JSON.stringify(obj, null, 2);
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
                                if (self.xbj.logType == "EVNT" && self.xbj.appType == "FIX" && self.xbj.data.description == "FIX Client logon") {
                                    Dash.Ses.updateConn(self.xbj,"doit");
                                    //console.log(JSON.parse(row.msg));
                                }
                                if ((self.xbj.logType=='INFO') && (self.xbj.appType=='FTP') && (self.xbj.appId=='FTP-XML')) rtrx = true;
                                if ((self.xbj.logType!='METR') && (self.xbj.logType!='STAT')) reve = true;
                                self.lastId = row.id;
                            }
                            if (reve) XEvent.refresh();
                            if (rtrx) Trx.leftRefresh();
                            self.xdate = (new Date()).format("localShortTime");
                            $id('syncInfo').innerText = self.info(self.xbj);
                            if (self.active) {
                                setTimeout(doit_repeat, self.interval);
                            }
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
        clientConn: function(){
            Api(this.connUrl).then(
                oke => {
                    if (oke.error == 0) {
                        //
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
        currentBox: function(){
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
        }
    };
</script>