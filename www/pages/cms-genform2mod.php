<?php
$DPRM = array(
    "icon" => "thumbs up outline",
    "caption" => "Form Caption",
    "accordionID" => "",
    "menuID" => "menu-",

    "leftModel" => "",
    "leftColor" => "orange",
    "leftLabel" => "Left",
    "leftApiList" => "",
    "leftApiCreate" => "",
    "leftApiUpdate" => "",
    "leftApiDelete" => "",
    "leftButtons" => "",
    "leftUppercase" => true,
    "fnLeftRefresh" => "btnLRefresh_click",
    "fnLeftView" => "btnLView_click",
    "fnLeftAdd" => "btnLAdd_click",
    "fnLeftEdit" => "btnLEdit_click",
    "fnLeftDelete" => "btnLDelete_click",
    "leftTabulatorOption" => "",
    "leftIDField" => "id",
    "leftTextField" => "",
    "leftAddAfterShow" => "",
    "left_add_readform_isedit" => "false",
    "left_add_readform_getro" => "false",
    "leftEditAfterShow" => "",
    "left_edt_readform_isedit" => "true",
    "left_edt_readform_getro" => "false",

    "rightModel" => "",
    "rightColor" => "green",
    "rightLabel" => "Right",
    "rightApiList" => "",
    "rightApiCreate" => "",
    "rightApiUpdate" => "",
    "rightApiDelete" => "",
    "rightButtons" => "",
    "rightUppercase" => true,
    "fnRightRefresh" => "btnRRefresh_click",
    "fnRightView" => "btnRView_click",
    "fnRightAdd" => "btnRAdd_click",
    "fnRightEdit" => "btnREdit_click",
    "fnRightDelete" => "btnRDelete_click",
    "rightTabulatorOption" => "",
    "rightIDField" => "id",
    "rightTextField" => "",
    "rightAddAfterShow" => "",
    "right_add_readform_isedit" => "false",
    "right_add_readform_getro" => "false",
    "rightEditAfterShow" => "",
    "right_edt_readform_isedit" => "true",
    "right_edt_readform_getro" => "false",

    "jsStartup" => "",
    "extraJS" => ""
);
// $PRM
$PPRM = array();
foreach ($DPRM as $key=>$val) $PPRM[$key] = array_key_exists($key, $PRM) ? $PRM[$key] : $val;
?><!--<div id="kepala" class="ui top attached segment">
    <div class="ui header"><h1><i class="<?=$PPRM['icon']?> icon"></i> <?=$PPRM["caption"]?></h1></div>
</div>-->
<div id="leher" class="attached standard">
    <div class="ui grid">
        <div class="eight wide column">
            <div id="kiri" class="attached">
                <div class="ui mini <?=$PPRM['leftColor']?> tag label"><h5><?=$PPRM["leftLabel"]?></h5></div>
                <?=$PPRM['leftButtons'] ?>
            </div>
            <div id="table_left" class="ui <?=$PPRM['leftColor']?> table"></div>
        </div>
        <div class="eight wide column">
            <div id="kanan" class="attached">
                <div class="ui mini <?=$PPRM['rightColor']?> tag label"><h5><?=$PPRM["rightLabel"]?></h5></div>
                <?=$PPRM['rightButtons'] ?>
            </div>
            <div id="table_right" class="ui <?=$PPRM['rightColor']?> table"></div>
        </div>
    </div>
</div>
<!-- <div id="badan" class="attached standard-full"></div> -->
<script type="text/javascript">
    var Tabll;
    var Tablr;
    var AccordionMenuID = "<?=$PPRM['accordionID']?>";
    var MenuID = "<?=$PPRM['menuID']?>";
    var frmLeft = new Formation(<?=$PPRM['leftUppercase']?>);
    var frmRight = new Formation(<?=$PPRM['rightUppercase']?>);

    var leftData = null;
    var leftID = 0;

    frmLeft.setModel(<?=$PPRM['leftModel']?>);
    frmRight.setModel(<?=$PPRM['rightModel']?>);

    mod_startup = () => {
        // open accordion and activate menu
        try {
            if (AccordionMenuID != "") $('#'+AccordionMenuID).accordion('open',0);
            if (MenuID != "") $id(MenuID).classList.add("active");
        } catch (err) {
            // do nothing;
        }

        var w0 = Math.ceil(window.innerHeight),
            w1 = 0; //Math.ceil($("#kepala").outerHeight()),
            w2 = parseInt($("#leher").css("margin")),
            k1 = Math.ceil($("#kanan").outerHeight());

        //console.log({"w0":w0, "w1":w1, "w2":w2, "k1":k1});

        Tabll = frmLeft.xTabulator("table_left", w0-w1-k1-w2-w2-w2, "cms_left", "<?=$PPRM['leftApiList']?>", {layout: "fitDataStretch"});
        Tablr = frmRight.xTabulator("table_right", w0-w1-k1-w2-w2-w2, "cms_right", "<?=$PPRM['rightApiList']?>", {layout: "fitDataStretch"});

        Tabll.on("rowClick", function(e, row){
            //e - the click event object
            //row - row component
            //console.log(row.getData());
            leftData = row.getData();
            leftID = leftData.ID;
            //Tablr.replaceData(`<?=$PPRM['rightApiList']?>/${leftID}`);
        });

        <?=$PPRM["jsStartup"]?>
    }

    <?= $PPRM["extraJS"] ?>
</script>
