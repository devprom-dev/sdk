<?php

class ReleaseBurndownSection extends InfoSection
{
    var $release_it;
    
    function __construct( $release_it )
    {
		$this->release_it = $release_it;
    }
    
 	function getCaption()
 	{
 		return translate('Burndown');
 	}
 	
 	function drawBody()
 	{
		$report_it = getFactory()->getObject('PMReport')->getExact('releaseburndown');
		$url = $report_it->getUrl().'&release='.$this->release_it->getId();
		$chart_id = 'chart'.md5($url);

		echo '<div id="'.$chart_id.'" class="plot" url="'.$url.'" style="width:900px;height:340px;"></div>';

		$flot = new FlotChartBurndownWidget();
		$flot->setUrl( getSession()->getApplicationUrl().'chartburndownversion.php?version='.$this->release_it->getId().'&json=1' );
		$flot->draw($chart_id);
	}
}