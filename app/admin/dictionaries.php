<?php

include 'header.php';
include 'views/dictionaries/SystemDictionaryPage.php';
include SERVER_ROOT_PATH . 'pm/classes/model/classes.php';

$page = new SystemDictionaryPage;
$page->render();

