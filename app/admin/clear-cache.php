<?php
include (dirname(__FILE__).'/../common.php');
include_once "install/Installable.php";
include_once "install/ClearCache.php";

$command = new ClearCache();
$command->install();

exit(header('Location: /'));