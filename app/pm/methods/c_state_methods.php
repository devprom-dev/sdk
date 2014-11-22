<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";
include_once SERVER_ROOT_PATH.'pm/classes/workflow/WorkflowModelBuilder.php';
include_once SERVER_ROOT_PATH.'pm/classes/workflow/WorkflowTransitionAttributesModelBuilder.php';

class TransitionStateMethod extends WebMethod
{
 	var $transition_it, $object_it;
 	
 	protected $reason_has_no_access;
 	
 	private $target_ref_name = '';
 	
 	private $source_ref_name = '';
 	
 	function TransitionStateMethod ( $transition_it = null, $object_it = null )
 	{
 		parent::WebMethod();
	
		if ( is_object($transition_it) )
		{
	 		$this->transition_it = $transition_it;  
	 		$this->object_it = $object_it;
		}
		
		if ( is_object($object_it) )
		{
			$this->setSourceStateRefName( $object_it->get('State') );
		}
		
		$this->setRedirectUrl( 'function() { window.location.reload(); }' );
 	}

   	function setSourceStateRefName( $ref_name )
 	{
 		$this->source_ref_name = $ref_name;
 	}
 	
 	function setTargetStateRefName( $ref_name )
 	{
 		$this->target_ref_name = $ref_name;
 	}
 		
 	function setObjectIt( $object_it )
	{
 		$this->object_it = $object_it;
	}
	
	function setTransitionIt( $transition_it )
	{
 		$this->transition_it = $transition_it;  
	}
	
 	function getCaption()
 	{
 		return $this->transition_it->getDisplayName();
 	}
 	
 	function getDescription()
 	{
 		return $this->transition_it->get('Description');
 	}
 	
 	function getWarning() 
 	{
 		$state_it = $this->transition_it->getRef( 'TargetState', 
 			getFactory()->getObject($this->object_it->object->getStateClassName()) );
 			
 		return $state_it->getWarningMessage( $this->object_it );
 	}

 	function hasAccess()
 	{
 		if ( !$this->object_it->IsTransitable() )
 		{
 			return false;
 		}
 		
 		if ( !$this->transition_it->doable( $this->object_it ))
 		{
 			$this->reason_has_no_access = $this->transition_it->getNonDoableReason();
 			 
 			return false;
 		}
 		
 		return getFactory()->getAccessPolicy()->can_modify($this->object_it) && $this->transition_it->appliable();
 	}
 	
 	function getReasonHasNoAccess()
 	{
 		return $this->reason_has_no_access;
 	}
 	
	function getUrl( $parms_array = array() )
	{
		return parent::getUrl(
			array ( 'object' => $this->object_it->getId(),
				    'class' => get_class($this->object_it->object),
				    'transition' => $this->transition_it->getId() )
		);
	}

	function getJSCall( $parms_array = array() )
	{
		$parms = array (
				ObjectUid::getProject($this->object_it),
				$this->object_it->getId(),
				get_class($this->object_it->object),
				$this->object_it->object->getEntityRefName(),
				$this->source_ref_name != ''
						? $this->source_ref_name : $this->transition_it->getRef('SourceState')->get('ReferenceName'),
				$this->target_ref_name != '' 
						? $this->target_ref_name : $this->transition_it->getRef('TargetState')->get('ReferenceName'),
				$this->transition_it->getId(), 
				$this->transition_it->getDisplayName()
		);
		
		return "javascript: workflowMoveObject('".join("','", $parms)."', ".$this->getRedirectUrl().")";
	}
	
 	function execute_request()
 	{
		$object = getFactory()->getObject($_REQUEST['class']);
		
		$object_it = $object->getExact($_REQUEST['object']);

		$this->setObjectIt( $object_it );
 		
		$transition = getFactory()->getObject('pm_Transition');
		
		$transition->setVpdContext( $object_it );

		$transition_it = $transition->getExact($_REQUEST['transition']);
 		
		$this->setTransitionIt( $transition_it ); 
	
		if ( $this->getRedirectUrl() != '' )
		{
			echo '&Transition='.SanitizeUrl::parseUrl($_REQUEST['transition']);
		}
		else
		{
			$this->execute( $_REQUEST['transition'], $_REQUEST['object'], $_REQUEST['class'] );
		}
 	}

 	function execute( $transition_id, $object_id, $class, $required = array() )
 	{
 		getSession()->addBuilder( new WorkflowModelBuilder() );
 		
 		$object = getFactory()->getObject($class);
		
		$object_it = $object->getExact( $object_id ); 		

		if ( !getFactory()->getAccessPolicy()->can_modify($object_it) ) return;

		$transition = getFactory()->getObject('Transition');
		
		$transition->setVpdContext( $object_it );
		
		$transition_it = $transition->getExact($transition_id);
		
		if ( $object->getAttributeType('State') == '' ) return;
		
		$state_it = $transition_it->getRef('TargetState');
		
		$required['State'] = $state_it->get('ReferenceName');
		
		$required['Transition'] = $transition_it->getId();
		
		foreach( $required as $key => $value )
		{
			if ( $required[$key] == '' ) unset($required[$key]);
		}

		$object_it->object->modify_parms($object_it->getId(), $required);

	    getFactory()->getEventsManager()->
	    		executeEventsAfterBusinessTransaction(
	    				$object_it->object->getExact($object_it->getId()),
	    				'WorklfowMovementEventHandler'
 				);
 	}
 }
 
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class ModifyStateWebMethod extends TransitionStateMethod
 {
	function getUrl( $parms_array = array() )
	{
		$source_it = $this->transition_it->getRef('SourceState');
		$target_it = $this->transition_it->getRef('TargetState');
		
		return WebMethod::getUrl(
			array ( 'object' => $this->object_it->getId(),
				    'class' => get_class($this->object_it->object),
				    'source' => $source_it->get('ReferenceName'),
					'target' => $target_it->get('ReferenceName') )
		);
	}
 	
	function execute_request()
 	{
		$this->execute( $_REQUEST );
 	}

 	function execute( $parms )
 	{
 		getSession()->addBuilder( new WorkflowModelBuilder() );
 		
 		$class_name = getFactory()->getClass($parms['class']);
 		
 		if ( !class_exists($class_name) ) throw new Exception('Unknown class name: '.$parms['class']);
 		
		$object = getFactory()->getObject($class_name);
		
		if ( $parms['object'] > 0 )
		{
			$object_it = $object->getExact( $parms['object'] ); 		
		}
		else
		{
			$index = $object->getRecordCount() + 1;
			
			$parms['Caption'] = $object->getDisplayName().' '.$index;
			
			$object_it = $object->getExact( 
				$object->add_parms( $parms ) ); 		

			echo '{"message":"ok","object":"'.$object_it->getId().'"}';
			return;
		}
		
		if ( !getFactory()->getAccessPolicy()->can_modify($object_it) )
		{
			$result = array (
				"message" => "denied",
				"description" => IteratorBase::wintoutf8(text(707))				
			);
		
			echo JsonWrapper::encode($result);
			
			return;
		}

		$state_object = getFactory()->getObject($object->getStateClassName());
		
	    $source_it = $state_object->getRegistry()->Query(
    			array (
    					new \FilterAttributePredicate('ReferenceName', $object_it->get('State')),
    					new \FilterVpdPredicate($object_it->get('VPD')),
    					new \SortOrderedClause()
    			)
    	);
		
		if ( $source_it->getId() < 1 ) 
		{
		    $source_it = $state_object->getRegistry()->Query(
	    			array (
	    					new \FilterVpdPredicate($object_it->get('VPD')),
	    					new \SortOrderedClause()
	    			)
	    	);
		}

		$target_it = $state_object->getRegistry()->Query(
				array (
						new FilterAttributePredicate('ReferenceName', preg_split('/,/', trim($parms['target']))),
						new FilterBaseVpdPredicate(),
						new SortOrderedClause()
				)
		);
		
		if ( $target_it->count() < 1 )
		{
			$result = array (
				"message" => "denied",
				"description" =>
						str_replace('%1', getSession()->getApplicationUrl().'project/workflow/'.$object->getStateClassName(), 
								IteratorBase::wintoutf8(text(1860)))				
			);
			
			echo JsonWrapper::encode($result);
			
			return;
		}

		$transition_it = getFactory()->getObject('Transition')->getRegistry()->Query(
				$parms['transition'] > 0
				? array (
						new FilterInPredicate($parms['transition'])
				  )
				: array (
						new FilterAttributePredicate('SourceState', $source_it->getId()),
						new FilterAttributePredicate('TargetState', $target_it->getId()),
						new TransitionStateClassPredicate($object->getStatableClassName()),
						new FilterBaseVpdPredicate(),
						new SortOrderedClause()
				  )
		);

		$this->setObjectIt( $object_it );
		
		$reason = '';
		
		while( !$transition_it->end() )
		{
			$this->setTransitionIt( $transition_it );
			
			if ( !$this->hasAccess() )
			{
				$reason = $this->getReasonHasNoAccess();
				
				$transition_it->moveNext();
				
				continue;
			}

			// extend model to get visible|required attributes
			$model_builder = new WorkflowTransitionAttributesModelBuilder( $transition_it );
			
			$model_builder->build( $object );
			
			$attributes = array();
			
			foreach( $object->getAttributes() as $attribute => $data )
			{
				if ( !$object->IsAttributeVisible($attribute) ) continue;
				
				$attributes[] = $attribute;
			}
			        	
			if ( $object instanceof Request && in_array('Tasks', $attributes, true) )
			{
	   	 	 		$url = getSession()->getApplicationUrl($object_it).
    	 	 			'issues/board?mode=group&ChangeRequest='.$object_it->getId().'&formonly=true&Transition='.$transition_it->getId();
    	 	 		
    				echo '{"message":"redirect","url":"'.$url.'"}';
    				
    		 	 	return;
			}
			
			if ( count($attributes) > 0 )
			{
	 	 		$url = $object_it->getEditUrl().'&Transition='.$transition_it->getId().'&formonly=true';
	 	 		
				echo '{"message":"redirect","url":"'.$url.'"}';
				
				return;
			}
			else
			{
				$method = new TransitionStateMethod( $transition_it, $object_it );

				unset($parms['class']);
				unset($parms['object']);
				unset($parms['target']);
				unset($parms['source']);
				
				$method->execute( 
					$transition_it->getId(), $object_it->getId(), get_class($object_it->object), $parms
				);
				
				echo '{"message":"ok"}';
				
				return;
			}
			
			$transition_it->moveNext();
		}
		
		$result = array (
			"message" => "denied",
			"description" => $reason != '' ? $reason :
					str_replace('%1', getSession()->getApplicationUrl().'project/workflow/'.$object->getStateClassName(), 
							IteratorBase::wintoutf8(text(1012)))				
		);
		
		echo JsonWrapper::encode($result);
 	}
 }
  
 ///////////////////////////////////////////////////////////////////////////////////////
 class FilterStateMethod extends FilterWebMethod
 {
 	var $state_it, $object;
 	
 	private $default;
 	
 	function FilterStateMethod( $object = null )
 	{
 		if ( is_object($object) )
 		{
	 		$this->object = $object;

	 		$this->state_it = $this->object->getAll();

 			parent::FilterWebMethod( $object->getClassName() );
 		}
 		else
 		{
 			parent::FilterWebMethod( '' );
 		}
 	}

 	function getCaption()
 	{
 		return translate('Состояние');
 	}
 	
 	function getValues()
 	{
  		$values = array (
 			'all' => translate('Любое'),
 			);
 		
 		while ( !$this->state_it->end() )
 		{
 			$values[$this->state_it->get('ReferenceName')] = 
 				$this->state_it->getDisplayName();
 				
 			$this->state_it->moveNext();
 		}
 		
 		return $values;
	}
	
	function getStyle()
	{
		return 'width:120px;';
	}

 	function getValueParm()
 	{
 		return 'state';
 	}
 	
 	function setDefaultValue( $value )
 	{
 	    $this->default = $value;
 	}
 	
 	function getValue()
 	{
 		$value = parent::getValue();
 		
 		if ( $value == '' )
 		{
 	        if ( $this->default != '' ) return $this->default;
 	    
 		    $state_it = $this->object->getSuitableToRoles( getSession()->getParticipantIt()->getRoles() );
 			
 			if ( $state_it->count() > 0 )
 			{
	 			return $state_it->get('ReferenceName');
 			}
 			else
 			{
	 			$this->state_it->moveFirst();
	 			
	 			return $this->state_it->get('ReferenceName');
 			}
 		}
 		
 		return $value;
 	}
 	
 	function hasAccess()
 	{
		return $this->state_it->count() > 0;
 	}
 }
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class FilterStateTransitionMethod extends FilterWebMethod
 {
 	var $state, $state_it;
 	
 	function FilterStateTransitionMethod( $state = null )
 	{
 		if ( is_object($state) )
 		{
	 		$this->state = $state;
 			$this->state_it = $this->state->getAll();
 			
 			parent::FilterWebMethod( $state->getClassName() );
 		}
 		else
 		{
 			parent::FilterWebMethod( '' );
 		}
 	}

 	function getCaption()
 	{
 		return translate('Переход');
 	}
 	
 	function getValues()
 	{
  		$values = array (
 			'all' => translate('Все'),
 			);
 		
 		$transition = getFactory()->getObject('Transition');
 		$transition_it = $transition->getByRefArray(
 			array( 'SourceState' => $this->state_it->idsToArray() )
 			);
 		
 		while ( !$transition_it->end() )
 		{
 			$source_it = $transition_it->getRef('SourceState');
 			$target_it = $transition_it->getRef('TargetState');
 			
 			$values[' '.$transition_it->getId()] = $transition_it->getDisplayName().' ('.
 				$source_it->getDisplayName().' > '.$target_it->getDisplayName().')';
 				
 			$transition_it->moveNext();
 		}
 		
 		return $values;
	}
	
	function getStyle()
	{
		return 'width:120px;';
	}

 	function getValueParm()
 	{
 		return 'transition';
 	}
 	
 	function hasAccess()
 	{
		return $this->state_it->count() > 0;
 	}
 }

?>