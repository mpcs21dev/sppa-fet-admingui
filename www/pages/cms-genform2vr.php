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
    "leftButtons" => "",
    "leftUppercase" => true,

    "rightModel" => "",
    "rightColor" => "green",
    "rightLabel" => "Right",
    "rightApiList" => "",
    "rightButtons" => "",
    "rightUppercase" => true,

    "labelField" => "", // field for deleting caption
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
    <!--
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
    -->
    <div class='laycol'>
        <div class='layrow'>
            <div class="vertical-text <?=$PPRM['leftColor']?>"><?=$PPRM['leftLabel'] ?></div>
            <div class='toolbar <?=$PPRM['leftColor']?>'><div class='ui vertical icon buttons'><?=$PPRM['leftButtons'] ?></div></div>
            <div id='table_left' class='ui <?=$PPRM['leftColor']?> table fixedmargin'></div>
        </div>
        <div class='separator'></div>
        <div class='layrow'>
            <div class="vertical-text <?=$PPRM['rightColor']?>"><?=$PPRM['rightLabel'] ?></div>
            <div class='toolbar <?=$PPRM['rightColor']?>'><div class='ui vertical icon buttons'><?=$PPRM['rightButtons'] ?></div></div>
            <div id='table_right' class='ui <?=$PPRM['rightColor']?> table fixedmargin'></div>
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
    var prevData = "";
    var w0 = 0;

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

        /*
        var w0 = Math.ceil(window.innerHeight),
            w1 = Math.ceil($("#kepala").outerHeight()),
            w2 = parseInt($("#leher").css("margin")),
            k1 = Math.ceil($("#kanan").outerHeight());
        */
        w0 = Math.ceil(window.innerHeight);

        //console.log({"w0":w0, "w1":w1, "w2":w2, "k1":k1});

        Tabll = frmLeft.xTabulator("table_left", w0 / 2 - 27, "cms_left", "<?=$PPRM['leftApiList']?>", {layout: "fitDataStretch"});
        Tablr = frmRight.xTabulator("table_right", w0 / 2 - 27, "cms_right", "<?=$PPRM['rightApiList']?>", {layout: "fitDataStretch"});

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
