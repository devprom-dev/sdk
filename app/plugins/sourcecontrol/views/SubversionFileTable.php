<?php

class SubversionFileTable extends PMPageTable
{
    var $file_body;

    function SubversionFileTable( $subversion_it )
    {
        parent::__construct( $this->getObject() );
        	
        $connector = $subversion_it->getConnector();
        	
        $this->file_body = IteratorBase::utf8towin(
                $connector->getTextFile(
                        IteratorBase::wintoutf8($_REQUEST['path']), $_REQUEST['version']
                )
        );
    }

    function getObject()
    {
        return getFactory()->getObject('pm_Subversion');
    }

    function getTemplate()
    {
        return "../../plugins/sourcecontrol/templates/SubversionFileTable.php";
    }
    
    function getRenderParms( $parms )
    {
        global $_REQUEST;
        
        return array_merge( parent::getRenderParms( $parms ), array (
            'file_body' => $this->file_body,
            'path' => $_REQUEST['path'],
            'name' => IteratorBase::utf8towin($_REQUEST['name']),
            'version' => $_REQUEST['version'],
        ));
    }
} 