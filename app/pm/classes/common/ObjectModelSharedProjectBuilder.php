<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once "persisters/EntityProjectPersister.php";

class ObjectModelSharedProjectBuilder extends ObjectModelBuilder
{
    var $shared_set;
    
    public function build( Metaobject $object )
    {
    	if ( $object instanceof SharedObjectSet ) return;
        if ( $object->hasAttribute('Project') ) return;
    	
        if ( !is_object($this->shared_set) ) {
            $this->shared_set = getFactory()->getObject('SharedObjectSet'); 
        }
	    
        if ( !$this->shared_set->hasObject( $object ) ) return;
	    if ( !$this->shared_set->sharedInProject($object, getSession()->getProjectIt()) ) return;

        $object->addAttribute('Project', 'REF_pm_ProjectId', translate('Проект'), false);
        $object->addPersister( new EntityProjectPersister );
    }
}