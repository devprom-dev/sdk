<?php

include "ProjectForm.php";

class ProjectSettingsPage extends PMPage
{
    function __construct()
    {
        parent::__construct();
        	
        $this->addInfoSection(new PMLastChangesSection(getSession()->getProjectIt()));
    }

    function needDisplayForm()
    {
        return true;
    }

    function getForm()
    {
		return new ProjectForm(getSession()->getProjectIt());
    }
}