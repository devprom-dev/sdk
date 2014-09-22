<?php

include "APIAccessPolicy.php";
include "AuthenticationSOAPFactory.php";
include "model/DataModelRegistryBuilderCommon.php";
include "versioning/ModelVersionAttributesBuilder.php";

class SOAPSession extends SessionBase
{
	function __construct()
	{
		parent::__construct( new AuthenticationSOAPFactory() );
		
		getLanguage();
	}
	
	function getProject()
	{
	    return $this->getAuthenticationFactory()->getProject();
	}
	
	function getCacheKey()
 	{
 		return 'soap';	
 	}
 	
 	function createBuilders()
 	{
 	    return array_merge(
 	            parent::createBuilders(), 
 	            array (
 	                    new IterationMetadataBuilder(),
 	                    new ReleaseMetadataBuilder(),
 	            		new TaskMetadataBuilder(),
 	                    new MilestoneMetadataBuilder(),
 	            		new ModelVersionAttributesBuilder(),
 	            		new DataModelRegistryBuilderCommon()
 	            )
 	    );
 	}
}
