<?php
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH.'pm/classes/workflow/WorkflowModelBuilder.php';
include_once SERVER_ROOT_PATH."pm/classes/workflow/WorkflowTransitionAttributesModelBuilder.php";

class TransitionStateMethod extends WebMethod
{
 	var $transition_it, $object_it;
 	protected $reason_has_no_access;
 	private $target_ref_name = '';
 	private $source_ref_name = '';
 	
 	function TransitionStateMethod ( $transition_it = null, $object_it = null )
 	{
 		parent::WebMethod();
	
		if ( is_object($transition_it) ) {
	 		$this->transition_it = $transition_it;  
	 		$this->object_it = $object_it;
		}
		if ( is_object($object_it) ) {
			$this->setSourceStateRefName( $object_it->get('State') );
		}
        $this->setRedirectUrl( 'devpromOpts.updateUI' );
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

        $required['WasRecordVersion'] = $object_it->get('RecordVersion');
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

 ///////////////////////////////////////////////////////////////////////////////////////


