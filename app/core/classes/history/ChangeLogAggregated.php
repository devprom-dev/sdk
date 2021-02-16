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
    }
}