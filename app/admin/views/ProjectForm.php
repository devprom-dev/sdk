<?php
include (dirname(__FILE__).'/../methods/c_project_methods.php');

class ProjectForm extends AdminPageForm
{
	function getRenderParms()
    {
        $object = $this->getObject();
        foreach( array_keys($object->getAttributes()) as $attribute ) {
            $object->setAttributeVisible($attribute, false);
        }
        $visible = array(
            'Caption', 'CodeName', 'Language', 'Importance', 'Description', 'IsClosed'
        );
        foreach( $visible as $attribute ) {
            $object->setAttributeVisible($attribute, true);
        }
        return parent::getRenderParms();
    }

    function getDeleteActions()
    {
        $actions = array();
        $method = new ProjectDeleteWebMethod($this->getObjectIt());
        if ( $method->HasAccess() )
        {
            $actions[] = array(
                'url' => $method->getRedirectUrl(), 'name' => $method->getCaption()
            );
        }
        return $actions;
    }
}
