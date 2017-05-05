<?php
include "TaskTypeStateIterator.php";

class TaskTypeState extends Metaobject
{
    function __construct() {
        parent::__construct('pm_TaskTypeState');
    }

    function createIterator() {
        return new TaskTypeStateIterator( $this );
    }

    function add_parms($parms)
    {
        if ( is_numeric($parms['State']) ) {
            $parms['State'] = getFactory()->getObject('IssueState')->getExact($parms['State'])->get('ReferenceName');
        }
        return parent::add_parms($parms);
    }
}