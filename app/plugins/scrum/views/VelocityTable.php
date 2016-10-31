<?php
include "VelocityChart.php";

class VelocityTable extends PMPageTable
{
	function getList()
	{
		return new VelocityChart( $this->getObject() );
	}
} 