<?php
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */

include 'MenuCustomizationTable.php';

class MenuCustomizationPage extends PMPage
{
    public function getTable()
    {
		return new MenuCustomizationTable(getFactory()->getObject('pm_Workspace'));
    }

 	function getModule()
 	{
 		return 'navigation-settings';
 	}
    
 	function getFullPageRenderParms()
 	{
 		$parms = parent::getFullPageRenderParms();
 		
 		unset($parms['navigation_parms']['areas']);
 		
 		$parms['inside'] = true;
 		$parms['has_horizontal_menu'] = false;
 		$parms['reports_edit_url'] = getFactory()->getObject('Module')->getExact('project-reports')->get('Url');
 		$parms['close_url'] = strpos($_SERVER['HTTP_REFERER'], '/menu/') === false ? $_SERVER['HTTP_REFERER'] : getSession()->getApplicationUrl();
 		$parms['hint_top'] = $parms['hint'];
 		$parms['hint'] = '';

        $roles = getSession()->getRoles();
        if ( $roles['lead'] ) {
            $parms['share_url'] = getSession()->getApplicationUrl().'settings/menu/makedefault';
        }

 		return $parms;
 	}
}
