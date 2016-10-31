<?php
include_once "FieldTask.php";
include_once "FieldEstimationDictionary.php";

class RequestPlanningForm extends RequestForm
{
	function getEmbeddedForm() {
		return new FormTaskEmbedded();	
	}
	
 	function IsNeedButtonDelete() {
		return false;
	}

	function getTransitionAttributes()
	{
		return array_merge(
		    array('Caption', 'Priority', 'Estimation', 'Description'),
            $this->getObject()->getAttributesByGroup('additional'),
            $this->getObject()->getAttributesByGroup('trace')
        );
	}
	
	function createFieldObject( $attr_name )
	{
		$object_it = $this->getObjectIt();
 		switch ( $attr_name ) 
 		{
 		    case 'Tasks':
				return new FieldTask($object_it);
			case 'Estimation':
				if ( getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy()->hasDiscreteValues() ) {
					return new FieldEstimationDictionary($this->getObject());
				}
				else {
					return parent::createFieldObject( $attr_name );
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
		    case 'Iteration':
		        $registry = $this->createFieldObject($attr)->getObject()->getRegistry();
                $registry->setLimit(1);
                return $value == ''
                    ? $registry->Query(
                            array (
                                new FilterAttributePredicate('Version', parent::getFieldValue('PlannedRelease')),
                                new FilterVpdPredicate(),
                                new SortAttributeClause('RecordCreated')
                            )
                        )->getId()
                    : $value;

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
			case 'Description':
			    if ( is_a($field, 'FieldText') ) {
			        $field->setRows( 6 );
			    }
			    return $field;
    			     
    		default:
    			return parent::createField( $name );
    	}
    }

	function getHint()
	{
		return '';
	}

	function getFieldDescription($field_name)
    {
        switch( $field_name ) {
            case 'Tasks':
                $url = getFactory()->getObject('Module')->getExact('dicts-tasktype')->getUrl();
                return str_replace('%1', $url, text(2234));
            default:
                return parent::getFieldDescription($field_name);
        }
    }

    function getRenderParms()
    {
        return array_merge(parent::getRenderParms(), array(
            'showtabs' => true
        ));
    }
}