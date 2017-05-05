<?php
include 'SystemTemplateTable.php';
include 'SystemTemplateForm.php';

class SystemTemplatePage extends AdminPage
{
	function getObject()
	{
		return new \SystemTemplate();
	}
	
    function getTable()
    {
        return new SystemTemplateTable($this->getObject());
    }

    function getForm()
    {
        return new SystemTemplateForm($this->getObject());
    }
}

