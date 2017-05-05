<?php

include_once SERVER_ROOT_PATH."pm/classes/product/validation/ModelValidatorAvoidInfiniteLoop.php";
include_once SERVER_ROOT_PATH."pm/classes/product/validation/ModelValidatorChildrenLevels.php";
include_once SERVER_ROOT_PATH."pm/views/product/FieldFunctionTrace.php";
include_once SERVER_ROOT_PATH."pm/views/ui/FieldHierarchySelector.php";
include "FieldFeatureTagTrace.php";
include "FieldFeatureIssues.php";

class FunctionForm extends PMPageForm
{
	private $create_subfunc_actions = array();
	private $create_issue_actions = array();
	private $goto_issues_template = '';
	private $request_it = null;
	
	function __construct( $object )
	{
		parent::__construct( $object );
		$this->buildMethods();
		
		$this->request_it = $_REQUEST['Request'] > 0 
			? getFactory()->getObject('Request')->getExact($_REQUEST['Request'])
			: getFactory()->getObject('Request')->getEmptyIterator();
	}
	
	function buildModelValidator()
	{
		$validator = parent::buildModelValidator();
		$validator->addValidator( new ModelValidatorAvoidInfiniteLoop() );
		$validator->addValidator( new ModelValidatorChildrenLevels() );
		return $validator;
	}
	
	function buildMethods()
 	{
 		if ( getFactory()->getAccessPolicy()->can_create($this->getObject()) )
 		{
 			$type_it = getFactory()->getObject('FeatureType')->getAll();
		    while( !$type_it->end() )
		    {
		        $method = new ObjectCreateNewWebMethod($this->getObject());
		        $this->create_subfunc_actions[$type_it->get('OrderNum')] = array(
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

 		$request = getFactory()->getObject('Request');
 	 	if ( getFactory()->getAccessPolicy()->can_create($request) )
 		{
 			$type_it = getFactory()->getObject('RequestType')->getRegistry()->Query(
 					array ( new FilterBaseVpdPredicate() )
 			);
		    while( !$type_it->end() )
		    {
		        $method = new ObjectCreateNewWebMethod($request);
		        $method->setRedirectUrl('donothing');
		        
		        $this->create_issue_actions[] = array(
			            'name' => $type_it->getDisplayName(),
			        	'method' => $method,
			        	'type' => $type_it->getId()
		        );
		        
		        $type_it->moveNext();
		    }
		    
	        $method = new ObjectCreateNewWebMethod($request);
	        $method->setRedirectUrl('donothing');
	        
	        $this->create_issue_actions[] = array(
		            'name' => $request->getDisplayName(),
		        	'method' => $method
	        );
 		}

 		$report_it = getFactory()->getObject('PMReport')->getExact('allissues');
 		if ( $report_it->getId() != '' )
 		{
 			$this->goto_issues_template = $report_it->getUrl().'&state=all&function=';
 		}
 	}

 	function getActions()
 	{
 		$object_it = $this->getObjectIt();
 		
 		$actions = parent::getActions();
 		
 		if ( !is_object($object_it) ) return $actions;
 		
 		$new_actions = array();
 		
 		$able_create_issues = $object_it->get('Type') == ''
 				|| $this->getObject()->getAttributeType('Type') != '' && $object_it->getRef('Type')->get('HasIssues') == 'Y';
 		
 		if ( $able_create_issues )
 		{
	 	 	foreach( $this->create_issue_actions as $key => $data )
	 		{
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
 			if ( count($levels) > 0 && !in_array($data['system-name'], $levels) ) continue;
 			
 			$method = $data['method'];
	        $subfunc_actions[] = array(
	            'name' => $data['name'],
	            'url' => $method->getJSCall( array(
			            		'ParentFeature' => $object_it->getId(),
			            		'Type' => $data['type']
	            		))
	        );
 		}

 	 	if ( count($subfunc_actions) > 0 )
 		{
 			if ( $new_actions[array_pop(array_keys($new_actions))]['name'] != '' ) $new_actions[] = array();
 			$new_actions = array_merge($new_actions, $subfunc_actions);
 		}
 		
 		if ( count($new_actions) > 0 )
 		{
 			if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
 			$actions[] = array ( 
				'name' => translate('Создать'),
	            'items' => $new_actions
			);
 		}
 		
 		if ( $this->goto_issues_template != '' )
 		{
	 		if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
			$actions[] = array(
			    'url' => $this->goto_issues_template.$object_it->getId(), 
			    'name' => translate('Перейти к пожеланиям')
			);
 		}
 		
 		return $actions;
 	}
 	
 	function IsAttributeVisible( $attr_name ) 
 	{
 		switch ( $attr_name )
 		{
 		    case 'OrderNum':
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
				if ( $this->request_it->getId() > 0 ) {
					return $this->request_it->getHtmlDecoded($attribute);
				}
				return parent::getFieldValue( $attribute );
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
		global $model_factory;
		
		switch ( $name ) 
		{
			case 'Requirement':
				return new FieldFunctionTrace( $this->object_it, 
					$model_factory->getObject('FunctionTraceRequirement') );

			case 'Tags':
			    return new FieldFeatureTagTrace( 
			        is_object($this->object_it) ? $this->object_it : null 
			    );

			case 'Request':
				return new FieldFeatureIssues(is_object($this->object_it) ? $this->object_it : null);

			case 'ParentFeature':
				return new FieldHierarchySelector($this->getObject());

            case 'Caption':
                if ( !$this->getEditMode() ) {
                    $field = new FieldWYSIWYG();
                    $field->setObjectIt( $this->getObjectIt() );
                    $field->getEditor()->setMode( WIKI_MODE_INPLACE_INPUT );
                }
                else {
                    $field = parent::createFieldObject($name);
                }
                return $field;

			default:
				return parent::createFieldObject( $name );
		}
	}

	function IsAttributeEditable( $attr )
	{
		switch ( $attr )
		{
			case 'Description':
				return $this->getEditMode();
		}
		return parent::IsAttributeEditable($attr);
	}

	function process()
	{
		if ( $this->getAction() != 'add' ) return parent::process();

		if ( $this->request_it->getId() > 0 ) {
			$this->request_it->object->delete($this->request_it->getId());
		}
		
		return parent::process();
	}

    function getShortAttributes() {
        return array_merge(
            parent::getShortAttributes(),
            array('Importance', 'Tags')
        );
    }
}