<?php

class PMForm extends AjaxForm
{
	private $customtypes = array();
	
	function __construct( $object )
	{
		parent::__construct($object);
		
        $it = getFactory()->getObject('pm_CustomAttribute')->getByEntity($this->getObject());
        
        while (!$it->end()) 
        {
            $this->customtypes[$it->get('ReferenceName')] = $it->get('AttributeType');

            $it->moveNext();
        }
	}
	
	function getSite()
	{
		return 'pm';
	}
	
	function getAttributeType( $attribute )
	{
		if ( $this->customtypes[$attribute] == 'dictionary' )
		{
			return 'custom';
		}

		return parent::getAttributeType( $attribute );
	}
	
	function drawCustomAttribute( $attribute, $value, $tab_index )
	{
		if ( $this->customtypes[$attribute] == 'dictionary' )
		{
			echo $this->getName($attribute);
			
			$field = new FieldCustomDictionary($this->getObject(), $attribute);
			
			$field->SetId($attribute);
			$field->SetName($attribute);
			$field->SetValue($value);
			$field->SetTabIndex($tab_index);
			
			$field->draw();
			
			return;
		}
		
		parent::drawCustomAttribute( $attribute, $value, $tab_index );
	}
}
