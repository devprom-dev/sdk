<?php
include_once SERVER_ROOT_PATH . "pm/classes/project/ProjectModelExtendedBuilder.php";
include "ProjectForm.php";
include "ProjectSettingsTable.php";

class ProjectSettingsPage extends PMPage
{
    function getObject() {
        getSession()->addBuilder(new ProjectModelExtendedBuilder());
        return getFactory()->getObject('Project');
    }

    function getTable() {
        return new ProjectSettingsTable($this->getObject());
    }

    function getEntityForm() {
		$form = new ProjectForm($this->getObject());
		$form->edit(getSession()->getProjectIt()->getId());
		return $form;
    }

    function needDisplayForm() {
        return $_REQUEST['mode'] == 'settings' || parent::needDisplayForm();
    }
}