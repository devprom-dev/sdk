<?php

class DashboardItemForm extends PMPageForm
{
    function extendModel()
    {
        $this->getObject()->setAttributeType('WidgetUID', 'REF_WidgetId');
        parent::extendModel();
    }
}