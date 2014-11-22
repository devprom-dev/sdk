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
		$attributes = array('Caption', 'Release', 'Priority', 'Estimation');
		
		if ( $this->getFieldValue('Description') != '' ) 
		{
		    $attributes[] = 'Description';
		}
		
		return $attributes;
	}
	
	function createFieldObject( $attr_name )
	{
		global $_REQUEST, $model_factory;
		
		$object_it = $this->getObjectIt();
		
 		switch ( $attr_name ) 
 		{
 		    case 'Tasks':
 		        
 		        $iteration = $model_factory->getObject('Iteration');
 		        
 		        $iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED) );
 		        
 		        $iteration_it = $iteration->getAll();
 		        
				return new FieldTask($object_it, $iteration_it);
		
		    case 'Release':

 		        $iteration = $model_factory->getObject('Iteration');
 		        
 		        $iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED) );
		        
				return new FieldDictionary( $iteration );
		
			case 'Estimation':
			    
				$strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();

				$field = $strategy->getEstimationFormField( $this );
				
				if ( !is_object($field) )
				{
					return parent::createFieldObject( $attr_name );
				}
				else
				{
					return $field;
				}
				
			default:
				return parent::createFieldObject( $attr_name );
 		}	
	}

	function getFieldValue( $attr )
	{
		$value = parent::getFieldValue( $attr );
		
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