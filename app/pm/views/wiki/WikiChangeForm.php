<?php

class WikiChangeForm extends PMPageForm
{
    protected function extendModel()
    {
 		parent::extendModel();
 		
 		$object = $this->getObject();
 		foreach( $object->getAttributes() as $attribute => $data ) {
 			$object->setAttributeVisible($attribute, false);
 			$object->setAttributeRequired($attribute, false);
 		}

        $object->setAttributeVisible('Content', true);
        $this->changeIt = getFactory()->getObject('WikiPageChange')->getExact($_REQUEST['revision']);
    }

    function IsAttributeEditable($attr_name) {
        return false;
    }

    function getFieldValue($field)
    {
        switch( $field ) {
            case 'Content':
                return $this->changeIt->get('Content');
            default:
                return parent::getFieldValue($field);
        }
    }
}