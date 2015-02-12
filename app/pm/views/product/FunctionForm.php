<?php

include_once SERVER_ROOT_PATH."pm/classes/product/validation/ModelValidatorAvoidInfiniteLoop.php";
include_once SERVER_ROOT_PATH."pm/classes/product/validation/ModelValidatorChildrenLevels.php";
include_once SERVER_ROOT_PATH."pm/views/product/FieldFunctionTrace.php";
include_once SERVER_ROOT_PATH."pm/views/ui/FieldHierarchySelector.php";
include "FieldFeatureTagTrace.php";

class FunctionForm extends PMPageForm
{
	private $create_subfunc_actions = array();
	private $create_issue_actions = array();
	private $goto_issues_template = '';
	
	function __construct( $object )
	{
		parent::__construct( $object );
		
		$this->buildMethods();
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
		        $method->setRedirectUrl('donothing');
		        
		        $this->create_subfunc_actions[$type_it->get('OrderNum')] = array(
			            'name' => $type_it->getDisplayName(),
			        	'method' => $method,
			        	'type' => $type_it->getId(),
		        		'system-name' => $type_it->get('ReferenceName')
		        );
		        
		        $type_it->moveNext();
		    }
 		}

 		$request = getFactory()->getObject('Request');
 		
 	 	if ( getFactory()->getAccessPolicy()->can_create($request) )
 		{
 			$type_it = getFactory()->getObject('RequestType')->getAll();
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
 				|| $object_it->getRef('Type')->get('HasIssues') == 'Y';
 		
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
		            		))
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

	function draw()
	{
		global $_REQUEST;
		
		parent::draw();

		$this->object_it = $this->getObjectIt();
		
		if ( isset($this->object_it) && $_REQUEST['formonly'] != 'true')
		{
			echo '<div class="line">';
			echo '</div>';
			echo '<div class="line">';
			echo '</div>';

			echo '<div class="line">';
				echo translate('Комментарии участников');
			echo '</div>';

			echo '<div class="line">';
				$comment_form = new CommentList( $this->object_it );
				$comment_form->draw();
			echo '</div>';
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

			case 'Description':
				$field = parent::createFieldObject( $name );
				
				$field->setRows( 8 );
				
				return $field;
		
			case 'Tags':
			    return new FieldFeatureTagTrace( 
			        is_object($this->object_it) ? $this->object_it : null 
			    );
			    
			case 'ParentFeature':
				return new FieldHierarchySelector($this->getObject());
			    
			default:
				return parent::createFieldObject( $name );
		}
	}
}