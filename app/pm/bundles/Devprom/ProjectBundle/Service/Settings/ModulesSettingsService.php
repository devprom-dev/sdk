<?php

namespace Devprom\ProjectBundle\Service\Settings;

include_once SERVER_ROOT_PATH."pm/classes/project/CloneLogic.php";

class ModulesSettingsService implements SettingsService
{
	public function reset()
	{
		$setting_it = getFactory()->getObject('PMUserSettings')->getRegistry()->Query(
				array (
					new \FilterAttributePredicate('Participant', getSession()->getParticipantIt()->getId()),
					new \FilterBaseVpdPredicate()
				)
		);
		
		while(!$setting_it->end())
		{
			$setting_it->delete();
			$setting_it->moveNext();
		}
	}
	
	public function makeDefault()
	{
 		// disable any model events handler
		getFactory()->setEventsManager( new \ModelEventsManager() );
		
		// prepare settings object
 		$settings = getFactory()->getObject('PMUserSettings');
 		$settings->setRegistry( new \PMUserSettingsExportRegistry() );
 		
 		$objects = array ( $settings );
 		$context = new \CloneContext();
 		
 		foreach ( $objects as $object )
		{
			$data_it = $object->getAll();
			
			switch ( $object->getEntityRefName() )
			{
				case 'pm_UserSetting':
					// remove common (glogal) settings for reports/modules
					$it = getFactory()->getObject('PMUserSettings')->getRegistry()->Query(
							array (
									new \SettingGlobalPredicate('-'),
									new \FilterBaseVpdPredicate()
							)
					);
					while( !$it->end() )
					{
						$it->delete();
						$it->moveNext();
					}
					break;
			}

			\CloneLogic::Run( $context, $object, $data_it, getSession()->getProjectIt() ); 
		}
		
		getSession()->truncate();
	}
}