<?php
include 'CoPageTable.php';
include "CoPageNavigation.php";

class CoPage extends Page
{
	function getRenderParms()
	{
		return array_merge( parent::getRenderParms(), array (
			'caption_template' => 'co/PageTitle.php',
		));
	}
	
	function getTabsTemplate() {
		return 'co/PageTabs.php'; 	
	}

	function buildNavigationParms() {
        return new CoPageNavigation($this);
    }
}