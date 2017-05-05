<?php

class BusinessAction
{
 	function getId()
 	{
 		return null;
 	}
 	
 	function getDisplayName()
 	{
 		return '';
 	}
 	
 	function getObject()
 	{
 		return null;
 	}
 	
 	function apply( $object_it )
 	{
 		return false;
 	}

 	function getData() {
        return $this->data;
    }

    function setData( $data ) {
        $this->data = $data;
    }

 	private $data = array();
}
