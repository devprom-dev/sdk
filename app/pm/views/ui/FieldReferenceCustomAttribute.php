<?php

class FieldReferenceCustomAttribute extends FieldReferenceAttribute
{
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