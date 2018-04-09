<?php
include "WorkItemTypeRegistry.php";

class WorkItemType extends Metaobject
{
    function __construct() {
        parent::__construct('pm_TaskType', new WorkItemTypeRegistry($this));
        $this->setSortDefault(array(new SortCaptionClause()));
    }

    function getVpds() {
        return array();
    }

    function IsDeletedCascade($object) {
        return false;
    }

    function IsUpdatedCascade($object) {
        return false;
    }
}