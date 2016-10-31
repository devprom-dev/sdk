<?php

namespace Devprom\ProjectBundle\Service\Settings;

include_once SERVER_ROOT_PATH."pm/classes/project/CloneLogic.php";

class ModulesSettingsService implements SettingsService
{
	public function reset()
	{
 		// disable any model events handler
		getFactory()->setEventsManager( new \ModelEventsManager() );

		foreach( $this->getIterators() as $object_it ) {
			while(!$object_it->end()) {
				// remove personal settings of the current user
				if ( $object_it->get('Participant') == getSession()->getParticipantIt()->getId() ) {
					$object_it->object->delete($object_it->getId());
				}
				$object_it->moveNext();
			}
		}

		\SessionBuilder::Instance()->invalidate();
		getSession()->truncate();
	}

	public function resetToDefault()
	{
		// disable any model events handler
		getFactory()->setEventsManager( new \ModelEventsManager() );

		$xml = $this->getTemplateXml();
		if ( $xml == '' ) return;

		$context = new \CloneContext();
		foreach( $this->getIterators() as $object_it ) {
			switch ( $object_it->object->getEntityRefName() )
			{
				case 'pm_UserSetting':
					while(!$object_it->end()) {
						if ( $object_it->get('Participant') < 1 ) {
							$object_it->object->delete($object_it->getId());
						}
						$object_it->moveNext();
					}
					break;
				case 'pm_CustomReport':
					continue;
			}

			\CloneLogic::Run(
				$context,
				$object_it->object,
				$object_it->object->createXMLIterator($xml),
				getSession()->getProjectIt()
			);
		}

        \SessionBuilder::Instance()->invalidate();
		getSession()->truncate();
	}

	public function makeDefault()
	{
 		// disable any model events handler
		getFactory()->setEventsManager( new \ModelEventsManager() );
		
		// prepare settings object
 		$context = new \CloneContext();
 		foreach ( $this->getIterators() as $object_it ) {
			switch ( $object_it->object->getEntityRefName() )
			{
				case 'pm_UserSetting':
					// remove personal settings of the current user
					while(!$object_it->end()) {
						if ( $object_it->get('Participant') == getSession()->getParticipantIt()->getId() ) {
							$object_it->object->delete($object_it->getId());
						}
						$object_it->moveNext();
					}

					// filter only personal settings to be copied as defaults
					$object_it = $object_it->object->createCachedIterator(
						array_filter($object_it->getRowset(), function($row) {
							return $row['Participant'] == getSession()->getParticipantIt()->getId();
						})
					);
					break;
			}

			\CloneLogic::Run(
				$context,
				$object_it->object,
				$object_it,
				getSession()->getProjectIt()
			);
		}

        \SessionBuilder::Instance()->invalidate();
		getSession()->truncate();
	}

	protected function getIterators()
	{
		$iterators = array();

		$iterators[] = getFactory()->getObject('pm_CustomReport')->getRegistry()->Query(
			array (
				new \CustomReportMyPredicate(),
				new \FilterVpdPredicate()
			)
		);

		$settings = new \PMUserSettings();
 		$settings->setRegistry( new \PMUserSettingsExportRegistry() );
		$iterators[] = $settings->getAll();

		return $iterators;
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