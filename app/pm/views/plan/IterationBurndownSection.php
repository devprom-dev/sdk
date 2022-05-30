<?php

class IterationBurndownSection extends InfoSection
{
    var $iteration_it;
    
    function __construct( $iteration_it ) {
		$this->iteration_it = $iteration_it;
        parent::__construct();
    }
    
 	function getCaption() {
 		return translate('Burndown');
 	}
 	
 	function drawBody()
 	{
		$report_it = getFactory()->getObject('PMReport')->getExact('iterationburndown');
		$url = $report_it->getUrl().'&release='.$this->iteration_it->getId();
		$chart_id = 'chart'.md5(get_class($this).$url.uniqid());

		echo '<div id="'.$chart_id.'" class="plot" url="'.$url.'" style="width:900px;height:340px;"><div class="document-loader"></div></div>';

		$flot = new FlotChartBurndownWidget();
		$flot->setUrl( getSession()->getApplicationUrl().'chartburndown.php?release_id='.$this->iteration_it->getId().'&json=1' );
		$flot->draw($chart_id, true);
	}
}