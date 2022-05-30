<?php
include_once "ChangeLog.php";
include "ChangeLogWhatsNewRegistry.php";
include "predicates/ChangeLogSinceNotificationFilter.php";

class ChangeLogWhatsNew extends ChangeLog
{
    function __construct()
    {
		parent::__construct( new ChangeLogWhatsNewRegistry($this) );

   		$visible_attributes = array (
            'Content',
            'RecordModified',
            'SystemUser'
		);
		foreach( array_keys($this->getAttributes()) as $attribute ) {
            $this->addAttributeGroup($attribute, 'nonbulk');
		    if ( in_array($attribute, $visible_attributes) ) continue;
 			$this->addAttributeGroup($attribute, 'system');
            $this->setAttributeVisible($attribute, false);
		}
        $this->setAttributeVisible('RecordModified', true);

        $this->addAttribute( 'Project', 'REF_pm_ProjectId', translate('Проект'), false, false );

		$this->setSortDefault( array(
			new SortChangeLogRecentClause()
		));
    }
}