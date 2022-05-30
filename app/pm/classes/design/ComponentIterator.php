<?php

class ComponentIterator extends ObjectHierarchyIterator
{
    function getDisplayName($prefix = '') {
        return $prefix . $this->get('CaptionAndType');
    }
}