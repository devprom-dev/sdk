<?php
include_once "ChangeLog.php";
include "ChangeLogAggregatedRegistry.php";

class ChangeLogAggregated extends ChangeLog
{
    function __construct()
    {
		parent::__construct( new ChangeLogAggregatedRegistry($this) );
		$this->addAttribute( 'ChangeDate', 'DATE', translate('Дата изменения'), false, false );
        $this->addAttribute( 'ChangeKind', 'VARCHAR', translate('Вид изменения'), false, false );
        $this->addAttribute( 'Project', 'REF_pm_ProjectId', translate('Проект'), false, false );

   		$system_attributes = array (
			'ObjectId',
			'ObjectUrl',
			'VisibilityLevel',
			'EntityRefName',
			'ClassName'
		);
		foreach( $system_attributes as $attribute ) {
 			$this->addAttributeGroup($attribute, 'system');
		}

		$this->setSortDefault( array(
			new SortChangeLogRecentClause()
		));

        $this->addAttribute('UserAvatar', '', translate('Автор'), true, false, '', 1);
        $this->setAttributeCaption( 'SystemUser', translate('Имя автора') );
        $this->setAttributeOrderNum( 'SystemUser', 2 );
        $this->setAttributeVisible('RecordModified', true);
    }
}