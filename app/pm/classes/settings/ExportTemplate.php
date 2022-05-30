<?php
include_once "ExportTemplateIterator.php";

class ExportTemplate extends Metaobject
{
	public function __construct() {
		parent::__construct('pm_ExportTemplate');
		$this->setAttributeVisible('Options', false);
        $this->addAttributeGroup('BulletListTemplate', 'additional');
        $this->addAttributeGroup('NumberedListTemplate', 'additional');
	}
	
	public function createIterator() {
		return new ExportTemplateIterator($this);
	}
}