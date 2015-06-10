<?php

include_once "persisters/EntityProjectPersister.php";

class ObjectMetadataSharedProjectBuilder extends ObjectMetadataBuilder 
{
    var $shared_set;
    
    public function build( ObjectMetadata $metadata )
    {
    	if ( is_a($metadata->getObject(), 'SharedObjectSet') ) return;

        $attributes = $metadata->getAttributes();
        
        if ( array_key_exists('Project', $attributes) ) return;
    	
        if ( !is_object($this->shared_set) )
        {
            $this->shared_set = getFactory()->getObject('SharedObjectSet'); 
        }
	    
        if ( !$this->shared_set->hasObject( $metadata->getObject() ) ) return;
        
	    if ( !$this->shared_set->sharedInProject($metadata->getObject(), getSession()->getProjectIt()) ) return;
	    
		$metadata->addAttribute('Project', 'REF_pm_ProjectId', translate('Проект'), false);

		$metadata->addPersister( new EntityProjectPersister );
    }
}