<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ObjectModifyWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ModifyAttributeWebMethod.php";
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
 		if ( !$this->object_it->IsTransitable() ) {
 			return false;
 		}
 		
 		if ( !$this->transition_it->doable( $this->object_it )) {
 			$this->reason_has_no_access = $this->transition_it->getNonDoableReason();
 			return false;
 		}

 		if ( !$this->transition_it->appliable() ) {
            $this->reason_has_no_access = text(2227);
            return false;
        }

 		return getFactory()->getAccessPolicy()->can_modify_attribute($this->object_it->object, 'State');
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
            $this->object_it->get('ProjectCodeName'),
            $this->object_it->getId(),
            get_class($this->object_it->object),
            $this->object_it->object->getEntityRefName(),
            $this->source_ref_name != ''
                    ? $this->source_ref_name : $this->transition_it->get('SourceStateReferenceName'),
            $this->target_ref_name != ''
                    ? $this->target_ref_name : $this->transition_it->get('TargetStateReferenceName'),
            $this->transition_it->getId(),
            $this->transition_it->getDisplayName()
		);
		return "javascript: workflowMoveObject('".join("','", $parms)."', ".$this->getRedirectUrl().", ".str_replace('"',"'",json_encode($parms_array, JSON_HEX_APOS)).")";
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

		$required = array();
		foreach( $object->getAttributes() as $attribute => $info ) {
			if( $_REQUEST[$attribute] != '' ) {
				$required[$attribute] = $_REQUEST[$attribute];
			}
		}

		if ( $this->getRedirectUrl() != '' )
		{
			echo '&'.http_build_query(
				array_map(function($value) {
						return SanitizeUrl::parseUrl($value);
					},
					array_merge(
						$required,
						array (
							'Transition' => $_REQUEST['transition']
						)
					)
				)
			);
		}
		else
		{
			$this->execute( $_REQUEST['transition'], $_REQUEST['object'], $_REQUEST['class'], $required );
		}
 	}

 	function execute( $transition_id, $object_id, $class, $required = array(), $doEvents = true )
 	{
 		getSession()->addBuilder( new WorkflowModelBuilder() );
 		
 		$object = getFactory()->getObject($class);
		$object_it = $object->getExact( $object_id );
        $this->setObjectIt($object_it);

		$transition = getFactory()->getObject('Transition');
		$transition->setVpdContext( $object_it );
		$transition_it = $transition->getExact($transition_id);
        $this->setTransitionIt($transition_it);

        if ( !$this->hasAccess() ) return;
        if ( $object->getAttributeType('State') == '' ) return;
		
		$state_it = $transition_it->getRef('TargetState');

		$required['State'] = $state_it->get('ReferenceName');
		$required['Transition'] = $transition_it->getId();
		foreach( $required as $key => $value ) {
			if ( $required[$key] == '' ) unset($required[$key]);
            if ( $key == 'attribute' ) $required[$required[$key]] = $required['value'];
		}

		$object_it->object->modify_parms($object_it->getId(), $required);

        if ( $doEvents ) {
            getFactory()->getEventsManager()->
                executeEventsAfterBusinessTransaction(
                    $object_it->object->getExact($object_it->getId()),
                    'WorklfowMovementEventHandler',
                    $required
                );
        }
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
		global $session;

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
			$object_it = $object->getExact( $object->add_parms( $parms ) );
			echo '{"message":"ok","object":"'.$object_it->getId().'"}';
			return;
		}

        try {
            if ( $parms['attribute'] == 'Project' ) {
                $session = new PMSession(
                    getFactory()->getObject('Project')->getByRef('VPD', $object_it->get('VPD')),
                    getSession()->getAuthenticationFactory()
                );

                ob_start();
                $method = new ModifyAttributeWebMethod();
                $method->execute_request($parms);
                ob_end_clean();

                $session = new PMSession(
                    getFactory()->getObject('Project')->getExact($parms['value']),
                    getSession()->getAuthenticationFactory()
                );
            }
        }
        catch( Exception $e ) {
            echo JsonWrapper::encode(array (
                "message" => "denied",
                "description" => $e->getMessage()
            ));
            return;
        }

		getFactory()->resetCache();
		$object_it = getFactory()->getObject($class_name)->getExact($parms['object']);

		if ( !getFactory()->getAccessPolicy()->can_modify_attribute($object, 'State') )
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

 		if ( $transition_it->count() < 1 )
		{
			$method = new ObjectModifyWebMethod($source_it);
			$result = array (
				"message" => "denied",
				"description" => IteratorBase::wintoutf8(str_replace('%1', $method->getJsCall(), text(1860)))				
			);
			
			echo JsonWrapper::encode($result);
			return;
		}
		
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
			$model_builder = new WorkflowTransitionAttributesModelBuilder(
			    $transition_it,
                array(),
                array_merge(
                    $object_it->getData(),
                    $parms,
                    array (
                        $parms['attribute'] => $parms['value']
                    )
                )
            );
			$model_builder->build( $object );

			$attributes = array();
			foreach( $object->getAttributes() as $attribute => $data )
			{
				if ( !$object->IsAttributeVisible($attribute) ) continue;
				if ( $parms[$attribute] != '' ) continue;
				if ( !in_array($attribute, array('Tasks','Fact')) && $object_it->get($attribute) != '' ) continue;

				$attributes[] = $attribute;
			}

			if ( $target_it->get('TaskTypes') != '' ) $attributes[] = 'Tasks';

			if ( count($attributes) > 0 )
			{
				$required = array();
				foreach( $object->getAttributes() as $attribute => $info ) {
					if ( $parms[$attribute] != '' ) $required[$attribute] = $parms[$attribute];
                    if ( $parms['attribute'] != '' ) $required[$parms['attribute']] = $parms['value'];
				}

				$url = '&'.http_build_query(
					array_map(function($value) {
							return SanitizeUrl::parseUrl($value);
						},
						array_merge(
							$required,
							array (
								'Transition' => $transition_it->getId(),
								'formonly' => 'true'
							)
						)
					)
				);

				if ( $object instanceof Request && in_array('Tasks', $attributes, true) && !in_array('TransitionComment', $attributes) ) {
					$url = getSession()->getApplicationUrl($object_it).'issues/board?mode=group&ChangeRequest='.$object_it->getId().$url;
				}
				else {
					$url = $object_it->getEditUrl().$url;
				}

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
		
		$transition_it->moveFirst();
		$method = new ObjectModifyWebMethod($transition_it);
		$method->setObjectUrl(
				getSession()->getApplicationUrl().'project/workflow/'.$object->getStateClassName().$transition_it->getEditUrl()
			);
		
		$result = array (
			"message" => "denied",
			"description" => IteratorBase::wintoutf8(
									str_replace('%1', $method->getJsCall(), 
											str_replace('%2', $reason, $reason == '' ? text(1012) : text(2018))
							 		)
							 )
		);
		
		echo JsonWrapper::encode($result);
 	}
 }
  
 ///////////////////////////////////////////////////////////////////////////////////////


