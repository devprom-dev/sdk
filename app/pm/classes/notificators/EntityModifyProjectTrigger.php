<?php


include_once SERVER_ROOT_PATH."pm/classes/project/CloneLogic.php";

abstract class EntityModifyProjectTrigger extends SystemTriggersBase
{
	abstract protected function checkEntity( $object_it );
	abstract protected function & getObjectReferences( & $object_it );

	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
        if ( $kind != TRIGGER_ACTION_MODIFY ) return;
	    if ( !$this->checkEntity($object_it) ) return;

	    $references = $this->getObjectReferences($object_it);
	    if ( !is_array($references) ) return;

	    if ( !array_key_exists('Project', $content) )
	    {
		    $data = $this->getRecordData();

		    if ( !array_key_exists('Project', $data) ) return;
		    if ( $data['Project'] == $object_it->get('Project') ) return;

		    $project_it = getFactory()->getObject('Project')->getExact($data['Project']);
	    }
	    else
	    {
	    	$project_it = $object_it->getRef('Project');
	    }

	    $this->moveEntity( $object_it, $project_it, $references, $this->getRecordData() );
	}
	
	protected function moveEntity( & $object_it, & $target_it, & $references, $content )
	{
	    global $session;

        $wasProjectIt = getSession()->getProjectIt();
        $this->sourceProjectProcess = $wasProjectIt->get('Tools');

        $session = new PMSession($target_it);

 	 	foreach( $references as $object ) {
			$object->removeNotificator('ChangeLogNotificator');
            $object->removeNotificator('EmailNotificator');
            $object->disableVpd();
			$this->setProject($object->getAll(), $target_it, $content);
 	    }
		$this->updateChangeLog( $object_it, $wasProjectIt, $target_it );

        $session = new PMSession($wasProjectIt);
	}

	protected function setProject( $object_it, $target_it, $content )
	{
		$state = new StateBase();
		$storedObject = getFactory()->getObject(get_class($object_it->object));

		$methodology = getFactory()->getObject('Methodology');
		$targetEstimationValue = $methodology->getByRef('VPD', $target_it->get('VPD'))->get('RequestEstimationRequired');

		while( !$object_it->end() ) {
			$parms = array (
				'Project' => $target_it->getId(),
				'VPD' => $target_it->get('VPD')
			);
			foreach( array_keys($object_it->object->getAttributes()) as $attribute )
			{
                if ( $object_it->get($attribute) == '' ) continue;
			    if ( in_array($attribute, array('ParentPage','DocumentId')) && $object_it->get($attribute) != '' ) {
			        $ref_it = $object_it->object->getRegistry()->Query(
			            array (
			                new FilterInPredicate($object_it->get($attribute)),
                            new FilterVpdPredicate($target_it->get('VPD'))
                        )
                    );
			        if ( $ref_it->getId() == '' ) {
			            if ( $object_it->object instanceof ProjectPage ) {
                            $parms[$attribute] = $object_it->object->getRegistry()->Query(
                                array (
                                    new WikiRootFilter(),
                                    new FilterVpdPredicate($target_it->get('VPD'))
                                )
                            )->getId();
                        }
                        else {
                            $parms[$attribute] = '';
                        }
                    }
			        continue;
                }

                if ( in_array($attribute, array('Estimation','EstimationLeft')) ) {
                    $estimationValue = $methodology->getByRef('VPD', $object_it->get('VPD'))->get('RequestEstimationRequired');
			        if ( $estimationValue != $targetEstimationValue ) {
                        $parms[$attribute] = 'NULL';
                    }
                }

                if ( $attribute == 'Function' && $this->sourceProjectProcess == 'ticket_ru.xml' ) {
                    $parms[$attribute] = 'NULL';
                }

                if ( $object_it->object->IsReference($attribute) ) {
					$ref = $object_it->object->getAttributeObject($attribute);
                    if ( $ref->getVpdValue() == '' ) {
                        $parms[$attribute] = $object_it->get($attribute);
                        continue;
                    }
					$keys = $ref->getAttributesByGroup('alternative-key');
					if ( count($keys) > 0 ) {
						$queryParms = array(
                            new FilterVpdPredicate($target_it->get('VPD'))
                        );
                        $refIt = $ref->getRegistry()->Query(
                            array(
                                new FilterInPredicate($object_it->get($attribute))
                            )
                        );
						foreach( $keys as $key ) {
							$queryParms[] = new FilterAttributePredicate($key, $refIt->get($key));
						}
						$ref_it = $ref->getRegistry()->Query($queryParms);
						$parms[$attribute] = $ref_it->getId();
					}
				}
			}

			if ( $object_it->object instanceof MetaobjectStatable) {
				$state_it = $state->getRegistry()->Query(
					array(
						new StateClassPredicate($object_it->object->getStatableClassName()),
						new FilterAttributePredicate('ReferenceName', $object_it->get('State')),
						new FilterVpdPredicate($target_it->get('VPD'))
					)
				);
                // reset state if there is no such state in the target project
				if ( $state_it->getId() == '' || !array_key_exists('State', $content) ) $parms['State'] = '';
			}

            $object_it->object->modify_parms( $object_it->getId(), $parms );

            $storedObject->removeNotificator('AbstractServicedeskEmailNotificator');
            $storedIt = $storedObject->getExact($object_it->getId());
            getFactory()->getEventsManager()->notify_object_add($storedIt, $parms);
            getFactory()->getEventsManager()
                ->executeEventsAfterBusinessTransaction($storedIt, 'WorklfowMovementEventHandler', $parms);

			$object_it->moveNext();
		}
	}

	protected function updateChangeLog( $object_it, $source_it, $target_it )
	{
		// store message the issue has been moved
		$message = str_replace( '%1', $source_it->getDisplayName(),
			str_replace('%2', $target_it->getDisplayName(), text(1122)) );  
		
		$change_parms = array(
			'Caption' => $object_it->getDisplayName(),
			'ObjectId' => $object_it->getId(),
			'EntityName' => $object_it->object->getDisplayName(),
			'ClassName' => strtolower(get_class($object_it->object)),
			'ChangeKind' => 'deleted',
			'Content' => $message,
			'VisibilityLevel' => 1,
			'SystemUser' => getSession()->getUserIt()->getId()
		);

		$change = getFactory()->getObject('ObjectChangeLog');
		$change->disableVpd();
		$change->add_parms( $change_parms );

		// move related changes into target project
		$change_it = $change->getRegistry()->Query(
			array (
				new ChangeLogItemFilter($object_it),
				new FilterAttributePredicate('ChangeKind', 'modified,commented' )
			)
		);
		DAL::Instance()->Query(" UPDATE ObjectChangeLog SET VPD = '".$target_it->get('VPD')."' WHERE ObjectChangeLogId IN (".join(',',$change_it->idsToArray()).")");

		$change_parms['ChangeKind'] = 'modified';
		$change_parms['VPD'] = $target_it->get('VPD');
		$change->add_parms( $change_parms );
	}

	private $sourceProjectProcess = '';
}
 