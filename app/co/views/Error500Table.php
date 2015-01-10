<?php
 
class Error500Table extends CoPageTable
{
    function getRenderParms( $parms )
    {
         global $plugins;
         
         return array_merge( parent::getRenderParms( $parms ), array (
             'text' => $plugins->hasIncluded('eecoplugin') ? text(673) : text(677)
         ));
    }
     
 	function getCaption()
 	{
		return getFactory()->getObject('SystemSettings')->getAll()->getDisplayName();
 	}
     
	function getTemplate()
    {
   		if ( getSession()->getUserIt()->getId() > 0 )
		{
			return 'co/Error500Table.php';	     
		}
		else
		{
			return 'co/Error500TablePublic.php';	     
		}
    }
}
