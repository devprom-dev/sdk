<?php

class IntegrationIterator extends OrderedIterator
{
    function getDisplayName() {
        return $this->getRef('Caption')->getDisplayName();
    }
}