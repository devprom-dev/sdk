<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE
$view->extend('core/PageTable.php');
$view['slots']->start('_header');
?>
<link rel="stylesheet" href="/scripts/vis/vis.min.css?v=<?=$_SERVER['APP_VERSION']?>" />
<link rel="stylesheet" href="/styles/modules/deliverychart.css?v=<?=$_SERVER['APP_VERSION']?>" />
<script src="/scripts/vis/moment-with-locales.min.js?v=<?=$_SERVER['APP_VERSION']?>" type="text/javascript" charset="UTF-8"></script>
<script src="/scripts/vis/vis.min.js?v=<?=$_SERVER['APP_VERSION']?>" type="text/javascript" charset="UTF-8"></script>
<script src="/scripts/vis/handlebars-v4.1.0.js?v=<?=$_SERVER['APP_VERSION']?>" type="text/javascript" charset="UTF-8"></script>
<?php
$view['slots']->stop();
$view['slots']->output('_content');

