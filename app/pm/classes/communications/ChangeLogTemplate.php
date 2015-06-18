<?php
include "ChangeLogTemplateRegistry.php";

class ChangeLogTemplate extends ChangeLog
{
	function __construct() {
		parent::__construct(new ChangeLogTemplateRegistry($this));
	}
}