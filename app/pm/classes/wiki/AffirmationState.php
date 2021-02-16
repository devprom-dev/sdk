<?php
include "AffirmationStateRegistry.php";
include "predicates/AffirmationStateFilter.php";

class AffirmationState extends MetaobjectCacheable
{
    function __construct() {
        parent::__construct('entity', new AffirmationStateRegistry($this));
        $this->addAttributeGroup('ReferenceName', 'alternative-key');
    }

    function getVpdValue() {
        return '';
    }
}