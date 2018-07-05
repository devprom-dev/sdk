<?php

include ('header.php');
include ('views/maintenance/UpdatePage.php');

//$userIt = getFactory()->getObject('User')->getRegistry()->Query(array(new SortAttributeClause('IsAdmin.D')));
//$session = new AuthenticationAppKeyFactory();
//echo EnvironmentSettings::getServerUrl() . '/admin/updates.php?appkey='.$session->getKey($userIt->getId()).'&action=download&parms='.$updateName;

$page = new UpdatePage;

$page->render();
