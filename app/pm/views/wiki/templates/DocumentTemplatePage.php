<?php
include "DocumentTemplateTable.php";
include "DocumentTemplateForm.php";
        
class DocumentTemplatePage extends PMPage
{
    function getObject() {
		return null;
	}
	
    function getTable() {
        return new DocumentTemplateTable($this->getObject());
    }

    function getEntityForm() {
        return new DocumentTemplateForm($this->getObject());
    }
}
