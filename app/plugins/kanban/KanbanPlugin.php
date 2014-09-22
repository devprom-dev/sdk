<?php

include "KanbanPmPlugin.php";

class KanbanPlugin extends PluginBase
{
 	function getNamespace()
 	{
 		return 'kanban';
 	}
 
  	function getFileName()
 	{
 		return 'kanban.php';
 	}
 	
 	function getCaption()
 	{
 		return text('kanban1');
 	}
 	
 	function getIndex()
 	{
 	    return parent::getIndex() + 1000;
 	}
 	
 	function getSectionPlugins()
 	{
 		return array( new KanbanPmPlugin );
 	}
}