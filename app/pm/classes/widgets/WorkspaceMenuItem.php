<?php

class WorkspaceMenuItem extends Metaobject
{
    function __construct()
    {
        parent::__construct('pm_WorkspaceMenuItem');
    }
    
    function add_parms( $parms )
    {
    	if ( $parms['ReportUID'] != '' ) $parms['UID'] = $parms['ReportUID']; 

    	if ( $parms['ModuleUID'] != '' ) $parms['UID'] = $parms['ModuleUID']; 
    	
    	return parent::add_parms( $parms );
    }
}