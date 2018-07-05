<?php
 
class Error500Table extends CoPageTable
{
    function getRenderParms( $parms )
    {
		global $plugins;

		$error = \core\classes\ExceptionHandler::Instance()->getLastError();

        return array_merge(
			 parent::getRenderParms( $parms ),
			 array (
             	'text' => $error['error']['message'] != ''
					? $error['error']['message'] : ($plugins->hasIncluded('eecoplugin') ? text(673) : text(677))
         	)
		);
    }
     
 	function getCaption()
 	{
		return getFactory()->getObject('SystemSettings')->getAll()->getDisplayName();
 	}
     
	function getTemplate()
    {
        if ( defined('SERVER_INFO_HIDDEN') && SERVER_INFO_HIDDEN ) {
            return 'co/Error500TablePublic.php';
        }
   		if ( getSession()->getUserIt()->getId() > 0 ) {
			return 'co/Error500Table.php';	     
		}
		return 'co/Error500TablePublic.php';
    }
}
