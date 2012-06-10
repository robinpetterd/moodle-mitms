<?php

require_once('../../config.php');
$courseid = optional_param('course', SITEID, PARAM_INT);

$editorlanguage = substr(current_language(), 0, 2);
$SESSION->lang = $editorlanguage;

$directionality = get_string('thisdirection');
$strtime        = get_string('strftimetime');
$strdate        = get_string('strftimedaydate');

/*
 * This section configures the TinyMCE toolbar buttons on and off
 * depending on the Moodle settings
 *
 * The changes are applied on a global basis,
 * ..... but there is scope here to modify and restrict the config
 * on a role basis, course basis, user basis, etc. if so desired.
 *
 */
if (empty($CFG->tinymcehidebuttons)) {
    $CFG->tinymcehidebuttons = '';
}

$editorhidebuttons = str_replace(' ', ',', $CFG->tinymcehidebuttons);

$editorhidebuttons1 = $editorhidebuttons . ',visualaid,styleselect';

$editorhidebuttons = 'theme_advanced_disable : "'.$editorhidebuttons1.'",';

$editorhidebuttons = str_replace('fontsize',             'fontsizeselect',       $editorhidebuttons);
$editorhidebuttons = str_replace('subscript',            'sub',                  $editorhidebuttons);
$editorhidebuttons = str_replace('superscript',          'sup',                  $editorhidebuttons);
$editorhidebuttons = str_replace('insertorderedlist',    'numlist',              $editorhidebuttons);
$editorhidebuttons = str_replace('insertunorderedlist',  'bullist',              $editorhidebuttons);
$editorhidebuttons = str_replace('createanchor',         'anchor',               $editorhidebuttons);
$editorhidebuttons = str_replace('createlink',           'link',                 $editorhidebuttons);
$editorhidebuttons = str_replace('htmlmode',             'code',                 $editorhidebuttons);
$editorhidebuttons = str_replace('insertchar',           'charmap',              $editorhidebuttons);
$editorhidebuttons = str_replace('insertimage',          'image',                $editorhidebuttons);
$editorhidebuttons = str_replace('inserthorizontalrule', 'hr',                   $editorhidebuttons);
$editorhidebuttons = str_replace('formatblock',          'formatselect',         $editorhidebuttons);
$editorhidebuttons = str_replace('clean',                'cleanup,removeformat', $editorhidebuttons);

// insertsmile,

$pieces = explode(",", $editorhidebuttons1);

$spellcheck = '';
if (! in_array("spellcheck", $pieces)) {
    $spellcheck = 'spellchecker,';
}
$inserttable = '';
if (! in_array("inserttable", $pieces)) {
    $inserttable = 'tablecontrols,separator,';
}
$search_replace = '';
if (! in_array("search_replace", $pieces)) {
    $search_replace = 'search,replace,separator,';
}
$lefttoright = '';
if (! in_array("lefttoright", $pieces)) {
    $lefttoright = 'ltr,separator,';
}
$righttoleft = '';
if (! in_array("righttoleft", $pieces)) {
    $righttoleft = 'rtl,separator,';
}
$cleanup = '';
if (! in_array("cleanup", $pieces)) {
    $cleanup = 'cleanup,removeformat,separator,';
}
$fontselect = '';
if (! in_array("fontname", $pieces)) {
    $fontselect = 'fontselect,';
}
$fontsize = '';
if (! in_array("fontsize", $pieces)) {
    $fontsize = 'fontsizeselect,';
}
$forecolor = '';
if (! in_array("forecolor", $pieces)) {
    $forecolor = 'forecolor,';
}
$hilitecolor = '';
if (! in_array("hilitecolor", $pieces)) {
    $hilitecolor = 'backcolor,';
}
$popupeditor = '';
if (! in_array("popupeditor", $pieces)) {
    $popupeditor = 'fullscreen,';
}

$editoraddbuttons3 = 'theme_advanced_buttons3_add : "'.$fontselect.$fontsize.$forecolor.$hilitecolor.'",';
$editoraddbuttons4 = 'theme_advanced_buttons4 : "'.$spellcheck.$search_replace.$inserttable.$lefttoright.$righttoleft.$popupeditor.$cleanup.'",';

/*
 *
 * ********************************************************************************************************
 *
 */


$temp = $_SERVER["REQUEST_URI"];
$temp = explode('/', $temp);
$root = $temp[1];

$configuration = <<<EOF

function createHTMLArea(id) {

    random       = Math.ceil(1000*Math.random());
    editor       = 'editor'+random;
    editorsubmit = 'editorsubmit'+random;

    tinyMCE.init({
        mode     : "exact",
        elements : id,
        theme    : "advanced",

        plugins : "safari,spellchecker,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,pagebreak,",
        spellchecker_languages : "+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv",

        plugin_insertdate_dateFormat : "%Y-%m-%d",
        plugin_insertdate_timeFormat : "%H:%M:%S",

        content_css : "/$root/lib/editor/tinymce/examples/css/content.css",

        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "top",
        theme_advanced_statusbar_location : "bottom",

        theme_advanced_resize_horizontal : true,
        theme_advanced_resizing : true,
        apply_source_formatting : true,

        $editorhidebuttons
        $editoraddbuttons3
        $editoraddbuttons4

    });

document.getElementById('ed').form.editorsubmit_43 = document.getElementById('ed').form.onsubmit;
document.getElementById('ed').form.onsubmit = function() { 
    tinyMCE.triggerSave(); 
    document.getElementById('ed').form.editorsubmit_43(); 
    document.getElementById('ed').form.editorsubmit_43 = null;
}

}

EOF;


echo <<<EOF
    tinyMCE.init({
        mode: "textareas",
        relative_urls: false,
        editor_selector: "form-textarea-simple",
        document_base_url: "$CFG->httpswwwroot",
        theme: "simple",
        skin: "o2k7",
        skin_variant: "silver",
        apply_source_formatting: true, 
        remove_script_host: false,
        entity_encoding: "raw",
        language: "$editorlanguage",
        directionality: "$directionality",
        plugins: "spellchecker,emoticons,paste,directionality,contextmenu"
    });
    tinyMCE.init({
        mode: "textareas",
        relative_urls: false,
        editor_selector: "form-textarea-advanced",
        document_base_url: "$CFG->httpswwwroot",
        theme: "advanced",
        skin: "o2k7",
        skin_variant: "silver",
        apply_source_formatting: true, 
        remove_script_host: false,
        entity_encoding: "raw",
        language: "$editorlanguage",
        directionality: "$directionality",
        plugins: "safari,spellchecker,table,style,layer,advhr,advimage,advlink,emoticons,inlinepopups,media,searchreplace,paste,directionality,fullscreen,moodleimage,moodlenolink,dragmath,nonbreaking,contextmenu,insertdatetime,save,iespell,preview,print,noneditable,visualchars,xhtmlxtras,template,pagebreak",
        plugin_insertdate_dateFormat : "$strdate",
        plugin_insertdate_timeFormat : "$strtime",
        theme_advanced_font_sizes: "1,2,3,4,5,6,7",
        theme_advanced_layout_manager: "SimpleLayout",
        theme_advanced_toolbar_align : "left",
        theme_advanced_buttons1: "fontselect,fontsizeselect,formatselect,styleselect",
        theme_advanced_buttons2: "undo,redo,|,search,spellchecker,|,bold,italic,underline,strikethrough,sub,sup,|,emoticons,image,dragmath,advhr,nonbreaking,charmap",
        theme_advanced_buttons3: "forecolor,backcolor,|,ltr,rtl,|,bullist,numlist,outdent,indent,|,link,unlink,moodlenolink,anchor,|,insertdate,inserttime",
        theme_advanced_buttons4: "selectall,cleanup,removeformat,pastetext,pasteword,|,table,insertlayer,styleprops,visualchars,|,code,preview,|,justifyleft,justifycenter,justifyright,justifyfull",
        theme_advanced_fonts: "Trebuchet=Trebuchet MS,Verdana,Arial,Helvetica,sans-serif;Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;Georgia=georgia,times new roman,times,serif;Tahoma=tahoma,arial,helvetica,sans-serif;Times New Roman=times new roman,times,serif;Verdana=verdana,arial,helvetica,sans-serif;Impact=impact;Wingdings=wingdings", 
        theme_advanced_resize_horizontal: true,
        theme_advanced_resizing: true,
        theme_advanced_toolbar_location : "top",
        theme_advanced_statusbar_location : "bottom",
        file_browser_callback : "mce_moodlefilemanager",
        width : "100%",

EOF;
// the xhtml ruleset must be the last one - no comma at the end of the file
readfile('tinymce/xhtml_ruleset.txt');
echo <<<EOF
    });

    function mce_toggleEditor(id) {
        tinyMCE.execCommand('mceToggleEditor',false,id);
    }
    function mce_moodlefilemanager(field_name, url, type, win) {
        tinyMCE.activeEditor.windowManager.open({
            file: "{$CFG->httpswwwroot}/lib/editor/tinymce/jscripts/tiny_mce/plugins/moodlelink/link.php?id={$courseid}",
            width: 480,  
            height: 380,
            resizable: "yes",
            inline: "yes",  
            close_previous: "no"
        }, {
            window: win,
            input: field_name
        });
        return false;
    }
    function mce_saveOnSubmit(id) {
        var prevOnSubmit = document.getElementById(id).form.onsubmit;
        document.getElementById(id).form.onsubmit = function() { 
            tinyMCE.triggerSave(); 
            var ret = true;
            if (prevOnSubmit != undefined) {
              if (prevOnSubmit()) {
                ret = true;
                prevOnSubmit = null;
              } else {
                ret = false;
              }
            }
            return ret;
        };
    }
EOF;
?>
