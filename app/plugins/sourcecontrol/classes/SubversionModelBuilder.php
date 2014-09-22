<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
		
class SubversionModelBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'pm_Subversion' ) return;
    	
		include_once 'svn/SubversionConnector.php';
                
		$object->addConnector( new SubversionConnector() );

		include_once 'git/GitConnector.php';
                
		$object->addConnector( new GitConnector() );
                
		include_once 'tfs/TFSConnector.php';

		$object->addConnector( new TFSConnector() );
    }
}
