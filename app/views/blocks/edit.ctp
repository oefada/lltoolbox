<style>
    #dataDebug {
        display: none;
    }

    h1 {
        font-size: 200%;
        color: #888;
        margin-bottom: 20px;
    }

    div.title-header {
        padding-bottom: 0;
    }

    #content-area {
        padding-top: 0;
        padding-left: 10px;
    }

    #editorDiv, #dataDiv {
        margin-left: 257px;
        margin-right: 16px;
        padding-left: 16px;
        min-height: 400px;
    }

    #dataDiv {
        white-space: pre;
        font-family: monospace;
        display: block;
    }

    .editorPanel {
        border-color: #cccccc;
        border-width: 1px;
        border-style: solid;
        min-height: 300px;
        padding: 8px;
        background: white;
        box-shadow: 3px 3px 10px #dddddd;
    }

    #blockTree {
        min-height: 400px;
        background-color: #fcfcff;
    }

    #previewFrame {
        margin: 0;
        padding: 0;
        box-shadow: 3px 3px 10px #88bbdd;
        border: none;
        width: 100%;
        height: 400px;
    }

    #blockToolbar {
        background-color: #dddddd;
        margin: 24px 0;
    }

    #blockToolbar a, div.pressMe a {
        height: 16px;
        text-decoration: none;
        color: black;
        display: inline-block;
        padding: 2px 4px;;
        margin: 4px;
        border-style: solid;
        border-color: #cccccc;
        border-width: 1px;
        border-radius: 4px;
        background: #eeeeee;
        opacity: 0.3;
    }

    #blockToolbar a.active {
        opacity: 1.0;
    }

    #blockToolbar a:hover {
        background-color: #ffffff;
        color: blue;
    }

    #blockToolbar a img, div.pressMe img {
        width: 16px;
        height: 16px;
        vertical-align: middle;
        margin-right: 4px;
    }

    #blockToolbar a span {
        font-size: 10px;
        font-weight: bold;
    }

    div.editorParameter {
        margin-bottom: 20px;
    }

    input, textarea {
        margin: 8px;
    }

    input[type="text"], textarea {
        width: 80%;
    }

    textarea {
        height: 100px;
    }

    #previewDiv {
        margin: 32px 0;
        padding: 64px 128px;
        background: #fcfcfc;
    }

    #previewFrame {
        background: white;
        width: 100%;
        height: 600px;
        box-shadow: 10px 10px 20px #aaaaaa;
        border-color: #ffcccc;
        border-width: 5px;
        border-style: dashed;
    }

    #editorDiv a.lineJump {
        text-decoration: none;
        color: #000066;
    }

    #editorDiv a.lineJump:hover {
        text-decoration: none;
        color: #0000CC;
    }

    div.imageSelector {
        display: inline-block;
        margin: 8px;
        padding: 8px;
        background: #eeeeee;
        text-align: center;
        font-weight: bold;
        font-family: Arial;
        border-radius: 8px;
        border-style: solid;
        border-color: #dddddd;
        border-width: 1px;
    }
</style>
<?php
$this->pageTitle = 'Blocks Editor';
$this->set('hideSidebar', true);
?>

<div>
    <ul>
        <li>Currently editing: <b id="blockPageUrl"><?php echo $blockPageUrl; ?></b></li>
        <li><?php echo $html->link(
                'View Revisions',
                array(
                    'action' => 'revisions',
                    $blockPageId
                )
            );
            ?></li>
        <li><?php echo $html->link('List all Blocks', array('action' => 'index')); ?></li>
    </ul>
</div>

<br/>

<div class="pressMe">
    <a href="#save"><img src="http://ui.llsrv.us/images/icons/silk/database_save.png"/>Save and Preview</a>
    <a href="#publish" style="float:right;"><img src="http://ui.llsrv.us/images/icons/silk/exclamation.png"/>Publish</a>
</div>

<div id="blockToolbar"></div>

<div class="clearfix"></div>

<div id="blockTree" class="demo" style="float: left; width: 256px; overflow-x: scroll;"></div>

<div id="editorDiv">
    <div class="editorPanel"></div>
</div>

<div style="clear: both;"></div>

<div id="helpWindow"></div>

<div id="previewDiv">
    <h2>Preview:</h2>

    <div id="previewLink"></div>
    <br/>

    <div><a href="#top">Go back to top</a></div>
    <br/>
    <iframe id="previewFrame" src="/blocks/preview"></iframe>
</div>

<div id="dataDebug">
    DEBUG
</div>

<br/>
<br/>

<script type="text/javascript">
    (function () {
        var hex2bin = function (hexData) {
            var r = '';
            for (var i = 0; i < hexData.length; i += 2) {
                r += unescape('%' + hexData.substr(i, 2));
            }
            return r;
        };
        window['editorLoadData'] = JSON.parse(hex2bin('<?php echo $blockData; ?>'));
    })();
</script>

<script type="text/javascript" src="/js/blocks.js?v=<?=$jsVersion;?>"></script>
