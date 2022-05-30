<?php
include_once "ObjectTemplateFormTrait.php";
include_once SERVER_ROOT_PATH . 'pm/views/issues/RequestForm.php';

class RequestTemplateForm extends RequestForm
{
    use ObjectTemplateFormTrait;
    private $templateObject = null;

    function __construct($object) {
        parent::__construct(getFactory()->getObject('Request'));
        $this->traitConstruct($object);
    }
}