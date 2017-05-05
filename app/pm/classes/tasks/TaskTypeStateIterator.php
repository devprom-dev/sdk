<?php

class TaskTypeStateIterator extends OrderedIterator
{
    function getDisplayName() {
        if ( $this->get('State') == '' ) {
            return $this->getBackwardDisplayName();
        }
        else {
            return getFactory()->getObject('IssueState')->getByRef('ReferenceName', $this->get('State'))->getDisplayName();
        }
    }

    function getBackwardDisplayName() {
        return $this->getRef('TaskType')->getDisplayName();
    }
}
