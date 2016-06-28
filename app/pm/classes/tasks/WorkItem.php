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
        $this->addAttribute('Description', 'WYSIWYG', translate('Описание'), true, false, '', 6);

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
            'Attachment'
        );

        foreach( $this->getAttributes() as $attribute => $data ) {
            if ( in_array($attribute, $available) ) continue;
            $this->addAttributeGroup($attribute, 'system');
        }

        $this->addAttribute('DueDate', 'DATE', translate('Сроки'), true, false);
        $this->setAttributeOrderNum('TraceTask', 290);
    }

    function getVpds() {
        return array();
    }

    function createIterator() {
        return new WorkItemIterator($this);
    }
}