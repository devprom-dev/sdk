<?php
 
class Error503Table extends CoPageTable
{
 	function getCaption()
 	{
		return getFactory()->getObject('SystemSettings')->getAll()->getDisplayName();
 	}
	
	function getTemplate()
     {
         return 'co/Error503Table.php';
     }
}
