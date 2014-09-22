<?php

include 'LogsTable.php';
include 'LogForm.php';

class LogsPage extends AdminPage
{
    function getTable()
    {
        return new LogsTable( getFactory()->getObject('SystemLog') );
    }

    function getForm()
    {
    	if ( $_REQUEST['cms_BackupId'] == '' ) return null;
    	
        return new LogForm( getFactory()->getObject('SystemLog')->getExact($_REQUEST['cms_BackupId']) );
    }
}

