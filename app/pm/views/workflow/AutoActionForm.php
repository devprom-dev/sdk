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

            case 'State':
                $field = new FieldState(getFactory()->getObject($this->subject->getStateClassName()));
                $field->setNullOption(true);
                return $field;

            case 'Type':
                $object = $this->getObject()->getAttributeObject($name);
                $object->setRegistry(new ObjectRegistrySQL($object));
                return new FieldDictionary($object);

            case 'ResetAttributes':
                $attributes = array_diff(
                    array_keys($this->subject->getAttributes()),
                    $this->subject->getAttributesByGroup('system'),
                    array(
                        'State', 'Fact', 'Author', 'RecordCreated', 'RecordModified'
                    )
                );
                $rowset = array();
                foreach( $attributes as $attribute ) {
                    if ( $this->subject->IsAttributeRequired($attribute) ) continue;
                    if ( !$this->subject->getAttributeEditable($attribute) ) continue;
                    if ( !$this->subject->IsAttributeStored($attribute) ) continue;
                    $rowset[] = array(
                        'entityId' => $attribute,
                        'Caption' => $this->subject->getAttributeUserName($attribute)
                    );
                }
                usort($rowset, function( $left, $right ) {
                    return $left['Caption'] > $right['Caption'];
                });

                $object = new Metaobject('entity');
                $field = new FieldDictionary($object->createCachedIterator($rowset));
                $field->setMultiple(true);
                return $field;

			default:
			    $groups = $this->getObject()->getAttributeGroups($name);
                if ( in_array('dictionary', $groups) && in_array('actions', $groups) ) {
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