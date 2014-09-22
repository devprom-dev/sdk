<?php

class ProjectStage extends Metaobject
{
 	function ProjectStage() 
 	{
 		parent::Metaobject('pm_ProjectStage');
 	}
 	
 	function createIterator() 
 	{
 		return new OrderedIterator( $this );
 	}
}
