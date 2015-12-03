<?php

class TaskTypeStateIterator extends OrderedIterator
{
    function getDisplayName()
    {
        return getFactory()->getObject('IssueState')->getByRef('ReferenceName', $this->get('State'))->getDisplayName();
    }
}
