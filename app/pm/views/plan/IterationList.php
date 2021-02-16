<?php

class IterationList extends PMPageList
{
    function extendModel()
    {
        parent::extendModel();

        $this->getObject()->setAttributeVisible('ReleaseNumber', false);
        $this->getObject()->setAttributeVisible('Caption', true);
    }
}
