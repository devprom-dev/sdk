<?php

include SERVER_ROOT_PATH."admin/classes/SystemDictionary.php";
include 'SystemDictionaryForm.php';
include 'SystemDictionaryItemsTable.php';
include 'SystemDictionaryTable.php';

class SystemDictionaryPage extends AdminPage
{
	function __construct()
	{
		if ( $_REQUEST['entity'] != '' ) {
			$object = getFactory()->getObject($_REQUEST['entity']);
			$_REQUEST['dict'] = $object->getEntityRefName();
		}
			
		parent::__construct();
	}

	function getObject() {
		return $this->getDictionary();
	}
	
	function getDictionary()
	{
		switch ( $_REQUEST['dict'] )
		{
			case 'pm_ProjectRole':
				$object_it = $this->getObjectIt();
				$object = getFactory()->getObject('ProjectRoleBase');
				if ( is_object($object_it) && $object_it->get('ReferenceName') == 'lead' ) {
					$object->setAttributeVisible('ReferenceName', false);
				}
				break;

			case 'pm_TaskType':
				$object = getFactory()->getObject('TaskTypeBase');
				break;

			case 'pm_TestExecutionResult':
				$object = getFactory()->getObject('TestExecutionResultBase');
				break;

			case 'Priority':
            case 'pm_Severity':
			case 'pm_Importance':
			case 'pm_IssueType':
			case 'pm_ChangeRequestLinkType':
			case 'cms_Language':
            case 'pm_FinancingType':
			    $object = getFactory()->getObject($_REQUEST['dict']);
			    break;
		}
		
		if ( is_object($object) ) {
			$object->addSort( new SortAttributeClause('OrderNum') );
		}
		
		return $object;
	}

	function getTable()
	{
		$object = $this->getDictionary();

		if ( is_object($object) )
		{
			return new SystemDictionaryItemsTable( $object );
		}

		return new SystemDictionaryTable( new SystemDictionary() );
	}

	function getEntityForm()
	{
		$object = $this->getDictionary();

		if ( is_object($object) )
		{
			return new SystemDictionaryForm( $object );
		}

		return null;
	}
}
