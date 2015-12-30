<?php

namespace Devprom\ProjectBundle\Service\Settings;

include_once SERVER_ROOT_PATH."pm/classes/project/CloneLogic.php";

class ModulesSettingsService implements SettingsService
{
	public function reset()
	{
 		// disable any model events handler
		getFactory()->setEventsManager( new \ModelEventsManager() );

		$xml = $this->getTemplateXml();
		if ( $xml == '' ) return;

		$context = new \CloneContext();
		foreach( $this->getObjects() as $object )
		{
			$object_it = $object->getAll();
			while(!$object_it->end())
			{
				$object->delete($object_it->getId());
				$object_it->moveNext();
			}
			
			$iterator = $object->createXMLIterator($xml);
			\CloneLogic::Run( $context, $object, $iterator, getSession()->getProjectIt() ); 
		}
		getSession()->truncate();
	}
	
	public function makeDefault()
	{
 		// disable any model events handler
		getFactory()->setEventsManager( new \ModelEventsManager() );
		
		// prepare settings object
 		$context = new \CloneContext();
 		foreach ( $this->getObjects() as $object )
		{
			$object_it = $object->getAll();
			while(!$object_it->end())
			{
				$object->delete($object_it->getId());
				$object_it->moveNext();
			}
			$object_it->moveFirst();
			\CloneLogic::Run( $context, $object, $object_it, getSession()->getProjectIt() ); 
		}
		getSession()->truncate();
	}
	
	protected function getObjects()
	{
		$settings = new \PMUserSettings();
 		$settings->setRegistry( new \PMUserSettingsExportRegistry() );
		return array ( $settings );
	}

	protected function getTemplateXml()
	{
 		return file_get_contents(
 				getFactory()->getObject('ProjectTemplate')->getTemplatePath(
 						getSession()->getProjectIt()->get('Tools')
				)
 			);
	}
}