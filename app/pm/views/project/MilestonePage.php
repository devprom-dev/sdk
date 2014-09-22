<?php

include SERVER_ROOT_PATH.'pm/methods/c_milestone_methods.php';

include "MilestoneForm.php";
include "MilestoneTable.php";

class MilestonePage extends PMPage
{
 	function __construct()
 	{
 		parent::__construct();
 		
 		if ( $this->needDisplayForm() )
 		{
 			$object_it = $this->getObjectIt();

 			if ( is_object($object_it) && $object_it->count() > 0 )
 			{
				if ( $_REQUEST['Transition'] == '' )
				{
 				    $this->addInfoSection( new PageSectionComments($object_it) );
				}
 			}
 		}
 	}
 	
 	function getObject()
 	{
 		return new Milestone( new MilestoneExtendedRegistry() );
 	}
 	
 	function getTable() 
 	{
 		return new MilestoneTable( $this->getObject() );
 	}
 	
 	function getForm() 
 	{
 		return new MilestoneForm( $this->getObject() );
 	}
}
