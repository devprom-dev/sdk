<?php

include SERVER_ROOT_PATH."admin/classes/SystemDictionary.php";

include ('DictionaryForm.php');
include ('DictionaryItemsTable.php');
include ('DictionaryTable.php');

class DictionaryPage extends AdminPage
{
	function __construct()
	{
		global $_REQUEST, $model_factory;
			
		if ( $_REQUEST['entity'] != '' )
		{
			$object = $model_factory->getObject($_REQUEST['entity']);
			$_REQUEST['dict'] = $object->getEntityRefName();
		}
			
		parent::Page();
	}

	function getObject()
	{
		return $this->getDictionary();
	}
	
	function getDictionary()
	{
		switch ( $_REQUEST['dict'] )
		{
			case 'pm_ProjectRole':
				
				$object_it = $this->getObjectIt();
				
				$object = getFactory()->getObject('ProjectRoleBase');
				
				if ( is_object($object_it) && $object_it->get('ReferenceName') == 'lead' )
				{
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
			case 'pm_Importance':
			case 'pm_IssueType':
			case 'pm_ChangeRequestLinkType':
			case 'cms_Language':
				
			    $object = getFactory()->getObject($_REQUEST['dict']);
			    
			    break;
		}
		
		if ( is_object($object) )
		{
			$object->addSort( new SortAttributeClause('OrderNum') );
		}
		
		return $object;
	}

	function getTable()
	{
		$object = $this->getDictionary();

		if ( is_object($object) )
		{
			return new DictionaryItemsTable( $object );
		}

		return new DictionaryTable( new SystemDictionary() );
	}

	function getForm()
	{
		$object = $this->getDictionary();

		if ( is_object($object) )
		{
			return new DictionaryForm( $object );
		}

		return null;
	}
}
