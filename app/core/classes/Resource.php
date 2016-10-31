<?php

include_once "ResourceRegistry.php";

class Resource extends Metaobject
{
    protected $languageUid = '';

 	function __construct( ObjectRegistrySQL $registry = null ) 
 	{
 	    global $session;
        if ( is_object($session) ) {
            $this->setLanguageUid($session->getLanguageUid());
        }
 		parent::__construct('cms_Resource', is_object($registry) ? $registry : new ResourceRegistry($this));
 		$this->setAttributeVisible( 'OrderNum', false );
 	}

 	function setLanguageUid( $uid ) {
 	    $this->languageUid = $uid;
    }

    function getLanguageUid() {
        return $this->languageUid;
    }
}