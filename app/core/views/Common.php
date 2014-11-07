<?php

include SERVER_ROOT_PATH.'ext/vendor/autoload.php';

$dirname = dirname(__FILE__).'/';

include ($dirname.'Page.php');
include ($dirname.'FormAsync.php');
include ($dirname.'PopupMenu.php');
include ($dirname.'../c_more.php');

include_once SERVER_ROOT_PATH.'core/methods/SettingsWebMethod.php';
