<?php
include "ExampleEntityForm.php";
include "ExampleEntityTable.php";
include "ExampleEntitySettingsBuilder.php";
        
class ExampleEntityPage extends PMPage
{
    function __construct() {
        getSession()->addBuilder(new ExampleEntitySettingsBuilder());
        parent::__construct();
    }

    function getObject() {
		return getFactory()->getObject('ExampleEntity');
	}
	
    function getTable() {
        return new ExampleEntityTable($this->getObject());
    }

    function getEntityForm() {
        return new ExampleEntityForm($this->getObject());
    }
}
