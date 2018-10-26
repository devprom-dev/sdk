<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
		
class UserProcloudModelBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'cms_User' ) return;
    	
 		$object->addAttribute( 'ICQ', 'TEXT', 'Лицензии', false, true, '', 210 );
    }
}