<?php
include_once SERVER_ROOT_PATH."pm/views/product/FieldFunctionTrace.php";
include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";
include "FieldFeatureTagTrace.php";
include "FieldFeatureIssues.php";

class FunctionForm extends PMPageForm
{
	private $create_subfunc_actions = array();
	private $create_issue_actions = array();
	private $goto_issues_template = '';
	private $request_it = null;
	private $assignRequestIt = null;
	private $hasFeatureLevelRules = false;
	private $bind_method = null;

	function __construct( $object )
	{
		parent::__construct( $object );
		$this->buildMethods();
		
		$this->request_it = $_REQUEST['Request'] > 0 
			? getFactory()->getObject('Request')->getExact($_REQUEST['Request'])
			: getFactory()->getObject('Request')->getEmptyIterator();

        $this->assignRequestIt = $_REQUEST['IssueAssociated'] > 0
            ? getFactory()->getObject('Request')->getExact($_REQUEST['IssueAssociated'])
            : getFactory()->getObject('Request')->getEmptyIterator();
	}
	
	function extendModel()
    {
        if ( $this->getEditMode() ) {
            $this->getObject()->setAttributeVisible('OrderNum', true);
        }
        if ( $this->request_it->getId() > 0 ) {
            $this->getObject()->resetAttributeGroup('Request', 'trace');
        }

        parent::extendModel();

        if ( is_object($this->getObjectIt()) ) {
            $this->getObject()->setAttributeVisible('Children', true);
        }
    }

    function buildMethods()
 	{
        $this->hasFeatureLevelRules = join('',getFactory()->getObject('FeatureType')->getRegistry()->Query(
                array(
                    new FilterBaseVpdPredicate()
                )
            )->fieldToArray('ChildrenLevels')) != '';

 		if ( getFactory()->getAccessPolicy()->can_create($this->getObject()) )
 		{
 			$type_it = getFactory()->getObject('FeatureType')->getRegistry()->Query(
 			    array(
 			        new FilterBaseVpdPredicate()
                )
            );
		    while( !$type_it->end() ) {
		        $method = new ObjectCreateNewWebMethod($this->getObject());
		        $this->create_subfunc_actions[] = array(
			            'name' => $type_it->getDisplayName(),
			        	'method' => $method,
			        	'type' => $type_it->getId(),
		        		'system-name' => $type_it->get('ReferenceName')
		        );
		        $type_it->moveNext();
		    }
		    if ( $type_it->count() < 1 ) {
                $method = new ObjectCreateNewWebMethod($this->getObject());
                $this->create_subfunc_actions[] = array(
                    'name' => text(2272),
                    'method' => $method
                );
            }
 		}

        if ( getSession()->IsRDD() )
        {
            $request = getFactory()->getObject('Issue');
            if ( getFactory()->getAccessPolicy()->can_create($request) )
            {
                $method = new ObjectCreateNewWebMethod($request);
                $this->create_issue_actions[] = array(
                    'name' => $method->getCaption(),
                    'method' => $method
                );
            }
        }
        else {
            $request = getFactory()->getObject('Request');
            if ( getFactory()->getAccessPolicy()->can_create($request) )
            {
                $type_it = getFactory()->getObject('RequestType')->getRegistry()->Query(
                    array ( new FilterBaseVpdPredicate() )
                );
                while( !$type_it->end() )
                {
                    $method = new ObjectCreateNewWebMethod($request);
                    $this->create_issue_actions[] = array(
                        'name' => $type_it->getDisplayName(),
                        'method' => $method,
                        'type' => $type_it->getId()
                    );

                    $type_it->moveNext();
                }
            }
        }

        $it = getFactory()->getObject('ObjectsListWidget')
            ->getByRef('Caption',
                get_class($this->getObject()->getAttributeObject('Request'))
            );
        if ( $it->getId() != '' ) {
            $widget_it = $it->getWidgetIt();
 			$this->goto_issues_template = $widget_it->getUrl('state=all&function=%ids%');
 		}

        $method = new ObjectModifyWebMethod($this->getObject()->getEmptyIterator());
        if ( $method->hasAccess() ) {
            $this->bind_method = array(
                'name' => text(2692),
                'url' => $method->getJSCall(
                    array(
                        'object_id' => '%ids%&BindIssue=true',
                        'can_delete' => 'false'
                    )
                )
            );
        }
 	}

 	function getAbleCreateIssue( $objectIt ) {
	    return $objectIt->get('Type') == ''
            || $objectIt->object->IsReference('Type') && $objectIt->getRef('Type')->get('HasIssues') == 'Y';
    }

 	function getNewRelatedActions()
    {
        $object_it = $this->getObjectIt();

        $new_actions = array();

        if ( $this->getAbleCreateIssue($object_it) ) {
            foreach( $this->create_issue_actions as $key => $data ) {
                $method = $data['method'];
                $new_actions[] = array(
                    'name' => $data['name'],
                    'url' => $method->getJSCall( array(
                        'Function' => $object_it->getId(),
                        'Type' => $data['type']
                    )),
                    'view' => 'button',
                    'button-class' => 'btn-success new-at-form',
                    'icon' => 'icon-plus'
                );
            }
        }

        $levels = array_filter(preg_split('/\s*,\s*/', $object_it->get('ChildrenLevels')), function($value) {
            return $value != '';
        });
        $subfunc_actions = array();
        foreach( $this->create_subfunc_actions as $key => $data )
        {
            if ( $this->hasFeatureLevelRules && !in_array($data['system-name'], $levels) ) continue;

            $method = $data['method'];
            $subfunc_actions[] = array(
                'name' => $data['name'],
                'url' => $method->getJSCall( array(
                    'ParentFeature' => $object_it->getId(),
                    'Type' => $data['type']
                ))
            );
        }

        if ( count($subfunc_actions) > 0 ) {
            if ( $new_actions[array_pop(array_keys($new_actions))]['name'] != '' ) $new_actions[] = array();
            $new_actions = array_merge($new_actions, $subfunc_actions);
        }

        return $new_actions;
    }

 	function getActions()
 	{
 		$object_it = $this->getObjectIt();
 		
 		$actions = parent::getActions();

 		if ( !is_object($object_it) ) return $actions;
 		
 		if ( $this->getAbleCreateIssue($object_it) && is_array($this->bind_method) ) {
 		    $action = $this->bind_method;
            $action['url'] = str_replace('%ids%', $object_it->getId(), $action['url']);
 		    $actions[] = $action;
        }
 		
 		if ( $this->goto_issues_template != '' ) {
	 		if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
			$actions[] = array(
			    'url' => str_replace('%ids%', $object_it->getId(),$this->goto_issues_template),
			    'name' => $this->getObject()->getAttributeUserName('Request')
			);
 		}
 		
 		return $actions;
 	}
 	
 	function IsAttributeVisible( $attr_name ) 
 	{
 		switch ( $attr_name )
 		{
 		    case 'FinishDate':
 		    case 'ResponsibleAnalyst':
 		    case 'ResponsibleDesigner':
 		    case 'ResponsibleDeveloper':
 		    case 'ResponsibleTester':
 		    case 'ResponsibleDocumenter':
 		        return false;
 		        
 		    case 'Tags':
 		        return true;
 		        
 		    default:
 		        return parent::IsAttributeVisible( $attr_name );
 		}
	}

	function getFieldValue( $attribute )
	{
		switch( $attribute )
		{
			case 'Caption':
            case 'Description':
				if ( $this->request_it->getId() > 0 ) {
					return $this->request_it->getHtmlDecoded($attribute);
				}
				return parent::getFieldValue( $attribute );
			default:
				return parent::getFieldValue( $attribute );
		}
	}
	
	function createFieldObject( $name )
	{
		switch ( $name )
		{
			case 'Requirement':
				return new FieldFunctionTrace( $this->object_it, 
					getFactory()->getObject('FunctionTraceRequirement') );

            case 'TestScenario':
                return new FieldFunctionTrace( $this->object_it,
                    getFactory()->getObject('FunctionTraceTestScenario') );

            case 'HelpPage':
                return new FieldFunctionTrace( $this->object_it,
                    getFactory()->getObject('FunctionTraceHelpPage') );

			case 'Tags':
			    return new FieldFeatureTagTrace( 
			        is_object($this->object_it) ? $this->object_it : null 
			    );

			case 'Request':
				$field = new FieldFeatureIssues(
				    is_object($this->object_it) ? $this->object_it : null,
                    $this->getObject()->getAttributeObject('Request')
                );
                $field->setIssueIt($this->assignRequestIt);
                return $field;

			case 'ParentFeature':
				return new FieldHierarchySelector($this->getObject());

            case 'Children':
            case 'Increment':
                if ( is_object($this->getObjectIt()) ) {
                    return new FieldListOfReferences($this->getObjectIt()->getRef($name));
                }
                return null;

            case 'Attachment':
                return new FieldAttachments( is_object($this->getObjectIt()) ? $this->getObjectIt() : $this->object );

			default:
				return parent::createFieldObject( $name );
		}
	}

	function persist()
	{
        $result = parent::persist();

		if ( $this->getAction() == 'add' ) {
            if ( $result && $this->request_it->getId() > 0 ) {
                DAL::Instance()->Query(
                    "UPDATE pm_Task SET ChangeRequest = NULL WHERE ChangeRequest = {$this->request_it->getId()}"
                );
                $this->request_it->object->delete($this->request_it->getId());
            }
            if ( $result && $this->assignRequestIt->getId() > 0 ) {
                $this->assignRequestIt->object->getRegistry()->Store(
                    $this->assignRequestIt, array(
                        'Function' => $this->getObjectIt()->getId()
                    )
                );
            }
        }

		return $result;
	}

    function getShortAttributes() {
        return array_merge(
            parent::getShortAttributes(),
            array('Importance', 'Tags')
        );
    }
}