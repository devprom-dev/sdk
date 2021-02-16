<?php
include "ObjectsListWidgetIterator.php";
include "ObjectsListWidgetRegistry.php";

class ObjectsListWidget extends CacheableSet
{
    function __construct() {
        parent::__construct(new ObjectsListWidgetRegistry($this));
    }

    function createIterator() {
        return new ObjectsListWidgetIterator($this);
    }

    function getWidgetObject( $referenceName )
    {
        switch( $referenceName ) {
            case 'PMReport':
                if ( !is_object($this->report) ) $this->report = getFactory()->getObject('PMReport');
                return $this->report;
            default:
                if ( !is_object($this->module) ) $this->module = getFactory()->getObject('Module');
                return $this->module;
        }
    }

    private $module = null;
    private $report = null;
}
