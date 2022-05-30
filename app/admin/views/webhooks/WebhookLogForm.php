<?php

class WebhookLogForm extends AdminPageForm
{
    function extendModel()
    {
        foreach( $this->getObject()->getAttributes() as $attribute => $info ) {
            $this->getObject()->setAttributeEditable($attribute, false);
        }
        parent::extendModel();
    }

    function createFieldObject($name)
    {
        switch( $name ) {
            case 'Payload':
                if ( json_decode(JSONViewerField::stripTags($this->getObjectIt()->get($name))) ) {
                    return new JSONViewerField();
                }
                break;
        }
        return parent::createFieldObject($name);
    }
}