<?php

$etagFile = md5(__FILE__)."-".md5($_REQUEST['v']);
$etagHeader=(isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);
if ($etagHeader == $etagFile) {
	exit(header("HTTP/1.1 304 Not Modified"));
}

$cachedir = dirname(__FILE__) . '/cache';
$cssdir   = dirname(__FILE__) . '/';
$jsdir    = dirname(__FILE__) . '/scripts';

// Determine the directory and type we should use
switch ($_GET['type']) {
    case 'css':
        $base = $cssdir;
        $type = 'css';
        break;
    case 'print':
        $base = $cssdir;
        $type = 'print';
        break;
    case 'javascript':
    default:
        $base = $jsdir;
        $type = 'javascript';
        break;
};

$language = $_GET['l'];
if ( !in_array($language, array('ru','en')) ) $language = 'en';

switch ( $type )
{
    case 'css':
        $files =
            'styles/jquery-ui/jquery-ui-1.10.0.custom.css,' .
            'styles/bootstrap/css/bootstrap.css,'.
            'styles/bootstrap/css/bootstrap-responsive.css,'.
            'styles/select/jquery_select.css,' .
            'styles/fancybox/jquery.fancybox.min.css,'.
            'styles/newlook/main.css,'.
            'styles/newlook/medium-fonts.css,'.
            'scripts/color-picker/colorPicker.css,'.
            'styles/newlook/board.css,'.
            'styles/wysiwyg/codes.css,'.
            'styles/newlook/sidebar.css,'.
            'styles/newlook/ui.fancytree.css,'.
            'styles/newlook/extended.css,'.
            'styles/bootstrap/css/bootstrap-multiselect.css,'.
            'styles/jquery/perfect-scrollbar.min.css';
        break;

    case 'print':
        $type = 'css';
        $files =
            'styles/legacy/style_print.css';
        break;

    default:
        switch( $_GET['asset'] )
        {
            case '1':
                $files =
                    'jquery/jquery-3.5.1.min.js,'.
                    'jquery/jquery-migrate-1.2.1.js,'.
                    'jquery/jquery.form.js,'.
                    'keyboard/mousetrap.min.js,'.
                    'fancybox/jquery.fancybox.min.js,'.
                    'excanvas/excanvas.compiled.js,'.
                    'flot/jquery.flot.min.js,'.
                    'flot/jquery.flot.pie.min.js,'.
                    'flot/jquery.flot.time.min.js,'.
                    'flot/jquery.flot.stack.min.js,'.
                    'flot/jquery.flot.crosshair.min.js,'.
                    'flot/jquery.flot.threshold.min.js,'.
                    'flot/jquery.flot.tickrotor.js,'.
                    'jquery/jquery.cookies.2.2.0.min.js,' .
                    'modernizr/modernizr.js,' .
                    'jquery/jquery.base64.min.js,'.
                    'jquery/jquery.ba-resize.min.js,'.
                    'jquery/imagesloaded.pkgd.min.js,'.
                    'jquery/paste.js,'.
                    'jquery/jquery.imageLens.js,'.
                    'color-picker/jquery.colorPicker.min.js,'.
                    'locale/underi18n.js,' .
                    'time/jstz-1.0.4.min.js,'.
                    'clipboard/clipboard.min.js,'.
                    'annotation/jquery.mark.min.js,'.
                    'pm/locale/'.$language.'/resources.js,'.
                    'pm/comments.js,'.
                    'pm/common.js,'.
                    'pm/ui.js,'.
                    'pm/filter.js,'.
                    'flow/circle-progress.min.js,'.
                    'flow/flow.min.js,'.
                    'pm/upload.js,'.
                    'pm/board.js,'.
                    'pm/document.js,'.
                    'pm/treegrid.js,'.
                    'pm/shortcuts.js';
                break;
        }

        $files .=
            ',bootstrap/bootstrap.js,'.
            'bootstrap/bootstrap-filestyle.min.js,'.
            'bootstrap/bootstrap-contextmenu.js,'.
            'bootstrap/bootstrap-multiselect.js,'.
            'jquery-ui/jquery-ui.js,'.
            'jquery-ui/jquery.ui.touch-punch.min.js,'.
            'fancytree/jquery.fancytree.min.js,' .
            'fancytree/jquery.fancytree.ui-deps.js,' .
            'fancytree/jquery.fancytree.dnd5.js,' .
            'fancytree/jquery.fancytree.persist.js,' .
            'fancytree/jquery.fancytree.table.js,' .
            'scrollbar/perfect-scrollbar.min.js,'.
            'jquery/jquery.peekabar.js,'.
            'jquery/jquery.cookie.js,'.
            'hashids/hashids.min.js,';

        switch ( $_GET['dpl'] ) {
            case 'ru':
                $files .= 'jquery-ui/i18n/jquery.ui.datepicker-ru.js,datejs/date-ru-RU.js';
                break;
            case 'en-US':
                $files .= 'jquery-ui/i18n/jquery.ui.datepicker-en-US.js,datejs/date-en-US.js';
                break;
            case 'en-GB':
                $files .= 'jquery-ui/i18n/jquery.ui.datepicker-en-GB.js,datejs/date-en-GB.js';
                break;
        }
}

header('Cache-Control: public');
header("ETag: ". $etagFile);
header("Last-Modified: Fri, 01 Apr 2012 12:33:50 GMT");
header("Content-Type: text/".$type."; charset=utf-8");

if(!ob_start("ob_gzhandler")) ob_start();
foreach( explode(',', $files) as $element ) {
    echo file_get_contents($base . '/' . $element);
    echo "\n\n";
    ob_flush();
}
ob_end_clean();