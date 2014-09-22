<?php

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class PublicInfo extends Metaobject
 {
 	function PublicInfo() {
 		parent::Metaobject('pm_PublicInfo');
 	}
 	
    function getAttributeUserName( $name )
    {
    	switch ( $name )
    	{
    		default:
    			return parent::getAttributeUserName( $name );
    			 
    	}
    }
 }

?>
