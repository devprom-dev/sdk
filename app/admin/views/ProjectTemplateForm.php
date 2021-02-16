<?php
include SERVER_ROOT_PATH . "admin/classes/model/validators/ModelProjectTemplateValidator.php";

class ProjectTemplateForm extends PageForm
{
    function getValidators() {
        return array_merge(
            parent::getValidators(),
            array(
                new ModelProjectTemplateValidator()
            )
        );
    }
}
