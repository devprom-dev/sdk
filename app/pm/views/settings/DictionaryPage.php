<?php

include "DictionaryItemForm.php";
include "ProjectRoleForm.php";
include "TaskTypeForm.php";
include "CustomAttributeEntityForm.php";
include "CustomAttributeFinalForm.php";
include "DictionaryItemsTable.php";
include "DictionaryTable.php";
include "TestExecutionResultForm.php";

include SERVER_ROOT_PATH."pm/views/workflow/TransitionForm.php";
include SERVER_ROOT_PATH."pm/views/workflow/StateForm.php";
include SERVER_ROOT_PATH."pm/views/workflow/StateTable.php";
include SERVER_ROOT_PATH."pm/views/templates/ObjectTemplateTable.php";
include SERVER_ROOT_PATH."pm/views/wiki/RequirementTypeForm.php";

class DictionaryPage extends PMPage
{
 	function __construct()
 	{
 		global $_REQUEST, $model_factory;
 		
 		if ( $_REQUEST['entity'] != '' )
 		{
 			$object = $model_factory->getObject($_REQUEST['entity']);
 			$_REQUEST['dict'] = $_REQUEST['entity']; 
 		}
 		
 		parent::__construct();
 		
 		if ( $this->needDisplayForm() ) $this->buildFormSections( $this->getObjectIt() );
 	}
 	
 	function getObject()
 	{
 		return $this->getDictionary();
 	}
 	
 	function getDictionary()
 	{
 		global $_REQUEST, $model_factory;
 		
		getSession()->addBuilder( new StateBaseModelBuilder() );
 		
 		switch ( $_REQUEST['dict'] )
 		{
 			case 'pm_ProjectRole':
 				$object = $model_factory->getObject($_REQUEST['dict']);
 				$object->addFilter( new ProjectRoleInheritedFilter() );

 				return $object;

 			default:
 				if ( $_REQUEST['dict'] == '' )
 				{
 					return $model_factory->getObject('pm_TaskType');
 				}
 				else
 				{
 					return getFactory()->getObject($_REQUEST['dict']);
 				}
 		}
 	}
 	
 	function getTable() 
 	{
 	    if ( $_REQUEST['dict'] == '' )
 	    {
            return new DictionaryTable(getFactory()->getObject('Dictionary'));
 	    }
 	    
 		$object = $this->getDictionary();
 		
		if ( is_object($object) )
		{
			switch ( $object->getClassName() )
			{
				case 'pm_State':
				    return new StateTable( $object );
					    	
				default:
					if ( is_a($object, 'ObjectTemplate') )
					{
						return new ObjectTemplateTable( $object );
					}
					
					return new DictionaryItemsTable( $object );
			}
		}
		
		return null;			
 	}
 	
 	function getForm()
 	{
 		global $model_factory, $_REQUEST;

		$object = $this->getDictionary();
		
		if ( !is_object($object) ) return null;
		
		switch ( $object->getClassName() )
		{
			case 'pm_ProjectRole':
				return new ProjectRoleForm ( $object );
				
			case 'pm_TaskType':
				return new TaskTypeForm ( $object );

			case 'pm_Transition':
				return new TransitionForm( getFactory()->getObject('Transition') );

			case 'pm_State':
				return new StateForm ( $object );
				
			case 'WikiPageType':
				return new RequirementTypeForm ( $object );

			case 'pm_TestExecutionResult':
				return new TestExecutionResultForm ( $object );
				
		    case 'pm_CustomAttribute':
		    	
				if ( $_REQUEST['class'] == 'metaobject' )
				{
					if ( $_REQUEST['pm_CustomAttributeId'] == '' && ($_REQUEST['EntityReferenceName'] == '' || $_REQUEST['AttributeType'] == '') )
					{
						return new CustomAttributeEntityForm( $object );
					}
					else
					{
						return new CustomAttributeFinalForm( $object );
					}
				}
				else
				{
					return new CustomAttributeFinalForm( $object );	
				}

			default:
				return new DictionaryItemForm( $object );
		}
 	}
 	
 	function render( $view = null )
 	{
 		if ( $_REQUEST['wait'] != '' && $this->getDictionary() instanceof CustomResource )
 		{
 			die();
 		}
 		
 		return parent::render($view);
 	}
 	
 	function buildFormSections( $object_it )
 	{
		$this->addInfoSection(
				new PageSectionAttributes(
						$this->getFormRef()->getObject(), 'additional', translate('Дополнительно')
				)
		);

 		if ( !is_object($object_it) ) return;

 		if ( $object_it->object->getAttributeType('RecentComment') != '' ) {
 			$this->addInfoSection( new PageSectionComments($object_it) );
 		}
 	}
}