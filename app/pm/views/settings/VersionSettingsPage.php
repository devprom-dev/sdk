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
        global $model_factory;
        	
        $settings = $model_factory->getObject('pm_VersionSettings');

        $settings_it = $settings->getAll();
        
        return $settings_it;
    }
    
    function needDisplayForm()
    {
        $settings_it = $this->getObjectIt();
        
        if ( getFactory()->getAccessPolicy()->can_modify($settings_it) )
        {
            $this->form->edit( $settings_it->getId() );
        }
        else
        {
            $this->form->show( $settings_it->getId() );
        }
        	
        return true;
    }

    function getForm()
    {
        return new VersionSettingsForm();
    }
}