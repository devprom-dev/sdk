<?php
use Devprom\ProjectBundle\Service\Workflow\WorkflowService;

include "DictionaryItemForm.php";
include "ProjectRoleForm.php";
include "TaskTypeForm.php";
include "RecurringForm.php";
include "CustomAttributeEntityForm.php";
include "CustomAttributeFinalForm.php";
include "DictionaryItemsTable.php";
include "DictionaryTable.php";
include "TestExecutionResultForm.php";
include "TextTemplateForm.php";
include "TextTemplateTable.php";
include "DictionaryPageSettingBuilder.php";
include "FormFieldForm.php";
include "FormFieldTable.php";

include SERVER_ROOT_PATH."pm/views/workflow/TransitionForm.php";
include SERVER_ROOT_PATH."pm/views/workflow/StateForm.php";
include SERVER_ROOT_PATH."pm/views/workflow/StateTable.php";
include SERVER_ROOT_PATH."pm/views/templates/ObjectTemplateTable.php";
include SERVER_ROOT_PATH."pm/views/templates/RequestTemplateForm.php";
include SERVER_ROOT_PATH."pm/views/templates/TaskTemplateForm.php";
include SERVER_ROOT_PATH."pm/views/wiki/RequirementTypeForm.php";
include SERVER_ROOT_PATH."pm/classes/settings/DictionaryItemModelBuilder.php";

class DictionaryPage extends PMPage
{
    private $dictObject = null;

 	function __construct()
 	{
        getSession()->addBuilder( new \RequestModelExtendedBuilder() );
        getSession()->addBuilder( new \DictionaryPageSettingBuilder() );

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

        if ( is_object($this->dictObject) ) return $this->dictObject;
 		return $this->dictObject = $this->getDictionary();
 	}
 	
 	function getDictionary()
 	{
		getSession()->addBuilder( new StateBaseModelBuilder() );

 		switch ( $_REQUEST['entity'] )
 		{
 			case 'pm_ProjectRole':
 				return getFactory()->getObject('ProjectRoleInherited');

 			default:
 				if ( $_REQUEST['entity'] == '' ) {
 				    if ( $_REQUEST['dict'] == '' ) {
                        return getFactory()->getObject('pm_TaskType');
                    }
 				    else {
                        return getFactory()->getObject($_REQUEST['dict']);
                    }
 				}
 				else {
 					return getFactory()->getObject($_REQUEST['entity']);
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

                case 'pm_StateAttribute':
                    return new FormFieldTable( $object );

				default:
					if ( is_a($object, 'ObjectTemplate') ) {
						return new ObjectTemplateTable( $object );
					}
					return new DictionaryItemsTable( $object );
			}
		}
		
		return null;			
 	}
 	
 	function getEntityForm()
 	{
 	    if ( $_REQUEST['dict'] == 'RequestTemplate' ) {
            return new RequestTemplateForm(getFactory()->getObject('RequestTemplate'));
        }

        if ( $_REQUEST['dict'] == 'TaskTemplate' ) {
            return new TaskTemplateForm(getFactory()->getObject('TaskTemplate'));
        }

		$object = $this->getObject();

		if ( !is_object($object) ) return null;
		
		switch ( $object->getClassName() )
		{
			case 'pm_ProjectRole':
				return new ProjectRoleForm ( $object );
				
			case 'pm_TaskType':
				return new TaskTypeForm ( $object );

            case 'pm_Recurring':
                return new RecurringForm( $object );

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

            case 'pm_StateAttribute':
                return new FormFieldForm($object);
				
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
 		if ( $_REQUEST['wait'] != '' && $this->getObject() instanceof CustomResource ) {
 			die();
 		}
 		
 		return parent::render($view);
 	}
 	
 	function buildFormSections( $object_it )
 	{
 		if ( !is_object($object_it) ) return;
 		if ( $_REQUEST['dict'] == 'RequestTemplate' ) return;
        if ( $_REQUEST['dict'] == 'TaskTemplate' ) return;

 		if ( $object_it->object->getAttributeType('RecentComment') != '' ) {
 			$this->addInfoSection( new PageSectionComments($object_it, $this->getCommentObject()) );
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

    function getUrl()
    {
        $session = getSession();
        return $session->getApplicationUrl().'project/workflow?dict='.SanitizeUrl::parseUrl($_REQUEST['dict']);
    }
}