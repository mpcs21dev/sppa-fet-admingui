<?php
$Model = '{
    id:        {caption:"ID", title:"No", type:"numeric", autoValue:true, formatter: autoNumFormatter, frozen:true},
    name:      {caption:"Name", type:"string", frozen:true, upperCase:true, headerFilter: true, verify: (v)=>{
        if (v == "") {
            FiError("Error","Field Name required");
            return false;
        }
        return true;
    }},
    int_key:   {caption:"INT Key", type:"numeric", headerFilter: true, verify: (v,a)=>{
        if ((a.int_key == "")&&(a.str_key == "")) {
            FiError("Error","One of key fields (INT Key or STR Key) must have value");
            return false;
        }
        return true;
    }},
    str_key:   {caption:"STR Key", type:"string", upperCase:true, headerFilter: true, 
        formatter: (cell)=>{
            var v = cell.getValue();
            LastKey = v;
            return v;
        },
        verify: (v,a)=>{
            if ((a.int_key == "")&&(a.str_key == "")) {
                FiError("Error","One of key fields (INT Key or STR Key) must have value");
                return false;
            }
            return true;
        }
    },
    str_val:   {caption:"Value", type:"string", upperCase:false, headerFilter: true, 
        formatter: function(cell) {
            var hasil = "";
            var vlu = cell.getValue();
            const pl = vlu.length;
            //if (LastKey == "SMTP_PASS") console.log(LastKey,vlu);
            try {
                var row = cell.getRow();
                var rdat = row.getData();
                if (rdat["str_key"] == "SMTP_PASS") {
                    hasil = "&bull;".repeat(pl);
                } else {
                    hasil = vlu;
                }
            } catch (e) {
                if (LastKey == "SMTP_PASS") {
                    hasil = "&bull;".repeat(pl);
                } else {
                    hasil = vlu;
                }
            }
            return hasil;
        },
        verify: (v)=>{
            if (v == "") {
                FiError("Error","Field Value required");
                return false;
            }
            return true;
        }
    },
    updated_at:{caption:"Updated Date", type:"datetime", autoValue:true, formatter: datetimeFormatter},
    inserted_at:{caption:"Inserted Date", type:"datetime", autoValue:true, formatter: datetimeFormatter}
}';
$startup = cekLevel(2) ? "" : "
    \$id('btnAdd').remove();
    \$id('btnDelete').remove();
";
$ulevel = $usrx["ulevel"];
$PRM = array(
    "icon" => "check double",
    "caption" => "Reference",
    "accordionID" => "",
    "menuID" => "menu-ref",
    "apiList" => "api/?1/ref/ref/list",
    "apiCreate" => cekLevel(2) ? "api/?1/ref/ref/create" : "",
    "apiUpdate" => "api/?1/ref/ref/update",
    "apiDelete" => cekLevel(2) ? "api/?1/ref/ref/delete" : "",
    "labelField" => "name",
    "extraButtons" => "",
    "jsStartup" => $startup,
    "addAfterShow" => "",
    "editAfterShow" => "
        if (sel.str_key == 'SMTP_PASS') {
            var xel = \$id('fld-str_val');
            if (xel) xel.type = 'password'; 
        }
    ",
    "model" => $Model,
    "extraJS" => "
        LastKey = '';
        function checkAdd(row) {
            const name_ = row.get('name');
            const int_key_ = row.get('int_key');
            const str_key_ = row.get('str_key');
            const str_val_ = row.get('str_val');
            if (name_ == '') return [false,'Field Name cannot empty'];
            if ((int_key_ == '') && (str_key_ == '')) return [false,'One of key fields must have value'];
            if (str_val_ == '') return [false,'Field Value cannot empty'];
            return [true,'OK'];
        }
        function checkEdit(row) {
            const name_ = row.get('name');
            const int_key_ = row.get('int_key');
            const str_key_ = row.get('str_key');
            const str_val_ = row.get('str_val');
            if (name_ == '') return [false,'Field Name cannot empty'];
            if ((int_key_ == '') && (str_key_ == '')) return [false,'One of key fields must have value'];
            if (str_val_ == '') return [false,'Field Value cannot empty'];
            return [true,'OK'];
        }
    "
);

include_once "pages/cms-genform.php";
