<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include "persisters/UserLastAccessTimePersister.php";

class UserModelExtendedBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'cms_User' ) return;
    	
		$object->addAttribute( 'LastAccessTime', 'DATETIME', text(1866), false );
			
	    $object->addPersister( new UserLastAccessTimePersister() );
	    
	    $object->addPersister( new UserDetailsPersister() );
    }
}