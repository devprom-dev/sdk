<?php
include_once "ExportTemplateIterator.php";

class ExportTemplate extends Metaobject
{
	public function __construct() {
		parent::__construct('pm_ExportTemplate');
	}
	
	public function createIterator() {
		return new ExportTemplateIterator($this);
	}
}