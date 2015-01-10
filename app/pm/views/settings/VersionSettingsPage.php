<?php

include "VersionSettingsForm.php";

class VersionSettingsPage extends PMPage
{
    function __construct()
    {
        parent::__construct();
        	
        $this->addInfoSection( new PMLastChangesSection($this->getObjectIt()) );
    }

    function getObjectIt()
    {
        return getFactory()->getObject('pm_VersionSettings')->getAll();
    }
    
    function needDisplayForm()
    {
        $settings_it = $this->getObjectIt();
        
        if ( getFactory()->getAccessPolicy()->can_modify($settings_it) )
        {
            $this->getFormRef()->edit( $settings_it->getId() );
        }
        else
        {
            $this->getFormRef()->show( $settings_it->getId() );
        }
        	
        return true;
    }

    function getForm()
    {
        return new VersionSettingsForm();
    }
}