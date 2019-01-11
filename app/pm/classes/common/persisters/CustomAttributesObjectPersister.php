<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

class CustomAttributesObjectPersister extends CustomAttributesPersister
{
    private $objectIt = null;

    function setObjectIt( $objectIt ) {
        $this->objectIt = $objectIt;
    }

    protected function getAttributeIt() {
        return getFactory()->getObject('pm_CustomAttribute')->getRegistry()->Query(
            array(
                new CustomAttributeObjectPredicate($this->objectIt)
            )
        );
    }
}