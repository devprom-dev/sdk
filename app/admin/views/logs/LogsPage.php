<?php
include 'LogsTable.php';
include 'LogForm.php';

class LogsPage extends AdminPage
{
    function getTable() {
        return new LogsTable($this->getObject());
    }

    function getObject() {
        return getFactory()->getObject('SystemLog');
    }

    function getEntityForm()
    {
    	if ( $_REQUEST['cms_BackupId'] == '' ) return null;

    	$form = new LogForm($this->getObject());
        $form->show($this->getObject()->getExact($_REQUEST['cms_BackupId']));
        return $form;
    }
}

