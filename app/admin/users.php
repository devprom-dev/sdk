<?php
include ('header.php');
include ('views/UserPage.php');

$page = new UserPage;
$page->render();
