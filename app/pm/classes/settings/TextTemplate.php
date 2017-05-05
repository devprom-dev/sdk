<?php
include "TextTemplateIterator.php";
include "TextTemplateRegistry.php";
include "sorts/TextTemplateSortClause.php";

class TextTemplate extends Metaobject
{
	public function __construct() {
		parent::__construct('pm_TextTemplate', new TextTemplateRegistry($this));
        $this->setSortDefault(new TextTemplateSortClause());
        $this->setAttributeType('ObjectClass', 'REF_TextTemplateEntityId');
	}
	
	public function createIterator() {
		return new TextTemplateIterator($this);
	}

	public function getObjectClass() {
        return '';
    }
}