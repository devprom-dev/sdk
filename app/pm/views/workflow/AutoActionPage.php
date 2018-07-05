<?php
include "AutoActionForm.php";
include "AutoActionTable.php";
        
class AutoActionPage extends PMPage
{
    function __construct()
    {
        parent::__construct();
        if ( $this->needDisplayForm() ) {
            $this->addInfoSection( new PageSectionAttributes($this->getObject(),'actions',text(2444)) );
            $this->addInfoSection( new PageSectionAttributes($this->getObject(),'comment',text(2477)) );
            $methodologyIt = getSession()->getProjectIt()->getMethodologyIt();
            if ( $methodologyIt->HasTasks() ) {
                $this->addInfoSection( new PageSectionAttributes($this->getObject(),'task',text(2478)) );
            }
        }
    }

    function getObject() {
		return getFactory()->getObject('IssueAutoAction');
	}
	
    function getTable() {
        return new AutoActionTable($this->getObject());
    }

    function getForm() {
        return new AutoActionForm($this->getObject());
    }
}
