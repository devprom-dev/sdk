<?php
include "CommentStateRegistry.php";
include "predicates/CommentStateFilter.php";

class CommentState extends MetaobjectCacheable
{
    function __construct() {
        parent::__construct('entity', new CommentStateRegistry($this));
        $this->addAttributeGroup('ReferenceName', 'alternative-key');
    }

    function getVpdValue() {
        return '';
    }
}