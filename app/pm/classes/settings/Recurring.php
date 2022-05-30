<?php
include "RecurringIterator.php";
include "persisters/RecurringDetailsPersister.php";

class Recurring extends Metaobject
{
	public function __construct() {
		parent::__construct('pm_Recurring');

		$this->addAttribute('RequestTemplates', 'REF_RequestTemplateId', text(1520), true);
        $this->addAttribute('TaskTemplates', 'REF_TaskTemplateId', text(3108), true);
        $this->addAttribute('AutoActions', 'REF_AutoActionId', text(2433), true);

		foreach( array('RequestTemplates', 'TaskTemplates', 'AutoActions') as $attribute ) {
            $this->addAttributeGroup($attribute, 'additional');
            $this->addAttributeGroup($attribute, 'recurring');
            $this->setAttributeEditable($attribute, false);
        }
        $this->addPersister(new RecurringDetailsPersister());
	}
	
	public function createIterator() {
		return new RecurringIterator($this);
	}
}