<?php

class FieldReferenceCustomAttribute extends FieldReferenceAttribute
{
    function __construct($object_it, $attribute, $attributeObject, $moreActions = array()) {
        parent::__construct($object_it, $attribute, $attributeObject, $moreActions, 'btn-xs');
    }

    protected function getLovIterator()
    {
        $attributeIt = getFactory()->getObject('pm_CustomAttribute')->getRegistry()->Query(
            array(
                new CustomAttributeObjectPredicate($this->getObjectIt())
            )
        );
        $attributeIt->moveTo('ReferenceName', $this->getAttribue());
        $this->getLovObject()->setVpdContext($attributeIt);

        return parent::getLovIterator();
    }
}