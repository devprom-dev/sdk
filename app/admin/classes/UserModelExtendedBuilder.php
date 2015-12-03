<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include "persisters/UserActivityPersister.php";

class UserModelExtendedBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'cms_User' ) return;
    	
		$object->addAttribute( 'LastAuthTime', 'DATE', text(1866), false );
		$object->addAttribute( 'LastActivityDate', 'DATE', text(2059), false );

	    $object->addPersister( new UserActivityPersister() );
	    $object->addPersister( new UserDetailsPersister() );
    }
}