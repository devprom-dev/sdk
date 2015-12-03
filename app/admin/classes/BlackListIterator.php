<?php

class BlackListIterator extends OrderedIterator
{
    function getDisplayName()
    {
        return $this->getRef('SystemUser')->getDisplayName();
    }
}
