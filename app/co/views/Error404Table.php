<?php
 
class Error404Table extends CoPageTable
{
 	function getCaption()
 	{
		return getFactory()->getObject('SystemSettings')->getAll()->getDisplayName();
 	}
	
	function getRenderParms( $parms )
    {
         global $plugins;
         
         $reasons = array();

         if ( getSession()->getUserIt()->getId() == '' )
         {
             $reasons[] = str_replace('%url', '/login', text(1333));
         }
         
         if ( $plugins->hasIncluded('eecoplugin') )
         {
             $reasons[] = text(1333);
         }
         
         $reasons[] = text(676);
         
         return array_merge( parent::getRenderParms($parms), array (
             'reasons' => $reasons,
         	 'missed_url' => SanitizeUrl::parseUrlSkipQueryString($_REQUEST['redirect'])
         ));
    }
     
	function getTemplate()
    {
		if ( getSession()->getUserIt()->getId() > 0 )
		{
			return 'co/Error404Table.php';	     
		}
		else
		{
			return 'co/Error404TablePublic.php';	     
		}
    }
}
