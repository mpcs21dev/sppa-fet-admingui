class Seq {
    constructor(start=0) { this.value = start; }
    next() { return ++this.value; }
    last() { return this.value; }
}

class Common {
    constructor() { this.id = ""; }
    genId(tid,xid="") { return `${tid}_${xid==""?this.id:xid}`; }
}

class Queue {
    constructor() {this.items = [];}
    enq(element) {this.items.push(element);}
    deq() {return this.isEmpty() ? null : this.items.shift();}
    peek() {return this.isEmpty() ? null : this.items[0];}
    isEmpty() {return this.items.length === 0;}
    size() {return this.items.length;}
}

function xml2obj(xstr) {
    const parser = new DOMParser();
    const xd = parser.parseFromString(xstr, "text/xml");
    const ids = xd.getElementsByTagName("Id");
    const val = xd.getElementsByTagName('Value');
    let obj = {};
    for (var i=0,x=ids.length; i<x; i++) {
        obj[ids[i].textContent] = val[i].firstChild.textContent;
    }
    //console.log(obj);
    return obj;
}
function createTable(data, lmn) {
    const table = document.createElement('table');
    const tableHead = document.createElement('thead');
    const tableBody = document.createElement('tbody');

    // Append the table head and body to table
    table.appendChild(tableHead);
    table.appendChild(tableBody);

    // Creating table head
    let row = tableHead.insertRow();
    Object.keys(data[0]).forEach(key => {
        let th = document.createElement('th');
        th.textContent = key.toUpperCase();
        row.appendChild(th);
    });

    // Creating table body
    data.forEach(item => {
        let row = tableBody.insertRow();
        Object.values(item).forEach(value => {
        let cell = row.insertCell();
        cell.textContent = value;
        });
    });

    // Append the table to the HTML document
    document.getElementById(lmn).appendChild(table);
}

function vjson(data,arj){
    var xd = JSON.parse(JSON.stringify(data));
    try {
        xd[arj] = JSON.parse(data[arj]);
    } catch(e) {
        xd[arj] = data[arj];
    }
    return jsonToHTMLTable(xd,'vertical');
}

function vxml(data,arj) {
    var xd = JSON.parse(JSON.stringify(data));
    try {
        xd[arj] = xml2obj(data[arj]); //JSON.parse(data[arj]);
    } catch(e) {
        xd[arj] = data[arj];
    }
    return jsonToHTMLTable(xd,'vertical');
}

function jsonToHTMLTable(xjson,headingType,mode="mixed",tableClass="mvtable"){
    let parsedJson = xjson;
    if (!Array.isArray(xjson)) parsedJson = [xjson];
    var tableHeaders = new Array();
    if(headingType == "horizontal"){
        for(var i = 0 ;i < parsedJson.length ; i++){
            for( var j = 0 ; j < Object.keys(parsedJson[i]).length ; j++){
                if(tableHeaders.indexOf(Object.keys(parsedJson[i])[j]) == -1 )
                    tableHeaders.push(Object.keys(parsedJson[i])[j]);
            }
        }
        var headersHtml = "<tr>";

        for( var k = 0; k < tableHeaders.length ; k++){
            var fld = tableHeaders[k];
            if (mode=="camel") fld = capitalizeWords(camelCaseSpacer(tableHeaders[k]));
            if (mode=="underscore") fld = capitalizeWords(undescoreSpace(tableHeaders[k]));
            headersHtml += "<th>"+fld+"</th>";
        }
        headersHtml+="</tr>";

        var rows="";
        for(var l = 0 ;l < parsedJson.length ; l++){
            rows += "<tr>";
            for( var m = 0 ;m < tableHeaders.length ; m++){
                if(typeof parsedJson[l][tableHeaders[m]] == 'undefined')
                    rows += "<td></td>";
                else
                rows += "<td>"+  parsedJson[l][tableHeaders[m]]  +"</td>";
            }
            rows += "</tr>";
        }

        var horizontal_table= `<table class="${tableClass}">${headersHtml}${rows}</table>`;
    return horizontal_table;
    }
    else if(headingType == "vertical"){
        for(var i = 0 ;i < parsedJson.length ; i++){
            for( var j = 0 ; j < Object.keys(parsedJson[i]).length ; j++){
                if(tableHeaders.indexOf(Object.keys(parsedJson[i])[j]) == -1 )
                    tableHeaders.push(Object.keys(parsedJson[i])[j]);
            }
        }

        var rows="";
        for( var k = 0 ;k < tableHeaders.length ; k++){
            rows += "<tr>";
            for(var l = 0 ;l < parsedJson.length ; l++){
                if(l == 0) {
                    var fld = tableHeaders[k];
                    if (mode=="camel") fld = capitalizeWords(camelCaseSpacer(tableHeaders[k]));
                    if (mode=="underscore") fld = capitalizeWords(undescoreSpace(tableHeaders[k]));
                    if (mode=="mixed") fld = capitalizeWords(camelCaseSpacer(undescoreSpace(tableHeaders[k])));
                    rows += "<th>"+fld+"</th>";
                }
                if(typeof parsedJson[l][tableHeaders[k]] == 'undefined')
                    rows += "<td></td>";
                else {
                    var content = parsedJson[l][tableHeaders[k]];
                    if (Array.isArray(content)) content = jsonToHTMLTable(content,'vertical');
                    if (content instanceof Object) content = jsonToHTMLTable(content,'vertical');
                    rows += "<td>"+  content +"</td>";
                }
            }
            rows += "</tr>";
        }

        var vertical_table= `<table class="${tableClass}">${rows}</table>`;
    return vertical_table;
    }
}

/*
class Process extends Common {
    constructor(arr) {
        super();
        this.id = hashCode(arr[0]);
        this.name = arr[0];
        this.cpu = arr[1];
        this.mem = arr[2];
        this.state = arr[3];
    }
    render(sid) {
        const state = this.state == 1 ? "active" : "not-active";
        const name = this.name;
        return `<div class='boxitem ${state}'>
                    <div id="${this.genId('name')}">${this.name}</div>
                    <div>
                        <div class="progress-bar">
                            <div id="${this.genId('cpu')}" class="progress-bar-fill" style="width: ${this.cpu};">&nbsp;${this.cpu}</div>
                        </div>
                    </div>
                    <div><i data-id="${this.id}" data-serverid="${sid}" data-field="btnStart" data-value="start" class="play icon"></i> <i data-id="${this.id}" data-serverid="${sid}" data-field="btnStop" data-value="stop" class="stop icon"></i> <i data-id="${this.id}" data-serverid="${sid}" data-field="btnConfig" data-value="config" class="cog icon"></i></div>
                    <div>
                        <div class="progress-bar">
                            <div id="${this.genId('mem')}" class="progress-bar-fill" style="width: ${this.mem};">&nbsp;${this.mem}</div>
                        </div>
                    </div>
                </div>`;
    }
}
*/

/*
FNList = {
    btnStart: (o) => {
        console.log("btnStart - click", o);
    },
    btnStop: (o) => {
        console.log("btnStop - click", o);
    },
    btnConfig: (o) => {
        console.log("btnConfig - click", o);
    }
}

function globalClick(event) {
    let T = event.target;
    let SID = T.dataset.serverid;
    let IDS = T.dataset.id;
    let FLD = T.dataset.field;
    let VAL = T.dataset.value;
    //console.log({sid: SID, id: IDS, field: FLD, value: VAL});
    let pro = null;
    let i = FTP.serverById(SID);
    if (i === false) {
        i = FIX.serverById(SID);
        let pid = FIX.svr[i].processById(IDS);
        pro = FIX.svr[i].process[pid];
    } else {
        let pid = FTP.svr[i].processById(IDS);
        pro = FTP.svr[i].process[pid];
    }
    //console.log(pro);
    FNList[FLD](pro);
}
*/

