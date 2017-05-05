<?php

class TextTemplateList extends DictionaryItemsList
{
    function extendModel()
    {
        parent::extendModel();
        $this->getObject()->setAttributeVisible('Content', false);
    }

    function getGroup() {
        return 'ObjectClass';
    }
}
