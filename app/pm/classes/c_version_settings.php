<?php

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class VersionSettingsIterator extends OrderedIterator
 {
 	function get( $attr )
 	{
 		global $project_it;
 	
 		if ( is_object($project_it) && $attr == 'Caption' )
 		{
	 		return $project_it->getDisplayName();
 		}
 		
 		return parent::get( $attr );
 	}
 }

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class VersionSettings extends Metaobject 
 {
 	function VersionSettings() 
 	{
		parent::Metaobject('pm_VersionSettings');
	}
	
	function createIterator() 
	{
		return new VersionSettingsIterator( $this );
	}

	function getPage()
	{
	    $session = getSession();
		return $session->getApplicationUrl().'project/versionsettings?';
	}
 }
