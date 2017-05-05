<?php
include "model/IntegrationType.php";
include "model/IntegrationApplication.php";
include "model/IntegrationTracker.php";
include "model/Integration.php";
include "model/IntegrationLink.php";
include "PMPlugin.php";
include "COPlugin.php";

class integrationPlugin extends PluginBase
{
 	function getNamespace() {
 		return 'integration';
 	}
 
  	function getFileName() {
 		return 'integration.php';
 	}
 	
 	function getCaption() {
 		return text('integration1');
 	}
 	
 	function getIndex()	{
 	    return parent::getIndex() + 5000;
 	}
 	
 	function getSectionPlugins() {
 		return array( new integrationCO, new integrationPM );
 	}
 	
 	function IsUpdatedWithCore() {
 		return false;
 	}
}