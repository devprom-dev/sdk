<?php

include SERVER_ROOT_PATH."pm/classes/issues/RequestModelPlanningBuilder.php";

include_once "FieldTask.php";

class RequestPlanningForm extends PMPageForm
{
    protected function extendModel()
    {
		if ( getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() )
		{
			$builder = new RequestModelPlanningBuilder();
			$builder->build( $this->getObject() );
		}

		$builder = new RequestModelExtendedBuilder();
		$builder->build( $this->getObject() );

    	parent::extendModel();
    	
    	$this->getObject()->setAttributeRequired('Owner', false);
    	$this->getObject()->setAttributeVisible('Owner', false);

    	if ( getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() )
		{
	    	$this->getObject()->setAttributeRequired('PlannedRelease', false);
	    	$this->getObject()->setAttributeVisible('PlannedRelease', false);
		}
    }
    
	function getEmbeddedForm()
	{
		return new FormTaskEmbedded();	
	}
	
 	function IsNeedButtonDelete() 
 	{
		return false;
	}

	function getTransitionAttributes()
	{
		$attributes = array('Caption', 'Priority', 'Estimation');
		
		if ( $this->getFieldValue('Description') != '' ) {
		    $attributes[] = 'Description';
		}
		if ( getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() ) {
			$attributes[] = 'Release';
		}
		
		return $attributes;
	}
	
	function createFieldObject( $attr_name )
	{
		$object_it = $this->getObjectIt();
 		switch ( $attr_name ) 
 		{
 		    case 'Tasks':
 		        $iteration_it = getFactory()->getObject('Iteration')->getRegistry()->Query(
 		        		array (
 		        			new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED),
 		        			new FilterVpdPredicate()
 		        		)
 		        	);
				return new FieldTask($object_it, $iteration_it);
		
		    case 'Release':
 		        $iteration = getFactory()->getObject('Iteration');
 		        $iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED) );
				return new FieldDictionary( $iteration );
		
			case 'Estimation':
				$strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
				$field = $strategy->getEstimationFormField( $this );
				if ( !is_object($field) ) {
					return parent::createFieldObject( $attr_name );
				}
				else {
					return $field;
				}
			default:
				return parent::createFieldObject( $attr_name );
 		}	
	}

	function getDefaultValue( $attr )
	{
		$value = parent::getDefaultValue( $attr );
		
		switch( $attr )
		{
		    case 'Release':
    		case 'PlannedRelease':
		    	return $value == '' 
		    			? $this->createFieldObject($attr)->getObject()->getFirst()->getId()
		    			: $value;
		}
		
		return $value;
	}
	
    function createField( $name )
    {
        $field = parent::createField( $name );
        
    	switch ( $name )
    	{
			case 'Caption':
			
			    if ( $_REQUEST['Transition'] > 0 )
			    {
			        $field->setReadonly( true );
			    }
			
			    return $field;
			
			case 'Description':
			
			    if ( is_a($field, 'FieldText') )
			    {
			        $field->setRows( 6 );
			    }
			
			    if ( $_REQUEST['Transition'] > 0 )
			    {
			        $field->setReadonly( true );
			    }
			
			    return $field;
    			     
    		default:
    			return parent::createField( $name );
    	}
    }
}