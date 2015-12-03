<?php

class TaskTypeStageIterator extends OrderedIterator
{
    function getDisplayName()
    {
        $stage_it = $this->getRef('ProjectStage');
        return $stage_it->getDisplayName();
    }
}
