<?php
include_once "FieldAttributeDictionary.php";

class FormStateAttributeEmbedded extends PMFormEmbedded
{
    private $attributeObject = null;

    function setAttributeObject( $object ) {
        $this->attributeObject = $object;
    }

 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'ReferenceName':
 			case 'IsVisible':
 			case 'IsRequired':
            case 'IsReadonly':
            case 'IsMainTab':
            case 'IsAskForValue':
            case 'IsVisibleOnEdit':
                return true;

 			default:
 				return false;
 		}
 	}
 	
 	function IsAttributeObject( $attr_name )
 	{
 		switch ( $attr_name )
 		{
 			case 'ReferenceName':
 				return true;
 			default:
 				return parent::IsAttributeObject( $attr_name );
 		}
 	}
 	
 	function createField( $attr_name ) 
	{
		switch( $attr_name )
		{
			case 'ReferenceName':
                $readonly = str_replace('"',"'",
                    JsonWrapper::encode(
                        array_values(
                            array_diff(
                                $this->attributeObject->getAttributesReadonly(),
                                array(
                                    'Fact'
                                )
                            )
                        )
                    )
                );
				$field = new FieldAttributeDictionary($this->attributeObject);
                $field->setScript("updateStateAttributeData($('#embeddedFormBody{$this->getFormId()}'),$(this),{$readonly});");
                return $field;
			default:
				return parent::createField( $attr_name );
		}
	}
}