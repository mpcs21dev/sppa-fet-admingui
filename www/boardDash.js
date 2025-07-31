class Config extends Common {
    /* {"logType":"METR","appId":"FIX","appType":"DBS","data":{
        "totalCpu":"5994.00","userPercent":"0.43%","systemPercent":"0.22%","idlePercent":"99.30%",
        "totalMemory":"8.2 GB","userMemory":"1.3 GB","systemMemory":"3.3 GB","idleMemory":"3.8 GB"}} */
    constructor(rec){
        super();
        this.id = rec.id;
        this.part_id = rec.participant_id.toUpperCase();
        this.part_name = rec.participant_name;
        this.rec_type = rec.record_type.toUpperCase();
        this.data = JSON.parse(rec.data);

        this.totalCpu = "";
        this.userPercent = "";
        this.systemPercent = "";
        this.idlePercent = "99%";

        this.totalMemory = "8 GB";
        this.userMemory = "";
        this.systemMemory = "";
        this.idleMemory = "7 GB";

        if (this.rec_type == "PART") {
            const dc = this.data.length;
            for (var i=0; i<dc; i++) {
                this.data[i].rfoRequest = 0;
                this.data[i].approved = 0;
                this.data[i].rejected = 0;
                this.data[i].trade = 0;
                this.data[i].error = 0;
                this.data[i].send = 0;
                this.data[i].rendered = false;
            }
        }

        this.service_name = (`sppafet-dev-${this.part_id}-net`).toLowerCase();
        this.service_port = "80";
        this.service_status = "N/A";
    }
    getCpuUsage(){
        const usage = 100.0 - parseFloat(this.idlePercent);
        return ""+usage.toFixed(2)+"%";
    }
    getMemUsage(percent=true){
        const usage = parseFloat(this.totalMemory) - parseFloat(this.idleMemory);
        const pusag = usage/parseFloat(this.totalMemory)*100;
        if (percent) {
            return ""+pusag.toFixed(2)+"%";
        } else {
            return ""+usage.toFixed(2)+" GB";
        }
    }
    updateMetr(j,origin=""){
        //const j = JSON.parse(data);
        if (j.appId != this.part_id) {
            console.log('updateMetr-skip',origin,j,this);
            return false;
        }
        const f = Object.keys(j.data);
        const x = f.length;
        for (var i=0; i<x; i++) {
            //if (f[i] == "id") continue;
            this[f[i]] = j.data[f[i]];
        }
        try {
            //console.log($id("usage_cpu_"+this.id));
            $id("usage_cpu_"+this.id).innerText = this.getCpuUsage();
            $id("usage_mem_"+this.id).innerText = this.getMemUsage();
        } catch(err) {
            console.log('updateMetr-error',origin,err,this);
        }
        //console.log(this.id);
        return true;
    }
    indexByClientId(cid) {
        var hasil = false;
        const x = this.data.length;
        for (var i=0; i<x; i++) {
            //this.data[f[i]] = j.data[f[i]];
            var m = this.data[i];
            if (cid == this.part_id+'-'+m.clientId) {
                hasil = i;
                break;
            }
        }
        return hasil;
    }
    updateConn(j,ix,origin=""){
        if (ix === false) return false;
        var m = this.data[ix];
        if (j.appId == this.part_id+'-'+m.clientId) {
            try {
                if (j.data.description == "FIX Client logon") {
                    $id("clientId_"+this.part_id+'-'+m.clientId).className = 'send';
                }
                if (j.data.description == "FIX Client logout") {
                    $id("clientId_"+this.part_id+'-'+m.clientId).className = 'error';
                }
                //console.log('updateConn-oke',origin,'clientId_'+this.part_id+'-'+m.clientId,j);
            } catch(e) {
                console.log('updateConn-error',origin,e,'clientId_'+this.part_id+'-'+m.clientId,j.data);
            }
            return true;
        }
        return false;
    }
    updateStat(j,ix,origin=""){
        //const j = JSON.parse(data);
        if (ix === false) return false;
        var m = this.data[ix];
        if (j.appId == this.part_id+'-'+m.clientId) {
            const k = Object.keys(j.data);
            const y = k.length;
            for (var l=0; l<y; l++) {
                this.data[ix][k[l]] = j.data[k[l]];
                try {
                    $id(k[l]+'_'+this.part_id+'-'+this.data[ix].clientId).innerText = j.data[k[l]];
                } catch(e) {
                    console.log('updateStat-error',origin,e,k[l]+'_'+this.part_id+'-'+this.data[ix].clientId,j.data[k[l]]);
                }
            }
            return true;
        }
        return false;
    }
    randColor() {
        // https://stackoverflow.com/questions/43193341/how-to-generate-random-pastel-or-brighter-color-in-javascript
        return "hsl(" + 360 * Math.random() + ',' +
                    (25 + 70 * Math.random()) + '%,' + 
                    (85 + 10 * Math.random()) + '%)';
    }
    render(upd=false) {
        if (this.rec_type == "PART") {
            let cli = "";
            const dc = this.data.length;
            for (var i=0; i<dc; i++) {
                if (upd) {
                    if (this.data[i].rendered) continue;
                }
                this.data[i].rendered = true;
                const rec = this.data[i];
                const recid = this.part_id+'-'+rec.clientId;
                cli += `
                    <div class='console'>
                        <div class='SInfo'>
                            <div><div>${rec.clientName}</div><div id="${this.genId('clientId',recid)}" class="error">${rec.clientId}</div></div>
                            <div class="SInfo-separator"></div>
                            <div><div id="${this.genId('rfoRequest',recid)}">${rec.rfoRequest}</div><div>RFO</div></div>
                            <div><div id="${this.genId('approved',recid)}">${rec.approved}</div><div>APPRV</div></div>
                            <div><div id="${this.genId('rejected',recid)}">${rec.rejected}</div><div>RJECT</div></div>
                            <div><div id="${this.genId('trade',recid)}">${rec.trade}</div><div>TRADE</div></div>
                            <div class="SInfo-separator-2"></div>
                            <div><div id="${this.genId('error',recid)}">${rec.error}</div><div class="error">ERROR</div></div>
                            <div><div id="${this.genId('send',recid)}">${rec.send}</div><div class="send">SEND</div></div>
                        </div>
                    </div>
                `;
            }
            return `<div class='boxserver' style='background:${this.randColor()};'>
                        <div class='caption'>
                            <span id="${this.genId('name')}">${this.part_name}</span> 
                            [<span id="${this.genId('service_name')}">${this.service_name}</span> : 
                            <span id="${this.genId('service_port')}">${this.service_port}</span>] 
                            <!-- <span id="${this.genId('service_status')}">${this.service_status}</span> -->
                        </div>
                        <div class='console'>
                            <div class="SInfo">
                                <button onclick="showTrx('${this.part_id}')">Transaction</button>
                                <button onclick="showEvent()">Event</button>
                                <div class="SInfo-separator"></div>
                                <!--
                                <div><div id="${this.genId('core')}">${this.cpu_core}</div><div>CORE</div></div>
                                <div><div id="${this.genId('speed')}">${this.cpu_speed}</div><div>SPEED</div></div>
                                <div><div id="${this.genId('ram')}">${this.ram}</div><div>RAM</div></div>
                                <div><div id="${this.genId('disk')}">${this.disk}</div><div>DISK</div></div>
                                -->
                                <div class="SInfo-separator-2"></div>
                                <div><div id="${this.genId('usage_cpu')}">${this.getCpuUsage()}</div><div class="cpu">CPU</div></div>
                                <div><div id="${this.genId('usage_mem')}">${this.getMemUsage()}</div><div class="mem">MEM</div></div>
                            </div>
                        </div>
                        <!--
                        <div class='console'>
                            <div class='SInfo'>
                                <button>Message</button>
                                <button>Event</button>
                            </div>
                        </div>
                        -->
                        ${cli}
                    </div>
                `;
        } else {
            return `<div class='boxserver' style='background:${this.randColor()};'>
                        <div class='caption'>
                            <span id="${this.genId('name')}">${this.part_id}</span> 
                            [<span id="${this.genId('service_name')}">${this.service_name}</span> : 
                            <span id="${this.genId('service_port')}">${this.service_port}</span>] 
                            <span id="${this.genId('service_status')}">${this.service_status}</span>
                        </div>
                        <div class='console'>
                            <div class="SInfo">
                                <!--
                                <button>Message</button>
                                <button>Event</button>
                                -->
                                <div class="SInfo-separator"></div>
                                <!--
                                <div><div id="${this.genId('core')}">${this.cpu_core}</div><div>CORE</div></div>
                                <div><div id="${this.genId('speed')}">${this.cpu_speed}</div><div>SPEED</div></div>
                                <div><div id="${this.genId('ram')}">${this.ram}</div><div>RAM</div></div>
                                <div><div id="${this.genId('disk')}">${this.disk}</div><div>DISK</div></div>
                                -->
                                <div class="SInfo-separator-2"></div>
                                <div><div id="${this.genId('usage_cpu')}">${this.getCpuUsage()}</div><div class="cpu">CPU</div></div>
                                <div><div id="${this.genId('usage_mem')}">${this.getMemUsage()}</div><div class="mem">MEM</div></div>
                            </div>
                        </div>
                    </div>
            `;
        }
    }
}

class ConBox {
    constructor(){
        this.box = [];
    }
    count(){return this.box.length}
    addConfig(row){
        const cfg = new Config(row);
        this.box.push(cfg);
    }
    configById(id){
        let ide = false;
        this.box.forEach((lmn,ix) => {
            if (lmn.id == id) ide = ix;
        });
        return ide;
    }
    configByName(name){
        let ide = false;
        this.box.forEach((lmn,ix) => {
            if (lmn.part_id == name) ide = ix;
        });
        return ide;
    }
    updateMetr(j,origin=""){
        //const j = JSON.parse(data);
        const ix = this.configByName(j.appId);
        if (ix !== false) {
            //this.box[ix].updateMetr(j);
            try {
                this.box[ix].updateMetr(j,origin);
                //$id("usage_cpu_"+this.box[ix].id).innerText = this.box[ix].getCpuUsage();
                //$id("usage_mem_"+this.box[ix].id).innerText = this.box[ix].getMemUsage();
            } catch(err) {
                console.log("ERROR-updateMetr",origin,{index:ix,data:j,error:err});
            }
        } else {
            console.log("SKIP-updateMetr",origin,ix,j);
        }
    }
    updateStat(j,origin=""){
        //const j = JSON.parse(data);
        const x = this.box.length;
        for (var i=0; i<x; i++) {
            let ix = this.box[i].indexByClientId(j.appId);
            if (ix !== false) {
                return this.box[i].updateStat(j,ix,origin);
                //break;
            }
        }
        return false;
    }
    updateConn(j,origin=""){
        //const j = JSON.parse(data);
        const x = this.box.length;
        for (var i=0; i<x; i++) {
            let ix = this.box[i].indexByClientId(j.appId);
            if (ix !== false) {
                return this.box[i].updateConn(j,ix,origin);
                //break;
            }
        }
        return false;
    }
    lastIndex(){return this.box.length-1}
    /*
    render(id){
        let lmn = $id(id);
        this.box.forEach((b,ix) => {
            let div = document.createElement("div");
            div.innerHTML = b.render();
            lmn.appendChild(div.firstChild);
        });
    }
    */
    render(){
        let hasil = "";
        this.box.forEach((b,ix) => {
            //let div = document.createElement("div");
            //div.innerHTML = b.render();
            //lmn.appendChild(div.firstChild);
            hasil += b.render();
        });
        return hasil;
    }
}
/*
class Server extends Common {
    constructor(arr) {
        super();
        this.id = hashCode(arr[1]);
        [
            this.idx,
            this.name, 
            this.ip, 
            this.service_name, 
            this.service_port,
            this.service_status,
            this.cpu_core, 
            this.cpu_speed, 
            this.ram, 
            this.disk, 
            this.usage_cpu, 
            this.usage_ram,
            this.count_error,
            this.count_send,
            this.count_quote,
            this.count_resp,
            this.count_reject,
            this.count_trade
        ] = arr;
    }
    randColor() {
        // https://stackoverflow.com/questions/43193341/how-to-generate-random-pastel-or-brighter-color-in-javascript
        return "hsl(" + 360 * Math.random() + ',' +
                    (25 + 70 * Math.random()) + '%,' + 
                    (85 + 10 * Math.random()) + '%)';
    }
    render() {
        //const clr = svr.type == "ftp" ? "yellow" : "ungu";
        return `<div class='boxserver' style='background:${this.randColor()};'>
                    <div class='caption'>
                        <span id="${this.genId('name')}">${this.name}</span> 
                        [<span id="${this.genId('service_name')}">${this.service_name}</span> : 
                        <span id="${this.genId('service_port')}">${this.service_port}</span>] 
                        <span id="${this.genId('service_status')}">${this.service_status}</span>
                    </div>
                    <div class='console'>
                        <div class="SInfo">
                            <!--
                            <div><div id="${this.genId('core')}">${this.cpu_core}</div><div>CORE</div></div>
                            <div><div id="${this.genId('speed')}">${this.cpu_speed}</div><div>SPEED</div></div>
                            <div><div id="${this.genId('ram')}">${this.ram}</div><div>RAM</div></div>
                            <div><div id="${this.genId('disk')}">${this.disk}</div><div>DISK</div></div>
                            -->
                            <div class="SInfo-separator"></div>
                            <div><div id="${this.genId('usage_cpu')}">${this.usage_cpu}</div><div class="cpu">CPU</div></div>
                            <div><div id="${this.genId('usage_ram')}">${this.usage_ram}</div><div class="mem">MEM</div></div>
                        </div>
                    </div>
                    <div class='console'>
                        <div class='SInfo'>
                            <button>Message</button>
                            <button>Event</button>
                        </div>
                    </div>
                    <div class='console'>
                        <div class='SInfo'>
                            <div><div id="${this.genId('count_quote')}">${this.count_quote}</div><div>RFO</div></div>
                            <div><div id="${this.genId('count_resp')}">${this.count_resp}</div><div>RESP</div></div>
                            <div><div id="${this.genId('count_reject')}">${this.count_reject}</div><div>RJCT</div></div>
                            <div><div id="${this.genId('count_trade')}">${this.count_trade}</div><div>TRADE</div></div>
                            <div class="SInfo-separator"></div>
                            <div><div id="${this.genId('count_error')}">${this.count_error}</div><div class="error">ERROR</div></div>
                            <div><div id="${this.genId('count_send')}">${this.count_send}</div><div class="send">SEND</div></div>
                        </div>
                    </div>
                </div>
            `;
    }
}

class AServer {
    constructor() {
        this.svr = [];
    }
    count() {
        return this.svr.length;
    }
    getDS() {
        var hasil = [];
        for (var i=0; i<this.svr.length; i++) {
            hasil.push({
                id: this.svr[i].idx,
                participant_id: this.svr[i].name,
                service_name: this.svr[i].service_name,
                service_port: this.svr[i].service_port,
                status: this.svr[i].status
            });
        }
        return hasil;
    }
    addServer(arr) {
        let obj = new Server(arr);
        this.svr.push(obj);
        return this;
    }
    serverByName(name) {
        let idx = false;
        this.svr.forEach((lmn,ix) => {
            if (lmn.name == name) idx = ix;
        });
        return idx;
    }
    serverById(id) {
        let ide = false;
        this.svr.forEach((lmn,ix) => {
            if (lmn.id == id) ide = ix;
        });
        return ide;
    }
    serverByIdx(idx) {
        let ide = false;
        this.svr.forEach((lmn,ix) => {
            if (lmn.idx == idx) ide = ix;
        });
        return ide;
    }
    serverLastIndex() {
        return this.svr.length - 1;
    }
    render(id) {
        let html = "";
        for (let i = 0, len = this.svr.length; i < len; i++) {
            html += this.svr[i].render();
        }                
        document.getElementById(id).innerHTML = html;
    }
}
*/    
Dash = {
    ID: 1,
    RootID: 'dashBoard',
    Data: [],
    Ses: new ConBox(),
    readStat: function(){
        Api("api/?1/config/stat/listall").then(oke => {
            if (oke.error == 0) {
                for (var i=0; i<oke.data.length; i++) {
                    let dx = oke.data[i];
                    const appid = dx.appId;
                    delete dx["id"];
                    delete dx["appId"];
                    delete dx["lastUpdate"];
                    const dk = Object.keys(dx);
                    for (var k=0; k<dk.length; k++) {
                        try {
                            $id(dk[k]+'_'+appid).innerText = dx[dk[k]];
                        } catch(e) {
                            console.log('updateStat-error',e,dk[k],dx[k]);
                        }
                    }
                }
            } else {
                ToastError("Error readng connection table");
                console.log(oke);
            }
        }, err => {
            ToastError("Connection Status: Server Error");
            console.log({status: "Connection Status: Server Error", detail: err});
        });
    },
    readConn: function(){
        Api("api/?1/config/fix/listall").then(oke => {
            if (oke.error == 0) {
                for (var i=0; i<oke.data.length; i++) {
                    try {
                        $id("clientId_"+oke.data[i].appId).className = oke.data[i].login == "1" ? 'send':'error';
                    } catch(e) {
                        console.log('updateConn-error',e,'clientId_'+this.appId,oke.data[i]);
                    }
                }
            } else {
                ToastError("Error readng connection table");
                console.log(oke);
            }
        }, err => {
            ToastError("Connection Status: Server Error");
            console.log({status: "Connection Status: Server Error", detail: err});
        });
    },
    render_wrap: function(html) {
        let div = document.createElement("div");
        div.id = "sesgrid";
        div.className = 'session-grid';
        div.innerHTML = html;
        //console.log([this.RootID, $id(this.RootID), this]);
        $id(this.RootID).appendChild(div);
    },
    render: function(upd = false) {
        let selfDash = this;
        Api("api/?1/config/config/listall").then(
            data => {
                if (data.error == 0) {
                    if (data.data != null) {
                        if (Array.isArray(data.data)) {
                            this.Data = data.data;
                            for (var i=0; i<data.data.length; i++) {
                                var d = data.data[i];
                                this.Ses.addConfig(d);
                            }
                            /*
                            SES.addServer([2,"BCA-JKT","","fet-bca-jkt","9900","LIVE","4","2.0GHz","16GB","120GB","60%","30%",0,0,100,50,0,25]);
                            SES.addServer([3,"OCBC-JKT","","fet-ocbc-jkt","9900","LIVE","4","2.0GHz","16GB","120GB","60%","30%",0,0,100,50,0,25]);
                            SES.addServer([4,"SUCOR-JKT","","fet-sucor-jkt","9900","LIVE","4","2.0GHz","16GB","120GB","60%","30%",0,0,100,50,0,25]);
                            SES.addServer([5,"CGS-JKT","","fet-cgs-jkt","9900","LIVE","4","2.0GHz","16GB","120GB","60%","30%",0,0,100,50,0,25]);
                            SES.addServer([6,"TRIM-JKT","","fet-trim-jkt","9900","LIVE","4","2.0GHz","16GB","120GB","60%","30%",0,0,100,50,0,25]);
                            */
                            //this.Ses.render("sesgrid");
                            this.render_wrap(this.Ses.render(upd));
                        } else this.Data=[];
                    } else this.Data=[];
                } else {
                    //SwalToast('error','Error Get Challange');
                    ToastError("Error readng config table");
                    console.log(data);
                }
                setTimeout(selfDash.readConn,500);
                setTimeout(selfDash.readStat,1500);
            },
            error => {
                //SwalToast('error','Get Challange: Server Error');
                ToastError("Config List: Server Error");
                console.log({status: "Config List: Server Error", detail: error});
            }
        );
    },
    startup: function() {
        this.render();
    }
}

function showTrx(partid) {
    Task.Show('ttrx');
    Trx.Tabll.setHeaderFilterValue("participant",partid);
}
function showEvent() {
    Task.Show('tevent');
}