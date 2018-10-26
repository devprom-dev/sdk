<?php

include "ArtefactTypeIterator.php";

class ArtefactType extends Metaobject 
{
 	function __construct() 
 	{
		parent::__construct('pm_ArtefactType');
	}
	
	function createIterator()
	{
		return new ArtefactTypeIterator( $this );
	}
	
	function getPage() 
	{
		return getSession()->getApplicationUrl().'module/fileserver/folders?';
	}
}
