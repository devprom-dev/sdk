<?php
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */

include 'MenuCustomizationTable.php';

class MenuCustomizationPage extends PMPage {
    
    public function getTable()
    {
		return new MenuCustomizationTable(getFactory()->getObject('pm_Workspace'));
    }
    
 	function getFullPageRenderParms()
 	{
 		$parms = parent::getFullPageRenderParms();
 		
 		unset($parms['areas']);
 		
 		$parms['inside'] = true;
 		
 		$parms['has_horizontal_menu'] = false;
 		
 		$parms['reports_edit_url'] = getFactory()->getObject('Module')->getExact('project-reports')->get('Url');
 		
 		$parms['close_url'] = strpos($_SERVER['HTTP_REFERER'], '/menu/') === false ? $_SERVER['HTTP_REFERER'] : getSession()->getApplicationUrl();  

 		$parms['hint'] = getFactory()->getObject('UserSettings')->getSettingsValue('navigations-hint') != 'off' ? text(1806) : '';
 		
 		return $parms;
 	}
}
