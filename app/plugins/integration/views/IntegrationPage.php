<?php

include "IntegrationForm.php";
include "IntegrationTable.php";
include "IntegrationSettingsBuilder.php";
        
class IntegrationPage extends PMPage
{
    function __construct()
    {
        getSession()->addBuilder(new IntegrationSettingsBuilder());
        parent::__construct();
    }

    function getObject() {
		return getFactory()->getObject('Integration');
	}
	
    function getTable() {
        return new IntegrationTable($this->getObject());
    }

    function getForm()
    {
        $form = new IntegrationForm($this->getObject());
        if ( $this->needDisplayForm() ) {
            $this->addInfoSection(new PageSectionAttributes($form->getObject(), 'mapping', translate('integration6')));
            $this->addInfoSection(new PageSectionAttributes($form->getObject(), 'additional', translate('Лог')));
        }
        return $form;
    }
}
