<?php
include "WorkItemRegistry.php";
include "WorkItemIterator.php";
include "predicates/WorkItemStatePredicate.php";

class WorkItem extends MetaobjectStatable
{
    function __construct()
    {
        parent::__construct('pm_Task', new WorkItemRegistry($this));

        $this->setAttributeOrderNum('TaskType', 1);
        $this->setAttributeType('TaskType', 'REF_WorkItemTypeId');
        $this->addAttribute('Description', 'WYSIWYG', translate('Описание'), false, false, '', 6);
        $this->addAttribute('IsTerminal', 'VARCHAR', '', false, false, '', 0);

        $available = array (
            'Caption',
            'Description',
            'TaskType',
            'Priority',
            'State',
            'Project',
            'RecentComment',
            'Fact',
            'Spent',
            'RecordCreated',
            'RecordModified',
            'StartDate',
            'FinishDate',
            'OrderNum',
            'Release',
            'PlannedRelease',
            'TraceTask',
            'Attachment',
            'Tags',
            'Assignee'
        );

        foreach( $this->getAttributes() as $attribute => $data ) {
            if ( in_array($attribute, $available) ) continue;
            $this->addAttributeGroup($attribute, 'system');
        }

        $this->addAttribute('DueDate', 'DATE', text(2264), true, false, '', 25);
        $this->setAttributeOrderNum('TraceTask', 290);
        foreach( array('DueDate') as $attribute ) {
            $this->addAttributeGroup($attribute, 'dates');
        }
    }

    function getVpds() {
        return array();
    }

    function createIterator() {
        return new WorkItemIterator($this);
    }

    function IsDeletedCascade($object) {
        return false;
    }

    function IsUpdatedCascade($object) {
        return false;
    }
}