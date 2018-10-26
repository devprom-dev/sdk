<?php
use Devprom\ProjectBundle\Service\Workflow\WorkflowService;

include "DictionaryItemForm.php";
include "ProjectRoleForm.php";
include "TaskTypeForm.php";
include "CustomAttributeEntityForm.php";
include "CustomAttributeFinalForm.php";
include "DictionaryItemsTable.php";
include "DictionaryTable.php";
include "TestExecutionResultForm.php";
include "TextTemplateForm.php";
include "TextTemplateTable.php";

include SERVER_ROOT_PATH."pm/views/workflow/TransitionForm.php";
include SERVER_ROOT_PATH."pm/views/workflow/StateForm.php";
include SERVER_ROOT_PATH."pm/views/workflow/StateTable.php";
include SERVER_ROOT_PATH."pm/views/templates/ObjectTemplateTable.php";
include SERVER_ROOT_PATH."pm/views/wiki/RequirementTypeForm.php";
include SERVER_ROOT_PATH."pm/classes/settings/DictionaryItemModelBuilder.php";

class DictionaryPage extends PMPage
{
    private $object = null;

 	function __construct()
 	{
 		if ( $_REQUEST['entity'] != '' ) {
			$_REQUEST['dict'] = $_REQUEST['entity'];
 		}
 		
 		parent::__construct();
 		
 		if ( $this->needDisplayForm() ) {
            $this->buildFormSections( $this->getObjectIt() );
        }
        else {
            $this->buildTableSections();
        }
 	}
 	
 	function getObject()
 	{
 	    getSession()->addBuilder( new DictionaryItemModelBuilder() );

        if ( is_object($this->object) ) return $this->object;
 		return $this->object = $this->getDictionary();
 	}
 	
 	function getDictionary()
 	{
		getSession()->addBuilder( new StateBaseModelBuilder() );
 		
 		switch ( $_REQUEST['dict'] )
 		{
 			case 'pm_ProjectRole':
 				return getFactory()->getObject('ProjectRoleInherited');

 			default:
 				if ( $_REQUEST['dict'] == '' ) {
 					return getFactory()->getObject('pm_TaskType');
 				}
 				else {
 					return getFactory()->getObject($_REQUEST['dict']);
 				}
 		}
 	}
 	
 	function getTable() 
 	{
 	    if ( $_REQUEST['dict'] == '' ) {
            return new DictionaryTable(getFactory()->getObject('Dictionary'));
 	    }
 	    
 		$object = $this->getObject();
		if ( is_object($object) )
		{
			switch ( $object->getClassName() )
			{
				case 'pm_State':
				    return new StateTable( $object );

                case 'pm_TextTemplate':
                    return new TextTemplateTable( $object );

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
		$object = $this->getObject();
		
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

            case 'pm_TextTemplate':
                return new TextTemplateForm($object);
				
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
 		if ( $_REQUEST['wait'] != '' && $this->getObject() instanceof CustomResource )
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

 	function buildTableSections()
    {
    }

    function getHint()
    {
        $hint = parent::getHint();

        $object = $this->getObject();
        if ( $object instanceof StateBase ) {
            $hint = '<div class="workflow-image-holder">'.WorkflowService::getImage($object).'</div><hr/>'.$hint;
        }

        return $hint;
    }
}