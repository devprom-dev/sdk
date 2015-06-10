<?php

include "BulkActionRegistry.php";

class BulkAction extends Metaobject
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
	
    private $base_object = null;
}
