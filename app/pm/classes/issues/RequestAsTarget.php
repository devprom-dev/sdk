<?php

include_once "Request.php";
include "RequestAsTargetRegistry.php";

class RequestAsTarget extends Request
{
	function __construct() {
		parent::__construct( new RequestAsTargetRegistry($this) );
	}
}