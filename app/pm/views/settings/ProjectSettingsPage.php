<?php

include "ProjectForm.php";
include "StoreTemplateForm.php";
include "ApplyTemplateForm.php";

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

    function getTable()
    {
        return null;
    }

    function getForm()
    {
        switch ( $_REQUEST['mode'] )
        {
            case 'export-settings':
                return new StoreTemplateForm( getFactory()->getObject('pm_ProjectTemplate') );
                	
            case 'import-settings':
                return new ApplyTemplateForm( getFactory()->getObject('pm_ProjectTemplate') );
                	
            default:
                return new ProjectForm(getSession()->getProjectIt());
        }
    }
}