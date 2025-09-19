LS_KEY = "__CMS_PHP__";
STO = {};
function $id(id) {return document.getElementById(id)}
function loadStorage() {
    var loaded = window.localStorage.getItem(LS_KEY);
    if (loaded == null) { STO = {}; } else { STO = JSON.parse(loaded); }
}
function saveStorage() {
    window.localStorage.setItem(LS_KEY, JSON.stringify(STO));
}
function setSetting(key, val) {
    STO[key] = val;
    saveStorage();
}
function getSetting(key, def = "") {
    var k = Object.keys(STO);
    return (k.includes(key)) ? STO[key] : def;
}
function burnSetting() {
    STO = {};
    saveStorage();
}
normalizeCase = (teks, allcaps=false) => {
    if (allcaps) return teks.toLowerCase().replace(/(?<=(?:^|[.?!])\W*)[a-z]/g, i => i.toUpperCase());
    return teks.replace(/(?<=(?:^|[.?!])\W*)[a-z]/g, i => i.toUpperCase());
}
caseNormalizer = (teks) => {
    let sket = teks.toUpperCase();
    if (sket == teks) return normalizeCase(teks, true);
    return normalizeCase(teks);
}
function escapeTag(str) {
    return (""+str).replace(/&/g, "&amp;")
          .replace(/</g, "&lt;")
          .replace(/>/g, "&gt;")
          .replace(/"/g, "&quot;")
          .replace(/'/g, "&#39;");
 
}
(function (){
    var dateFormat = function () {
        var token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
            timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
            timezoneClip = /[^-+\dA-Z]/g,
            pad = function (val, len) {
                val = String(val);
                len = len || 2;
                while (val.length < len) val = "0" + val;
                return val;
            };

        // Regexes and supporting functions are cached through closure
        return function (date, mask, utc) {
            var dF = dateFormat;

            // You can't provide utc if you skip other args (use the "UTC:" mask prefix)
            if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
                mask = date;
                date = undefined;
            }

            // Passing date through Date applies Date.parse, if necessary
            date = date ? new Date(date) : new Date;
            if (isNaN(date)) throw SyntaxError("invalid date");

            mask = String(dF.masks[mask] || mask || dF.masks["default"]);

            // Allow setting the utc argument via the mask
            if (mask.slice(0, 4) == "UTC:") {
                mask = mask.slice(4);
                utc = true;
            }

            var _ = utc ? "getUTC" : "get",
                d = date[_ + "Date"](),
                D = date[_ + "Day"](),
                m = date[_ + "Month"](),
                y = date[_ + "FullYear"](),
                H = date[_ + "Hours"](),
                M = date[_ + "Minutes"](),
                s = date[_ + "Seconds"](),
                L = date[_ + "Milliseconds"](),
                o = utc ? 0 : date.getTimezoneOffset(),
                flags = {
                    d:    d,
                    dd:   pad(d),
                    ddd:  dF.i18n.dayNames[D],
                    dddd: dF.i18n.dayNames[D + 7],
                    m:    m + 1,
                    mm:   pad(m + 1),
                    mmm:  dF.i18n.monthNames[m],
                    mmmm: dF.i18n.monthNames[m + 12],
                    yy:   String(y).slice(2),
                    yyyy: y,
                    h:    H % 12 || 12,
                    hh:   pad(H % 12 || 12),
                    H:    H,
                    HH:   pad(H),
                    M:    M,
                    MM:   pad(M),
                    s:    s,
                    ss:   pad(s),
                    l:    pad(L, 3),
                    L:    pad(L > 99 ? Math.round(L / 10) : L),
                    t:    H < 12 ? "a"  : "p",
                    tt:   H < 12 ? "am" : "pm",
                    T:    H < 12 ? "A"  : "P",
                    TT:   H < 12 ? "AM" : "PM",
                    Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
                    o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
                    S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
                };

            return mask.replace(token, function ($0) {
                return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
            });
        };
    }();

    // Some common format strings
    dateFormat.masks = {
        "default":      "ddd mmm dd yyyy HH:MM:ss",
        shortDate:      "m/d/yy",
        mediumDate:     "mmm d, yyyy",
        longDate:       "mmmm d, yyyy",
        fullDate:       "dddd, mmmm d, yyyy",
        shortTime:      "h:MM TT",
        mediumTime:     "h:MM:ss TT",
        longTime:       "h:MM:ss TT Z",
        isoDate:        "yyyy-mm-dd",
        isoTime:        "HH:MM:ss",
        isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
        isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'",
        localLongDate:  "dd mmmm yyyy",
        localShortDate: "dd-mmm-yyyy",
        localShortDateTime: "dd/mm/yy HH:MM:ss",
        localShortTime: "dd-mmm-yyyy HH:MM:ss",
        dmyDate: "dd/mm/yyyy",
        ymOnly: "yyyy-mm",
        noseparator: "yyyymmdd"
    };

    // Internationalization strings
    dateFormat.i18n = {
        dayNames: [
            "MI", "SE", "SE", "RA", "KA", "JU", "SA",
            "MINGGU", "SENIN", "SELASA", "RABU", "KAMIS", "JUMAT", "SABTU"
        ],
        monthNames: [
            "JAN", "FEB", "MAR", "APR", "MEI", "JUN", "JUL", "AGS", "SEP", "OKT", "NOV", "DES",
            "JANUARI", "FEBRUARI", "MARET", "APRIL", "MEI", "JUNI", "JULI", "AGUSTUS", "SEPTEMBER", "OKTOBER", "NOVEMBER", "DESEMBER"
        ]
    };

    // For convenience...
    Date.prototype.format = function (mask, utc) {
        return dateFormat(this, mask, utc);
    };
})();

const dateDiff = (startingDate, endingDate) => {
    var startDate = new Date(new Date(startingDate).toISOString().substr(0, 10));
    if (!endingDate) {
        endingDate = new Date().toISOString().substr(0, 10); // need date in YYYY-MM-DD format
    }
    var endDate = new Date(endingDate);
    if (startDate > endDate) {
        var swap = startDate;
        startDate = endDate;
        endDate = swap;
    }
    var startYear = startDate.getFullYear();
    var february = (startYear % 4 === 0 && startYear % 100 !== 0) || startYear % 400 === 0 ? 29 : 28;
    var daysInMonth = [31, february, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    var yearDiff = endDate.getFullYear() - startYear;
    var monthDiff = endDate.getMonth() - startDate.getMonth();
    if (monthDiff < 0) {
        yearDiff--;
        monthDiff += 12;
    }
    var dayDiff = endDate.getDate() - startDate.getDate();
    if (dayDiff < 0) {
        if (monthDiff > 0) {
            monthDiff--;
        } else {
            yearDiff--;
            monthDiff = 11;
        }
        dayDiff += daysInMonth[startDate.getMonth()];
    }

    //return yearDiff + 'Y ' + monthDiff + 'M ' + dayDiff + 'D';
    return [yearDiff, monthDiff, dayDiff];
}
/**
 * Returns a hash code from a string
 * @param  {String} str The string to hash.
 * @return {Number}    A 32bit integer
 * @see http://werxltd.com/wp/2010/05/13/javascript-implementation-of-javas-string-hashcode-method/
 */
function hashCode(str) {
    let hash = 0;
    for (let i = 0, len = str.length; i < len; i++) {
        let chr = str.charCodeAt(i);
        hash = (hash << 5) - hash + chr;
        hash |= 0; // Convert to 32bit integer
    }
    return hash;
}

function capitalizeWords(str) {
    return str
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
}

function camelCaseSpacer(camelCaseString){
    return camelCaseString.replace(/([a-z])([A-Z])/g, "$1 $2");
}

function undescoreSpace(txt) {
    return txt.replace(/_/g, ' ');
}

function Api(endpoint, {body, ...customConfig} = {}) {
    //const headers = {'Content-Type': 'multipart/form-data'};
    const config = {
        method: body ? 'POST' : 'GET',
        ...customConfig,
        headers: {
            ...customConfig.headers,
        },
    };
    if (body) {
        config.body = body; //JSON.stringify(body);
    }
    return window
        .fetch(`${endpoint}`, config)
        .then(async response => {
            if (response.ok) {
                return await response.json();
            } else {
                const errorMessage = await response.text();
                return Promise.reject(new Error(errorMessage));
            }
        });
}
async function SApi(url) {
    const res = await fetch(url);
    const json = await res.json();
    return json;
}

SLoader = (stitle) => {
    Swal.fire({
        title: stitle
    });
    Swal.showLoading();
}
SLoaderHide = () => {
    Swal.close();
}
SModal = (stitle,stext,sicon,opts={},callback=null,html=false,close=true) => {
    if (close) Swal.close();
    var optx = {
        icon: sicon,
        title: stitle,
        ...opts
    }
    if (html) {
        optx["html"] = stext;
    } else {
        optx["text"] = stext;
    }
    Swal.fire(optx).then(
        ret => {
            if (callback != null) callback(ret);
        }
    );
}
SError = (sttl, msg, opts={},callback=null,html=false,close=true) => {
    SModal(sttl, msg, 'error',opts,callback,html,close)
}
SConfirm = (sttl, msg, fn=null) => {
    Swal.fire({
        title: sttl,
        text: msg,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
    }).then(result => {
        /*
        if (result.isConfirmed) {
            if (fn != null) fn();
        }
        */
        if (fn != null) fn(result.isConfirmed, result);
    });
}
SAlert = (sttl, msg, icon="") => {
    var parm = {
        title: sttl,
        text: msg
    };
    if (icon != "") parm["icon"] = icon;
    Swal.fire(parm);
}
SToaster = (ttype, stitle, msg=null) => {
    // ttype = success | error | warning
    var opt = {
        position: 'top-end',
        icon: ttype,
        title: stitle,
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true
    };
    if (msg != null) opt.text = msg;
    Swal.fire(opt);
}
SToastError = (stitle, msg=null) => {
    Toaster("error", stitle, msg);
}
SToastSuccess = (stitle, msg=null) => {
    Toaster("success", stitle, msg);
}
SToastInfo = (stitle, msg) => {
    Toaster("info", stitle, msg);
}
SToastWarning = (stitle, msg) => {
    Toaster("warning", stitle, msg);
}

FModal = (sttl, msg, opts={}, acts=[{text: "OK", class:"blue"}]) => {
//    actions: [{
//        text    : 'Wait',
//        class   : 'red',
//        icon    : 'exclamation',
//        click   : function(){}
//    }]
    var optx = {
        title: sttl,
        class: 'mini',
        closeIcon: false,
        content: msg,
        ...opts
    };
    optx.actions = acts;
    $('body').modal(optx).modal('show');
}
FError = (sttl, msg) => {
    FModal(`<i class="exclamation triangle icon red"></i> ${sttl}`, msg, {inverted: true}, [{text:"OK",class:"red"}]);
}
FiError = (sttl, msg) => {
    $("body").modal("myError",`<i class='exclamation triangle icon red'></i> ${sttl}`, msg, {inverted: true, duration: 0}, [{text:"OK",class:"red"}]);
}
FConfirm = (sttl, msg, fn=null) => {
    $('body').modal('confirm', sttl, msg, (choice)=>{
        if (fn != null) fn(choice); // choice is true or false
    });
}
FAlert = (sttl, msg) => {
    $("body").modal("alert",sttl,msg);
}
Toaster = (ttype, stitle, msg=null, icon=null) => {
    // ttype = success | error | warning
    var opt = {
        title: stitle,
        showProgress: 'bottom',
        class: ttype,
        displayTime: 5000
    };
    if (msg != null) opt.message = msg;
    if (icon != null) opt.showIcon = icon;
    $('body').toast(opt);
}
ToastError = (stitle, msg=null) => {
    Toaster("error", stitle, msg, "times circle icon");
}
ToastSuccess = (stitle, msg=null) => {
    Toaster("success", stitle, msg, "thumbs up outline icon");
}
ToastInfo = (stitle, msg) => {
    Toaster("blue", stitle, msg, "info circle icon")
}
Loader = (teks="Loading...") => {
    $("body").dimmer({
        closable: false,
        displayLoader: true,
        loaderText: teks,
        loaderVariation: 'normal orange big elastic',
    }).dimmer('show');
}
LoaderHide = () => {$("body").dimmer("hide");}

LError = (sts,dtl) => {console.log({status:sts, detail:dtl})}

const myFormFill = (names,row) => {
    for (var i in names) {
        if ($id(names[i])) {
            $id(names[i]).value = row[names[i]];
        }
    }
}
const myFormGet = (names,upcase=true) => {
    var hasil = {};
    for (var i in names) {
        if ($id(names[i])) {
            hasil[names[i]] = upcase ? ($id(names[i]).value).toUpperCase() : $id(names[i]).value;
        }
    }
    return hasil;
}

const fmtFileSize = (number) => {
    if (number < 1024) {
      return `${number} bytes`;
    } else if (number >= 1024 && number < 1048576) {
      return `${(number / 1024).toFixed(1)} KB`;
    } else if (number >= 1048576) {
      return `${(number / 1048576).toFixed(1)} MB`;
    }
}

const fileTypes = [
    "image/apng",
    "image/bmp",
    "image/gif",
    "image/jpeg",
    "image/pjpeg",
    "image/png",
    "image/svg+xml",
    "image/tiff",
    "image/webp",
    "image/x-icon"
];

PreviewImage = (fld) => {
    var fls = $id(`fld-${fld}`);
    var prv = $id(`fld-${fld}-prv`);
    while(prv.firstChild) prv.removeChild(prv.firstChild);

    const curFiles = fls.files;
    if (curFiles.length === 0) {
        // do nothing
    } else {
        const list = document.createElement('ol');
        list.className = "zpl";
        prv.appendChild(list);
        for (const file of curFiles) {
            const listItem = document.createElement('li');
            listItem.className = "liprv";
            const para = document.createElement('p');
            para.className = "paraprv";
            if (fileTypes.includes(file.type)) {
                const image = document.createElement('img');
                para.textContent = `File size ${fmtFileSize(file.size)}.`;
                image.className = "h64";
                image.onload = (e)=>{
                    let nx = e.target.nextElementSibling;
                    nx.textContent += ` Image size: ${e.target.naturalWidth}x${e.target.naturalHeight}.`;
                };
                image.src = URL.createObjectURL(file);
                listItem.appendChild(image);
                listItem.appendChild(para);
            } else {
                para.textContent = `Not a valid file type. Update your selection.`;
                listItem.appendChild(para);
            }
            list.appendChild(listItem);
        }
    }
}

FieldVerifier = (test,msg) => {
    if (test) {
        FiError("Error", msg);
        return false;
    }
    return true;
};

tbltrGetSelRow = (T, de="No row selected") => {
    var sel = (T.getSelectedData())[0]; // get first selected element
    if (sel == undefined) {
        ToastError(de);
        return null;
    }
    return sel;
}

/*
    TABULATOR FORMATTER
*/
autoNumFormatter = (cell) => {
    var tbl = cell.getTable();
    var currentPage = tbl.getPage();
    var pageSize = tbl.getPageSize();
    var starter = (currentPage-1)*pageSize;
    var row = cell.getRow();
    var rowIndex = row.getPosition(false);
    return (starter+rowIndex);
};
chevronUpFormatter = (cell) => {
    return `<i class="chevron circle up icon"></i>`;
};
chevronDnFormatter = (cell) => {
    return `<i class="chevron circle down icon"></i>`;
};
boolFormatter = (cell) => {
    if (cell.getValue() != 0) {
        return `<i class="green check circle outline icon"></i>`;
    } else {
        return `<i class="red circle outline icon"></i>`;
    }
};
colorFormatter = (cell) => {
    var value = cell.getValue();
    if (value == "") return "";
    return `<span style="background-color: ${value}; width: 20px; border: 1px solid rgba(34,36,38,.15); border-radius: .28571429rem; display: inline-block; margin-right: 5px; ">&nbsp;</span> <span style="font-family: monospace;">${value}</span>`;
}
dateFormatter = (cell) => {
    var value = cell.getValue();
    if (value == "") return "";
    var tgl = new Date(value);
    return tgl.format("localLongDate");
}
dmyFormatter = (cell) => {
    var value = cell.getValue();
    if (value == "") return "";
    var tgl = new Date(value);
    return tgl.format("dmyDate");
}
medDateFormatter = (cell) => {
    var value = cell.getValue();
    if (value == "") return "";
    var tgl = new Date(value);
    return tgl.format("localShortDate");
}
isoDateFormatter = (cell) => {
    var value = cell.getValue();
    if (value == "") return "";
    var tgl = new Date(value);
    return tgl.format("isoDate");
}
isoTimeFormatter = (cell) => {
    var value = cell.getValue();
    if (value == "") return "";
    var tgl = new Date("1945-08-17 "+value);
    return tgl.format("isoTime");
}
datetimeFormatter = (cell) => {
    var value = cell.getValue();
    if (value == "") return "";
    var tgl = new Date(value);
    return tgl.format("localShortTime");
}
linkFormatter = (cell) => {
    var value = cell.getValue();
    return `<a href="${value}" target="_blank">${value}</a>`;
}
numberFormatter = (cell) => {
    var value = cell.getValue();
    return new Intl.NumberFormat('default').format(value);
}
percentFormatter = (cell) => {
    var value = cell.getValue();
    return value;
}
percentFormatter4 = (cell) => {
    var value = cell.getValue();
    var retv = new Intl.NumberFormat('default', {
        style: 'percent',
        minimumFractionDigits: 4,
        maximumFractionDigits: 4,
    }).format(value);
    return retv;
}
percentFormatter4edit = (cell) => {
    var value = cell.getValue();
    var retv = new Intl.NumberFormat('default', {
        minimumFractionDigits: 4,
        maximumFractionDigits: 4,
    }).format(value*100);
    return retv;
}
longtextFormatter = (cell) => {
    var value = cell.getValue();
    if (value == "") return "";
    return caseNormalizer(value);
}

userRefFormatter = (cell) => {
    var value = cell.getValue();
    if (value == "") return "";
    var hasil = "";
    for (var i in Ref.Data["User"]) {
        var row = Ref.Data["User"][i];
        if (row["ID"] == value) hasil = row["UserName"];
    }
    return hasil;
}
useridRefFormatter = (cell) => {
    var value = cell.getValue();
    if (value == "") return "";
    var hasil = "";
    for (var i in Ref.Data["User"]) {
        var row = Ref.Data["User"][i];
        if (row["ID"] == value) hasil = row["Uid"];
    }
    return hasil;
}

unformatPercent = (value) => {
    var hasil = parseFloat(value) / 100.0;
    return hasil;
}
/**
 * https://stackoverflow.com/questions/29255843/is-there-a-way-to-reverse-the-formatting-by-intl-numberformat-in-javascript
 * Parse a localized number to a float.
 * @param {string} stringNumber - the localized number
 * @param {string} locale - [optional] the locale that the number is represented in. Omit this parameter to use the current locale.
 */
function parseLocaleNumber(stringNumber, locale='default') {
    var thousandSeparator = Intl.NumberFormat(locale).format(11111).replace(/\p{Number}/gu, '');
    var decimalSeparator = Intl.NumberFormat(locale).format(1.1).replace(/\p{Number}/gu, '');

    return parseFloat(stringNumber
        .replace(new RegExp('\\' + thousandSeparator, 'g'), '')
        .replace(new RegExp('\\' + decimalSeparator), '.')
    );
}

function RealTimeNumberFormat(event, fmtr, unfmtr) {
    //var o = document.getElementById(id);
    var o = event.target;
    var cpo = o.selectionStart; // cursor position original
    var oto = o.value;          // object text original
    var cpn = 0;                // cursor position number
    for (var i=0; i<cpo; i++) {
        var c = oto.substr(i,1);
        if ("0123456789".indexOf(c) >= 0) cpn++;
    }
    var oot = {
        getValue : () => unfmtr(oto)
    };
    if (isNaN(oot.getValue())) return;

    var otf = fmtr(oot);
    var cpf = 0;
    var abis = false;
    var cpc = cpn;
    for (var i=0,l=otf.length; i<l; i++) {
        var c = otf.substr(i,1);
        if ("0123456789".indexOf(c) >= 0) cpc--;
        if (cpc < 0) {
            cpf = i;
            break;
        }
        if (i == l-1) abis = true;
    }
    if (abis) cpf = otf.length;
    o.value = otf;
    o.setSelectionRange(cpf,cpf);
}

function excludeChars(event,chars) {
    var o = event.target;
    var cpo = o.selectionStart; // cursor position original
    var oto = o.value;          // object text original
    let regex = new RegExp(`[${chars}]`, 'g');
    var nto = oto.replace(regex,'');
    if (oto.length != nto.length) cpo--;
    o.value = nto;
    o.setSelectionRange(cpo,cpo);
}

function includeChars(event,chars="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ") {
    var o = event.target;
    var cpo = o.selectionStart; // cursor position original
    var oto = o.value;          // object text original
    var esc = chars.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    let regex = new RegExp(`[^${esc}]`, 'g');
    var nto = oto.replace(regex,'');
    if (oto.length != nto.length) cpo--;
    o.value = nto;
    o.setSelectionRange(cpo,cpo);
}



/*
    CLASSES
*/
class Refs {
    constructor() {
        this.Data = {};
    }
    load(name, url, fn=false) {
        Api(url).then(
            data => {
                this.Data[name] = data.data; // array of row;
                if (fn) setTimeout(()=>{fn()}, 500);
            },
            error => {}
        );
    }
    find(name,fkey,key,fval){
        var hasil = "";
        for (var i in this.Data[name]) {
            var row = Ref.Data[name][i];
            if (row[fkey] == key) hasil = row[fval];
        }
        return hasil;
    }
}

class Framer {
    constructor(id="") {
        this.id = id;
        this.confirmShow = false;
        this.afterShow = null;
    }
    setCaption(cpt) {
        this.caption = cpt;
        return this;
    }
    setContent(con) {
        this.content = con;
        return this;
    }
    setVerifier(verify=false, fn=null) {
        this.verify = verify;
        this.verifier = fn;
        return this;
    }
    setAfterShow(fn = null) {
        this.afterShow = fn;
        return this;
    }
    setAction(oke, fn=null, btnCaption="Save") {
        this.okeShow = oke;
        this.okeCaption = btnCaption;
        this.okeFn = fn;
        return this;
    }
    setConfirmation(confirmHeader="Save Data", confirmContent="Are you sure?", confirmAsk=true) {
        this.confirmShow = confirmAsk;
        this.confirmCaption = confirmHeader;
        this.confirmBody = confirmContent;
        return this;
    }
    show(execas = true) {
        $("#frame_header").html(this.caption);
        $("#frame_content").html(this.content);
        var act = document.getElementById("frame_action");
        act.innerHTML = "";

        if (this.okeShow) {
            var oke = document.createElement("div");
            oke.className = "ui button green";
            oke.textContent = this.okeCaption;
            oke.addEventListener("click", (e)=>{
                var lanjut = true;
                if (this.verify) {
                    lanjut = this.verifier();
                }
                if (!lanjut) return;
                if (this.confirmShow) {
                    $("body").modal("myConfirm", this.confirmCaption, this.confirmBody, ()=>{
                        this.okeFn(e);
                        this.close();
                    });
                } else {
                    this.okeFn(e);
                }
            });
            act.appendChild(oke);
        }

        var cancel = document.createElement("div");
        cancel.className = "ui cancel button";
        cancel.textContent = "Close";
        act.appendChild(cancel);

        $("#"+this.id).modal({allowMultiple: true, closable: false}).modal("show");
        //$("#"+this.id).modal("show");

        if (execas && (this.afterShow != null)) {
            var that = this;
            setTimeout(() => {
                that.afterShow();
            }, 100);
        } 
    }
    close() {
        $("#"+this.id).modal("hide all");
    }
}

class SFramer {
    constructor(id="") {
        this.id = id;
        this.confirmShow = false;
        this.afterShow = null;
    }
    setCaption(cpt) {
        this.caption = cpt;
        return this;
    }
    setContent(con) {
        this.content = con;
        return this;
    }
    setVerifier(verify=false, fn=null) {
        this.verify = verify;
        this.verifier = fn;
        return this;
    }
    setAfterShow(fn = null) {
        this.afterShow = fn;
        return this;
    }
    /*
    setAction(oke, fn=null, btnCaption="Save") {
        this.okeShow = oke;
        this.okeCaption = btnCaption;
        this.okeFn = fn;
        return this;
    }
    */
    setConfirmation(confirmHeader="Save Data", confirmContent="Are you sure?", confirmAsk=true) {
        this.confirmShow = confirmAsk;
        this.confirmCaption = confirmHeader;
        this.confirmBody = confirmContent;
        return this;
    }
    show(execas = true) {
        //$("#frame_header").html(this.caption);
        //$("#frame_content").html(this.content);
        //var act = document.getElementById("frame_action");
        //act.innerHTML = "";

        /*
        if (this.okeShow) {
            var oke = document.createElement("div");
            oke.className = "ui button green";
            oke.textContent = this.okeCaption;
            oke.addEventListener("click", (e)=>{
                var lanjut = true;
                if (this.verify) {
                    lanjut = this.verifier();
                }
                if (!lanjut) return;
                if (this.confirmShow) {
                    $("body").modal("myConfirm", this.confirmCaption, this.confirmBody, ()=>{
                        this.okeFn(e);
                        this.close();
                    });
                } else {
                    this.okeFn(e);
                }
            });
            act.appendChild(oke);
        }

        var cancel = document.createElement("div");
        cancel.className = "ui cancel button";
        cancel.textContent = "Close";
        act.appendChild(cancel);

        $("#"+this.id).modal({allowMultiple: true, closable: false}).modal("show");
        */
        //$("#"+this.id).modal("show");

        var parm = {
            title: this.caption,
            html: this.content,
        };
        Swal.fire(parm);


        if (execas && (this.afterShow != null)) {
            var that = this;
            setTimeout(() => {
                that.afterShow();
            }, 100);
        } 
    }
    close() {
        //$("#"+this.id).modal("hide all");
        LoaderHide();
    }
}

class Formation {
    constructor(uval = false) {
        this.prefixId = "fld-";
        this.upperValue = uval;
        this.Model = null;
        this.Data = null;
        this.rawData = null;
        this.tempData = null;
        this.customModel = null;
        this.useCustomModel = false;
        this.ajaxConfig = "POST";
        this.useTag = "";
        this.TabKeys = ["frozen", "width", "formatter", "hozAlign", "headerHozAlign", "headerSort", "headerFilter","headerFilterFunc","headerFilterParams","headerFilterLiveFilter", "cellClick", "visible","editor","editorParams","editable"]; // keys for tabulator
    }

    setData(data) {
        this.Data = data;
        return this;
    }
    setRawData(raw) {
        this.rawData = raw;
        return this;
    }
    updateData(row, ids) {
        // row is the data (object)
        // ids is array of id
        var hasil = false;
        for (var i=0; i<this.Data.length; i++) {
            var dat = this.Data[i];
            var sam = true;
            for (var j=0; j<ids.length; j++) {
                var id = ids[j];
                if (dat[id] != row[id]) sam = false;
            }
            if (sam) {
                this.Data[i] = row;
                hasil = true;
                break;
            }
        }
        return hasil;
    }
    deleteData(row, ids) {
        var hasil = false;
        for (var i=0; i<this.Data.length; i++) {
            var dat = this.Data[i];
            var sam = true;
            for (var j=0; j<ids.length; j++) {
                var id = ids[j];
                if (dat[id] != row[id]) sam = false;
            }
            if (sam) {
                //this.Data[i] = row;
                this.Data.splice(i,1);
                hasil = true;
                break;
            }
        }
        return hasil;
    }
    setCustomModel(model) {
		this.customModel = model;
		this.useCustomModel = true;
		return this;
	}
    setModel(model) {
        /*
            caption:    title string
            type:       string | numeric | datetime
            primaryKey: true | false
            hidden:     true | false <hide when viewing>
            autoValue:  true | false <hide when adding, readonly when editing>
            readOnly:   true | false <showing when adding, readonly when editing>
            hideEdit:   true | false <depreciated! use noedit> <if false, hidden when editing> 
            noadd:      true | false <if true, hide in add form>
            noedit:     true | false <if true, hide in edit form>
            noview:     true | false <if true, hide in view card>
            control:    text {default} | password
            placeholder: <if empty then use title>
            upperCase:  true | false <get the upcase when read from control>
            normalize:  true | false <normalize sentence case>
            separator:  true | false <set column separator after the field>
            maxLength:  numeric
            editorFormat: function for editor
            defaultValue: default value
            useTag:     wrap tag in view
            pretext:    text before field label
            posttext:   text after field label
            <-- for tabulator -->
            title
            formatter
            frozen
            width
            hozAlign
            headerHozAlign
            headerSort
            headerFilter
            initialHeaderFilter
            headerFilterFunc
            headerFilterParam
            cellClick
            visible
            ... { dont't forget to add the keys to this.TabKeys @ constructor }
        */
        this.Model = model;
        return this;
    }
    getModelTabulator(){
        var hasil = [];
        for (var i in this.Model) {
            var fld = this.Model[i];
            var keys = Object.keys(fld);
            var xt = {field: i, title: fld.title ?? fld.caption};
            for (var x in this.TabKeys) {
                if (keys.indexOf(this.TabKeys[x]) >= 0) xt[this.TabKeys[x]] = fld[this.TabKeys[x]];
            }
            hasil.push(xt);
        }
        return hasil;
    }

    getById(id) {
        // param "id" is object {field:value}
        var x = this.Data.length;
        var keys = Object.keys(id);
        for (var i=0; i<x; i++) {
            var sama = true;
            for (var j=0; j<keys.length; j++) {
                if (this.Data[i][keys[j]] != id[keys[j]]) sama = false;
            }
            if (sama) return this.Data[i];
        }
        return null;
    }
    indexById(id){
        var x = this.Data.length;
        var keys = Object.keys(id);
        for (var i=0; i<x; i++) {
            var sama = true;
            for (var j=0; j<keys.length; j++) {
                if (this.Data[i][keys[j]] != id[keys[j]]) sama = false;
            }
            if (sama) return i;
        }
        return false;
    }

    formDataManual(inpt = []) {
        var data = inpt.reverse();
        var hasil = new FormData();
        while (data.length >= 2) {
            var key = data.pop();
            var val = data.pop();
            hasil.append(key, val);
        }
        return hasil;
    }
    formDataTabRow(sel, asObject=false) {
        var hasil = new FormData();
        var obj = {};
        var keys = Object.keys(sel);
        for (var i in keys) {
            var key = keys[i];
            hasil.append(key, sel[key]);
            obj[key] = sel[key];
        }
        return asObject ? obj : hasil;
    }
    readForm(isEdit=true,getRO=false,asObject=false) {
        //var elm = document.getElementById(id);
        var hasil = new FormData();
        var obj = {};
        var keys = Object.keys(this.Model);
        for (var i in keys) {
            var key = keys[i];
            var meta = this.Model[key];
            if (meta.hide) continue;
            if (meta.readOnly && !getRO) {
                //console.log ("SKIP", meta, meta.readOnly, getRO);
                continue;
            }
            var auto = meta.autoValue || false;
            var readonly = auto || meta.readOnly;
            var ucase = meta.upperCase == undefined ? this.upperValue : meta.upperCase;
            //var ucase = meta.upperCase || this.upperValue;
            var control = meta.control || meta.type || "";
            var normalCase = meta.normalize || false;

            var e = document.getElementById(this.prefixId+key);
            if (e) {
                switch (control) {
                    case "file":
                        if (e.files.length === 0) continue;
                        hasil.append(e.dataset.fieldName, e.files[0]);
                        obj[e.dataset.fieldName] = 1;
                        break;
                    case "boolean":
                        hasil.append(e.dataset.fieldName, e.checked ? 1 : 0);
                        obj[e.dataset.fieldName] = e.checked ? 1 : 0;
                        break;
                    default:
                        if (isEdit) {
                            var value = readonly ? e.dataset.fieldValue : e.value;
                            if (normalCase) value = caseNormalizer(value);
                            if (ucase) value = value.toUpperCase();
                            if (value == this.tempData[e.dataset.fieldName] && value == ""){
                                if (getRO) {
                                    hasil.append(e.dataset.fieldName, value);
                                    obj[e.dataset.fieldName] = value;
                                }
                            } else {
                                hasil.append(e.dataset.fieldName, value);
                                obj[e.dataset.fieldName] = value;
                            }
                        } else {
                            var value = e.value;
                            if (normalCase) value = caseNormalizer(value);
                            if (ucase) value = value.toUpperCase();
                            if (!auto) {
                                hasil.append(e.dataset.fieldName, value);
                                obj[e.dataset.fieldName] = value;
                            }
                        }
                    break;
                }
            }
        }
        return asObject ? obj : hasil;
    }

    doVerify() {
        var keys = Object.keys(this.Model);
        var vals = {};
        for (var i in keys) {
            var key = keys[i];
            var meta = this.Model[key];
            var ucase = meta.upperCase || this.upperValue;
            var theval = null;
            var e = document.getElementById(this.prefixId+key);
            if (e) {
                theval = ucase ? e.value.toUpperCase() : e.value;
            }
            vals[key] = theval;
        }
        for (var i in keys) {
            var key = keys[i];
            var meta = this.Model[key];
            var auto = meta.autoValue || false;
            var ucase = meta.upperCase || this.upperValue;

            if (auto) continue;

            if (meta.verify) {
                var e = document.getElementById(this.prefixId+key);
                if (e) {
                    var theval = ucase ? e.value.toUpperCase() : e.value;
                    if (!meta.verify(theval,vals)) {
                        e.focus();
                        return false;
                    }
                }
            }
        }
        return true;
    }

    formAdd(form_id, cols=1, showro=false) {
        var hasil = `<form id="${form_id}" onsubmit="return false;"><div class="ui form">`;
        if (cols > 1) hasil += `<div class="ui equal width grid"><div class="column">`;
        var keys = Object.keys(this.Model);
        for (var i in keys) {
            var key = keys[i];
            var meta = this.Model[key];
            if (meta.hide || false) continue;
            if (meta.noadd || false) continue;
            var auto = meta.autoValue || false;
            if (auto) continue;
            var readonly = meta.readOnly ?? false;
            var ph = meta.placeholder || meta.caption;
            //var ctr = meta.control ?? "input";
            var pretex = meta.pretext ?? "";
            if (pretex != "") pretex = `<div class='pretext'>${pretex}</div>`;
            var postex = meta.posttext ?? "";
            if (postex != "") postex = `<div class='postext'>${postex}</div>`;
            var uclass = (meta.upperCase == undefined ? this.upperValue : meta.upperCase) ? `class="upper-value"` : "";
            //var uclass = (meta.upperCase || this.upperValue) ? `class="upper-value"` : "";
            var maxl = (meta.maxLength || "") == "" ? "" : `maxlength="${meta.maxLength}"`;

            var deef = meta.defaultValue || "";
            //var def = deef;
            //if (deef != "") deef = " value='"+deef+"'";
            var dsbld = readonly && showro ? " disabled" : "";

            hasil += `<div class="field" id="div-${this.prefixId}${key}">${pretex}<label>${meta.caption}</label>${postex}`;
            var control = "";
            switch (meta.control || meta.type || "input") {
                case "password":
                    control = `<input type="password" id="${this.prefixId}${key}" name="${key}" data-field-name="${key}" placeholder="${ph}" value="${deef}"${dsbld}>`;
                    break;
                case "color":
                    control = `<input type="color" id="${this.prefixId}${key}" name="${key}" data-field-name="${key}" placeholder="${ph}" value="${deef}"${dsbld}>`;
                    break;
                case "file":
                    control = `<input type="file" id="${this.prefixId}${key}" name="${key}" data-field-name="${key}"${dsbld}><div id="${this.prefixId}${key}-prv"></div>`;
                    break;
                case "textarea":
                    control = `<textarea class="hta94" id="${this.prefixId}${key}" name="${key}" data-field-name="${key}" placeholder="${ph}" ${maxl}${dsbld}>${deef}</textarea>`;
                    break;
                case "date":
                    control = `<input type="date" id="${this.prefixId}${key}" name="${key}" data-field-name="${key}" placeholder="${ph}" value="${deef}"${dsbld}>`;
                    break;
                case "month":
                    control = `<input type="month" id="${this.prefixId}${key}" name="${key}" data-field-name="${key}" placeholder="${ph}"${dsbld}>`;
                    break;
                case "lookup":
                    var tbl = meta.option.table;
                    var tid = meta.option.id;
                    var txt = meta.option.text;
                    var mty = meta.option.empty;
                    control = `<select class="ui search dropdown" id="${this.prefixId}${key}" name="${key}" data-field-name="${key}" placeholder="${ph}"${dsbld}>`;
                    control += `<option value="0"></option>`;
                    if (!mty) {
                        for (var i in Ref.Data[tbl]) {
                            var row = Ref.Data[tbl][i];
                            control += `<option value="${row[tid]}">${row[txt]}</option>`;
                        }
                    }
                    control += `</select>`;
                    break;
                case "boolean":
                    var pref = "";
                    var suff = "";
                    if (readonly && (meta.type != "boolean")) {
                        pref += `<div class="ui disabled input">`;
                        suff += "</div>";
                    }
                    control = `${pref}<div class="ui toggle checkbox"><input type="checkbox" id="${this.prefixId}${key}" name="${key}" data-field-name="${key}" placeholder="${ph}"${dsbld}><label></label></div>${suff}`;
                    break;
                default:
                    control = `<input type="text" id="${this.prefixId}${key}" name="${key}" data-field-name="${key}" placeholder="${ph}" value="${deef}" ${uclass} ${maxl}${dsbld}>`;
                    break;
            }
            hasil += `${control}</div>`;
            if (cols > 1) {
                if (meta.separator || false) hasil += `</div><div class="column">`;
            }
        }
        if (cols > 1) hasil += `</div></div>`;
        hasil += `</div></form>`;
        return hasil;
    }

    formEdit(idx, form_id, cols=1) {
        var data = this.getById(idx);
        this.tempData = data;
        var hasil = `<form id="${form_id}" onsubmit="return false;"><div class="ui form">`;
        if (cols > 1) hasil += `<div class="ui equal width grid"><div class="column">`;
        var ceem = this.useCustomModel;
        var keys = ceem ? this.customModel : Object.keys(this.Model);
        for (var i in keys) {
            var key = keys[i];
            if (ceem && cols > 1) {
                if (key.toUpperCase()=="__SEPARATOR__") {
                    hasil += `</div><div class="column">`;
                    continue;
                }
            }
            var meta = this.Model[key];
            if (meta.hide || false) continue;
            if (meta.noedit || false) continue;
            var auto = meta.autoValue || false;
            var readonly = auto || meta.readOnly;
            var ph = meta.placeholder || meta.caption;
            var ctr = meta.control || "input";
            var uclass = (meta.upperCase == undefined ? this.upperValue : meta.upperCase) ? `class="upper-value"` : "";
            //var uclass = (meta.upperCase || this.upperValue) ? `class="upper-value"` : "";
            var ftype = meta.fileType || "";
            var maxl = (meta.maxLength || "") == "" ? "" : `maxlength="${meta.maxLength}"`;

            var fval = data[key];
            if (meta.lookup) fval = Lookup.value(meta.lookup.table, fval);
            if (meta.editorFormat) fval = meta.editorFormat({getValue:()=>fval});
            var nval = (fval+"").replace(/"/g, '&quot;');
            var addx = `value="${nval}"`;
 
            var pretex = meta.pretext ?? "";
            if (pretex != "") pretex = `<div class='pretext'>${pretex}</div>`;
            var postex = meta.posttext ?? "";
            if (postex != "") postex = `<div class='postext'>${postex}</div>`;
             
            var pref = `<div class="field" id="div-${this.prefixId}${key}">${pretex}<label>${meta.caption}</label>${postex}`;
            var suff = `</div>`;
            if (readonly && (meta.type != "boolean")) {
                pref += `<div class="ui disabled input">`;
                suff += "</div>";
            }

            var control = "";
            switch (meta.control || meta.type || "input") {
                case "password":
                    control = `${pref}<input type="password" id="${this.prefixId}${key}" name="${key}" data-field-name="${key}" data-field-value="${data[key]}" placeholder="${ph}" ${addx}>${suff}`;
                    break;
                case "color":
                    control = `${pref}<input type="color" id="${this.prefixId}${key}" name="${key}" data-field-name="${key}" data-field-value="${data[key]}" placeholder="${ph}" ${addx}>${suff}`;
                    break;
                case "file":
                    control = `${pref}<input type="file" id="${this.prefixId}${key}" name="${key}" data-field-name="${key}">`;
                    var isi = "";
                    if (ftype == "image") isi = `<ol class="zpl"><li class="liprv"><img class="h64" src="files/${fval}"><p class="paraprv">${fval}</p></li></ol>`;
                    control += `<div id="${this.prefixId}${key}-prv">${isi}</div>`;
                    break;
                case "textarea":
                    control = `${pref}<textarea class="hta94" id="${this.prefixId}${key}" name="${key}" data-field-name="${key}" data-field-value="${data[key]}" placeholder="${ph}" ${maxl} ${addx}>${fval}</textarea>${suff}`;
                    break;
                case "date":
                    addx = `value="${fval.substring(0,10)}"`;
                    control = `${pref}<input type="date" id="${this.prefixId}${key}" name="${key}" data-field-name="${key}" data-field-value="${(data[key]).substring(0,10)}" placeholder="${ph}"  ${addx}>${suff}`;
                    break;
                case "month":
                    addx = `value="${fval.substring(0,7)}"`;
                    control = `${pref}<input type="month" id="${this.prefixId}${key}" name="${key}" data-field-name="${key}" data-field-value="${(data[key]).substring(0,7)}" placeholder="${ph}" ${addx}>${suff}`;
                    break;
                case "lookup":
                    var tbl = meta.option.table;
                    var tid = meta.option.id;
                    var txt = meta.option.text;
                    var mty = meta.option.empty;
                    control = `${pref}<select class="ui search dropdown" id="${this.prefixId}${key}" name="${key}" data-field-name="${key}" placeholder="${ph}">`;
                    control += `<option value="0"></option>`;
                    if (!mty) {
                        for (var i in Ref.Data[tbl]) {
                            var row = Ref.Data[tbl][i];
                            control += `<option value="${row[tid]}">${row[txt]}</option>`;
                        }
                    }
                    control += `</select>${suff}`;
                    break;
                case "boolean":
                    addx = (fval==0) ? "" : "checked";
                    var addy = "";
                    if (readonly) addy = "disabled='disabled'";
                    control = `${pref}<div class="ui toggle checkbox"><input type="checkbox" id="${this.prefixId}${key}" name="${key}" data-field-name="${key}" data-field-value="${data[key]}" placeholder="${ph}" ${addx} ${addy}><label></label></div>${suff}`;
                    break;
                default:
                    control = `${pref}<input type="text" id="${this.prefixId}${key}" name="${key}" data-field-name="${key}" data-field-value="${data[key]}" placeholder="${ph}" ${maxl} ${uclass} ${addx}>${suff}`;
                    break;
            }
            hasil += control;
            if (!ceem && cols > 1) {
                if (meta.separator || false) hasil += `</div><div class="column">`;
            }
        }
        if (cols > 1) hasil += `</div></div>`;
        hasil += `</form>`;
        return hasil;
    }

    viewCard(idx, color="purple") {
        var keys = Object.keys(this.Model);
        var indices = Object.keys(idx);
        var data = this.getById(idx);
        var hasil = `<table class='ui definition striped ${color} table'><tbody>`;
        for (var i in keys) {
            var key = keys[i];
            var meta = this.Model[key];
            if (meta.hide || false) continue;
            if (meta.noview || false) continue;
            if (meta.hidden || false) continue; // ini kenapa banyak ya?
            var tag = meta.useTag || false;
            var crlf = meta.crlf || false;
            var teks = data[key];
            if (meta.formatter && (!indices.includes(key))) {
                var cell = {
                    getValue: () => {return data[key];}
                }
                teks = meta.formatter(cell);
            }
            var style = "";
            if (crlf) {
                style = " style='overflow: auto; white-space: pre-wrap;' "
            }
            if (tag) {
                var fmt = meta.tagFormat || "";
                if (fmt == "") {
                    const ffm = meta.tagFormatField || "";
                    fmt = (data[ffm]).toLowerCase();
                }
                if (fmt == "xml") {
                    var rep = escapeTag(teks);
                    teks = '<pre>'+rep+'</pre>';
                }
                if ((fmt == "json")||(fmt == "fix")) {
                    var obj = JSON.parse(teks);
                    //var rep = JSON.stringify(obj, null, 4);
                    //teks = '<pre>'+rep+'</pre>';
                    teks = jsonToHTMLTable(obj,'vertical');
                }
            }
            hasil += `<tr><td class="collapsing">${meta.caption}</td><td${style}>${teks}</td></tr>`;
        }
        hasil += "</tbody></table>";
        return hasil;
    }

    xTabulator(domID, domHeight, table_name, ajaxUrl, param={}) {
        var self = this;
        var _tlayout = param.layout ?? "fitData";
        var _ajaxCallback = param.ajaxCallback ?? null;
        //var _ajaxConfig = param.ajaxConfig ?? "POST";
        var _ajaxContentType = param.ajaxContentType ?? "json";
        var _movableColumns = param.movableColumns ?? true;

        var _sortMode = param.sortMode ?? "remote";
        var _filterMode = param.filterMode ?? "remote";

        var _pagination = param.pagination ?? true;
        var _paginationMode = param.paginationMode ?? "remote";
        var _paginationCounter = param.paginationCounter ?? "rows";

        var obj = {
            placeholder:"No Data Available",
            height: domHeight+"px",
            selectableRows: 1,
            layout: _tlayout,
            selectablePersistence:true,
            persistenceMode: true,
            persistenceID: table_name,
            persistence: {
                columns: true
            },
            ajaxConfig: this.ajaxConfig,
            ajaxContentType: _ajaxContentType,
            ajaxURL: ajaxUrl,
            ajaxResponse: function(url, params, response){
                //url - the URL of the request
                //params - the parameters passed with the request
                //response - the JSON object returned in the body of the response.
                var resp = null;
                if (_ajaxCallback != null) resp = _ajaxCallback(url, params, response);
                if (resp == undefined) resp = response;
                if (resp.error==888 && resp.message == "Session expired.") {
                    if (window != window.parent) {
                        window.parent.location.reload();
                    } else {
                        window.location.reload();
                    }
                }
                self.setData(resp.data);
                self.setRawData(resp);
                return resp; //return the tableData property of a response json object
            },
            movableColumns: _movableColumns,
            //pagination: _pagination,
            //paginationMode: _paginationMode,
            //paginationCounter: _paginationCounter,
            //paginationInitialPage: 1,
            progressiveLoad:"scroll",
            progressiveLoadScrollMargin:100,
            paginationSize: 50,
            //sortMode: _sortMode,
            //filterMode: _filterMode,
            editTriggerEvent:"dblclick",
            columns: this.useCustomModel ? this.customModel : this.getModelTabulator()
        };

        if (_sortMode != null) obj["sortMode"] = _sortMode;
        if (_filterMode != null) obj["filterMode"] = _filterMode;
        if (param.initialData != undefined) {
            obj["data"] = param.initialData;
            this.setData(param.initialData);
        }
        if (param.initialFilter != undefined) obj["initialFilter"] = param.initialFilter;
        if (param.initialHeaderFilter != undefined) obj["initialHeaderFilter"] = param.initialHeaderFilter;
        if (param.initialSort != undefined) obj["initialSort"] = param.initialSort;
        if (param.rowFormatter != undefined) obj["rowFormatter"] = param.rowFormatter;
        if (param.autoColumns != undefined) obj["autoColumns"] = param.autoColumns;
        //if (param.ajaxResponse != undefined) obj["ajaxResponse"] = param.ajaxResponse;

        return new Tabulator("#"+domID, obj);
    }
}
