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
		$object->setAttributeVisible('Actions', false);
        $object->setAttributeType('Actions', 'AutoActions');
    }

	function createFieldObject( $name )
	{
		switch ( $name )
		{
		    case 'Conditions':
		    	return new AutoActionConditionsField($this->getObject());
            case 'Project':
                return new FieldAutoCompleteObject(getFactory()->getObject('ProjectActive'));
            case 'EventType':
                return new FieldDictionary($this->getObject()->getAttributeObject($name));
			default:
                if ( in_array('dictionary', $this->getObject()->getAttributeGroups($name)) ) {
                    return new FieldCustomDictionary(getFactory()->getObject('Request'), $name);
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

    function IsAttributeVisible( $attr )
    {
        switch ($attr) {
            case 'State':
                return true;
            default:
                return parent::IsAttributeVisible($attr);
        }
    }

    function getDefaultValue( $field )
    {
        switch( $field ) {
            case 'Project':
                return;
            default:
                return parent::getDefaultValue( $field );
        }
    }
}