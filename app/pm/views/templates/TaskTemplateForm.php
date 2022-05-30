<?php
include_once "ObjectTemplateFormTrait.php";
include_once SERVER_ROOT_PATH . 'pm/views/tasks/TaskForm.php';

class TaskTemplateForm extends TaskForm
{
    use ObjectTemplateFormTrait;
    private $templateObject = null;

    function __construct($object) {
        parent::__construct(getFactory()->getObject('Task'));
        $this->traitConstruct($object);
    }
}