<?php
include_once SERVER_ROOT_PATH."pm/views/workflow/FieldAttributeDictionary.php";

class FormFieldForm extends PMPageForm
{
    function extendModel()
    {
        parent::extendModel();

        $object = $this->getObject();
        $object->setAttributeVisible('Entity', false);
    }

    function createFieldObject( $attr_name )
	{
		switch( $attr_name )
		{
            case 'ReferenceName':
                $attributeObject = getFactory()->getObject($this->getFieldValue('Entity'));
                $readonly = str_replace('"',"'",
                    JsonWrapper::encode($attributeObject->getAttributesReadonly()));
                $field = new FieldAttributeDictionary($attributeObject);
                $field->setScript("updateStateAttributeData($('#{$this->getId()}'),$(this),{$readonly});");
                $field->skipCustomAttributes();
                return $field;
			default:
				return parent::createFieldObject( $attr_name );
		}
	}

    function getFieldDescription($field_name)
    {
        switch( $field_name ) {
            case 'DefaultValue':
                return text(3319);
        }
        return parent::getFieldDescription($field_name);
    }
}