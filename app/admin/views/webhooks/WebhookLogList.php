<?php

class WebhookLogList extends PageList
{
    function extendModel()
    {
        parent::extendModel();
        $this->getObject()->setAttributeVisible('Payload', false);
        $this->getObject()->setAttributeVisible('Headers', false);
        $this->getObject()->setAttributeVisible('Method', false);
        $this->getObject()->setAttributeVisible('RecordCreated', true);
    }
}
