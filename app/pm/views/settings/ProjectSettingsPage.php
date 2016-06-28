<?php
include "ProjectForm.php";
include "ProjectSettingsTable.php";

class ProjectSettingsPage extends PMPage
{
    function getObject() {
        return getSession()->getProjectIt()->object;
    }

    function getTable() {
        return new ProjectSettingsTable($this->getObject());
    }

    function getForm() {
		$form = new ProjectForm($this->getObject());
		$form->edit(getSession()->getProjectIt()->getId());
		return $form;
    }

    function needDisplayForm() {
        return $_REQUEST['mode'] == 'settings' || parent::needDisplayForm();
    }
}