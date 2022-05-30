<?php
include "BulkActionRegistry.php";

class BulkAction extends MetaobjectCacheable
{
    function __construct( $object )
    {
    	$this->base_object = $object;
    	parent::__construct('entity', new BulkActionRegistry($this));
    }
    
    function getObject()
    {
    	return $this->base_object;
    }

    function getVpds()
    {
        return array();
    }

    function getCacheKey( $getter, $class_name = '' )
    {
        return parent::getCacheKey( $getter, $class_name ).'-'.get_class($this->base_object);
    }

    function getCacheCategory()
    {
        // participant-wide cache
        return getSession()->getCacheKey();
    }

    private $base_object = null;
}
