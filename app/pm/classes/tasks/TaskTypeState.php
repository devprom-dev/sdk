<?php
include "TaskTypeStateIterator.php";

class TaskTypeState extends Metaobject
{
    function __construct()
    {
        parent::__construct('pm_TaskTypeState');
    }

    function createIterator()
    {
        return new TaskTypeStateIterator( $this );
    }
}