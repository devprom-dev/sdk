<?php

namespace Devprom\ProjectBundle\Service\Settings;

include_once SERVER_ROOT_PATH."pm/classes/project/CloneLogic.php";

class ModulesSettingsService implements SettingsService
{
	public function reset()
	{
 		// disable any model events handler
		getFactory()->setEventsManager( new \ModelEventsManager() );

		foreach( $this->getIterators() as $object_it )
		{
            switch ( $object_it->object->getEntityRefName() ) {
                case 'pm_Workspace':
                    $system_it = $object_it->object->getRegistry()->Query(
                        array (
                            new \FilterAttributePredicate('SystemUser', getSession()->getUserIt()->getId()),
                            new \FilterBaseVpdPredicate(),
                        )
                    );
                    while( !$system_it->end() ) {
                        $system_it->delete();
                        $system_it->moveNext();
                    }
                    break;
                default:
                    while(!$object_it->end()) {
                        // remove personal settings of the current user
                        if ( $object_it->get('Participant') == getSession()->getParticipantIt()->getId() ) {
                            $object_it->object->delete($object_it->getId());
                        }
                        $object_it->moveNext();
                    }
            }
		}

        getFactory()->getCacheService()->invalidate('sessions');
		getSession()->truncate();
	}

    public function resetForAll()
    {
        // disable any model events handler
        getFactory()->setEventsManager( new \ModelEventsManager() );

        foreach( $this->getIterators() as $object_it )
        {
            switch ( $object_it->object->getEntityRefName() ) {
                case 'pm_Workspace':
                    $system_it = $object_it->object->getRegistry()->Query(
                        array (
                            new \FilterAttributeNotNullPredicate('SystemUser'),
                            new \FilterBaseVpdPredicate(),
                        )
                    );
                    while( !$system_it->end() ) {
                        $system_it->delete();
                        $system_it->moveNext();
                    }
                    break;
                default:
                    while(!$object_it->end()) {
                        if ( $object_it->get('Participant') > 0 ) {
                            $object_it->object->delete($object_it->getId());
                        }
                        $object_it->moveNext();
                    }
            }
        }

        getFactory()->getCacheService()->invalidate('sessions');
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
                case 'pm_Workspace':
                    $system_it = $object_it->object->getRegistry()->Query(
                        array (
                            new \FilterAttributeNullPredicate('SystemUser'),
                            new \FilterBaseVpdPredicate(),
                        )
                    );
                    while( !$system_it->end() )
                    {
                        $system_it->delete();
                        $system_it->moveNext();
                    }
                    break;
                case 'pm_CustomReport':
                    $it = $object_it->object->getRegistry()->Query(
                        array (
                            new \CustomReportCommonPredicate(),
                            new \FilterBaseVpdPredicate()
                        )
                    );
                    while( !$it->end() ) {
                        $object_it->object->delete($it->getId());
                        $it->moveNext();
                    }
                    break;
            }

			\CloneLogic::Run(
				$context,
				$object_it->object,
				$object_it->object->createXMLIterator($xml),
				getSession()->getProjectIt()
			);
		}

        getFactory()->getCacheService()->invalidate('sessions');
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
					// filter only personal settings to be copied as defaults
					$object_it = $object_it->object->createCachedIterator(
					    array_values(
                            array_filter($object_it->getRowset(), function($row) {
                                return $row['Participant'] == getSession()->getParticipantIt()->getId();
                            })
                        )
					);
					break;
                case 'pm_Workspace':
                    $system_it = $object_it->object->getRegistry()->Query(
                        array (
                            new \FilterAttributePredicate('SystemUser', 'none'),
                            new \FilterBaseVpdPredicate(),
                        )
                    );
                    while( !$system_it->end() )
                    {
                        $system_it->delete();
                        $system_it->moveNext();
                    }
                    break;
			}

			\CloneLogic::Run(
				$context,
				$object_it->object,
				$object_it,
				getSession()->getProjectIt()
			);
		}

        // remove personal settings of the current user, global settings will be used instead
        $object_it = getFactory()->getObject('pm_UserSetting')->getRegistry()->Query(
            array(
                new \FilterAttributePredicate('Participant', getSession()->getParticipantIt()->getId()),
                new \FilterVpdPredicate()
            )
        );
        while(!$object_it->end()) {
            $object_it->object->delete($object_it->getId());
            $object_it->moveNext();
        }

        getFactory()->getCacheService()->invalidate('sessions');
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

        $workspace = getFactory()->getObject('Workspace');
        $workspace_it = $workspace->getRegistry()->getDefault(getSession()->getProjectIt());

        $iterators[] = $workspace->getRegistry()->Query(
            array (
                new \FilterInPredicate($workspace_it->idsToArray())
            )
        );

        $menu_it = getFactory()->getObject('pm_WorkspaceMenu')->getRegistry()->Query(
            array (
                new \FilterAttributePredicate('Workspace', $workspace_it->idsToArray()),
                new \SortOrderedClause()
            )
        );
        $iterators[] = $menu_it;

        $iterators[] = getFactory()->getObject('pm_WorkspaceMenuItem')->getRegistry()->Query(
            array (
                new \FilterAttributePredicate('WorkspaceMenu', $menu_it->idsToArray()),
                new \SortOrderedClause()
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