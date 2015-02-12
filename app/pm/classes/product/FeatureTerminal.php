<?php

include_once "Feature.php";
include "FeatureTerminalRegistry.php";

class FeatureTerminal extends Feature
{
	function __construct()
	{
		parent::__construct(new FeatureTerminalRegistry());
	}
}