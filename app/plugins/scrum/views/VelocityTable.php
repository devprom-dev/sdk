<?php

include "VelocityChart.php";

include dirname(__FILE__)."/../classes/ReleaseMetadataVelocityBuilder.php";
include dirname(__FILE__)."/../classes/IterationMetadataVelocityBuilder.php";

class VelocityTable extends PMPageTable
{
	function getList()
	{
		return new VelocityChart( $this->getObject() );
	}
} 