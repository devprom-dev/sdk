<?php
include "MilestoneActualRegistry.php";

class MilestoneActual extends Milestone
{
 	function __construct() {
 		parent::__construct(new MilestoneActualRegistry($this));
	}
}