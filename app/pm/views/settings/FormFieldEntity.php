<?php

class FormFieldEntity extends PMPageForm
{
    function extendModel()
    {
        parent::extendModel();

        foreach( $this->getObject()->getAttributes() as $key => $info ) {
            $this->getObject()->setAttributeVisible($key, $key == 'Entity');
            $this->getObject()->setAttributeRequired($key, $key == 'Entity');
        }
    }

    function process()
    {
        if ( $_REQUEST['Entity'] == '' ) return false;
        echo json_encode(
            array(
                'Id' => $_REQUEST['Entity']
            )
        );
        return true;
    }

    function getCaption() {
        return text(3201);
    }}