<?php

class TaskTypeStateIterator extends OrderedIterator
{
    function getDisplayName() {
        return $this->getRef('TaskType')->getDisplayName();
    }
}
