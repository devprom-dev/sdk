<?php
include_once "FieldTask.php";
include_once "FieldEstimationDictionary.php";

class RequestPlanningForm extends RequestForm
{
    function extendModel()
    {
        parent::extendModel();

        $transition_it = $this->getTransitionIt();
        if ( $transition_it->getId() != '' ) {
            if ( $transition_it->getRef('TargetState', new IssueState())->get('TaskTypes') != '' ) {
                $this->getObject()->addAttribute('Tasks', 'REF_pm_TaskId', translate('Задачи'), true);
            }
        }

        $defaultIteration = $this->getDefaultValue('Iteration');
        if ( $defaultIteration == '' ) {
            $this->getObject()->setAttributeVisible('Iteration', false);
        }
    }

    function processEmbeddedForms($object_it, $callback = null )
    {
        if ( $_REQUEST['Iteration'] == '' ) {
            $_REQUEST['Iteration'] = $object_it->get('Iteration');
        }
        $_REQUEST['Release'] = $_REQUEST['Iteration'];

        parent::processEmbeddedForms($object_it, $callback);
    }

 	function IsNeedButtonDelete() {
		return false;
	}

	function getTransitionAttributes()
	{
        $attributes = array_merge(
            $this->getObject()->getAttributesByGroup('additional'),
            $this->getObject()->getAttributesByGroup('trace')
        );
        foreach( $attributes as $key => $attribute ) {
            if ( !$this->getObject()->IsAttributeVisible($attribute) ) {
                unset($attributes[$key]);
            }
        }
		return array_merge(
		    array('Caption', 'UID', 'Priority', 'Estimation', 'Description'),
            array_values($attributes)
        );
	}

    function showDescriptionOnRight() {
        return false;
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
		    	return $value == '' && $this->getObject()->hasAttribute($attr)
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
                $matches = array();
                if ( preg_match(REGEX_INCLUDE_PAGE, $this->getFieldValue('Description'), $matches) ) {
                    $field->setReadOnly(true);
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