<?php
include "fields/AutoActionConditionsField.php";

class AutoActionForm extends PMPageForm
{
    private $subject = null;

    function __construct( $object )
    {
        parent::__construct($object);
        $this->subject = getFactory()->getObject($object->getSubjectClassName());
    }

    function extendModel()
	{
		parent::extendModel();
		
		$object = $this->getObject();

		foreach( $object->getActionAttributes() as $attribute )
		{
			if ( $this->subject->getAttributeType($attribute) == '' ) continue;
			$object->addAttribute(
                $attribute,
                $attribute == 'State'
                    ? 'REF_' . $this->subject->getStateClassName() . 'Id'
                    : $this->subject->getAttributeDbType($attribute),
                $this->subject->getAttributeUserName($attribute),
                true,
                false
			);
            $groups = $this->subject->getAttributeGroups($attribute);
            if ( is_array($groups) ) $object->setAttributeGroups($attribute, $groups);
            $object->addAttributeGroup($attribute, 'actions');
		}

		$object->setAttributeVisible('Actions', false);
		$object->setAttributeType('Actions', 'AutoActions');
		$object->setAttributeVisible('Conditions', true);
	}
	
	function createFieldObject( $name ) 
	{
		switch ( $name )
		{
		    case 'Conditions':
		    	return new AutoActionConditionsField($this->getObject());
            case 'EventType':
                return new FieldDictionary($this->getObject()->getAttributeObject($name));
			default:
                if ( in_array('dictionary', $this->getObject()->getAttributeGroups($name)) ) {
                    return new FieldCustomDictionary($this->getObject(), $name);
                }
				return parent::createFieldObject( $name );
		}
	}

	function getShortAttributes()
    {
        $attributes = array(
            'Type', 'Priority'
        );
        foreach( $this->subject->getAttributes() as $attribute => $value ) {
            if ( in_array($this->subject->getAttributeType($attribute), array('integer','float')) ) {
                $attributes[] = $attribute;
            }
        }
        return array_merge(
            parent::getShortAttributes(),
            $attributes
        );
    }
}