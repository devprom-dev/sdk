<?php
include_once "CloneContext.php";

class CloneLogic
{
    static function Run( & $context, $object, $iterator, $project_it )
    {
        $ids_map = $context->getIdsMap();

   		while ( !$iterator->end() )
		{
			$parms = array();

			$attrs = array();

			foreach( $object->getAttributes() as $ref_name => $attribute )
			{
			    if ( !$object->IsAttributeStored($ref_name) ) continue;
			    $attrs[] = $ref_name;
			}

			switch ( $object->getEntityRefName() )
			{
				case 'pm_CustomReport':
					$ids_map[$object->getEntityRefName()][$iterator->getId()] =
                        self::applyToCustomReport( $context, $attrs, $iterator, $project_it );
					break;

				// special case of template importing
				case 'pm_Project':
				    CloneLogic::applyToProject( $context, $attrs, $iterator, $project_it );
				    break;

				// special case of template importing
				case 'pm_Methodology':
				    CloneLogic::applyToMethodology( $context, $attrs, $iterator, $project_it );
				    break;
					
                case 'pm_TaskType':
                case 'pm_FeatureType':
                case 'WikiPageType':
                case 'pm_IssueType':
                case 'pm_TestExecutionResult':
                case 'pm_CustomAttribute':
                case 'pm_State':
                case 'pm_DashboardItem':
                    $id = CloneLogic::applyToLegacy( $context, $iterator->object->getAttributesByGroup('alternative-key'), $attrs, $iterator, $project_it );
                    if ( $id > 0 ) {
                        $ids_map[$object->getEntityRefName()][$iterator->getId()] = $id;
                    }
                    else {
                        $parms = CloneLogic::applyToObject( $context, $attrs, $parms, $iterator, $project_it );
                        if ( count($parms) > 0 ) {
                            $ids_map[$object->getEntityRefName()][$iterator->getId()] = self::duplicate( $iterator, $context, $parms );
                        }
                    }
                    break;

                case 'pm_AttributeValue':
                    // create or merge attribute values
                    $parms = CloneLogic::applyToObject( $context, $attrs, $parms, $iterator, $project_it );
                    $ids_map[$object->getEntityRefName()][$iterator->getId()] = self::duplicate( $iterator, $context, $parms );
                    break;

				// special case of template importing
				case 'pm_ProjectRole':
					$id = CloneLogic::applyToLegacy( $context, $iterator->object->getAttributesByGroup('alternative-key'), $attrs, $iterator, $project_it );
					if ( $id > 0 ) {
						$ids_map[$object->getEntityRefName()][$iterator->getId()] = $id;
					}
					elseif ( !$context->getRestoreFromTemplate() || !$context->getUseExistingReferences() )
					{
						$parms = CloneLogic::applyToObject( $context, $attrs, $parms, $iterator, $project_it );
    					if ( count($parms) > 0 ) {
    						$ids_map[$object->getEntityRefName()][$iterator->getId()] = self::duplicate( $iterator, $context, $parms );
    					}
					}
					else {
						unset($ids_map[$object->getEntityRefName()][$iterator->getId()]);
					}
					break;
					
				case 'pm_Participant':
					$id = CloneLogic::applyToLegacy( $context, array('SystemUser'), $attrs, $iterator, $project_it );
					
					if ( $id > 0 )
					{
						$ids_map[$object->getEntityRefName()][$iterator->getId()] = $id;
					}
					else
					{
						$parms = CloneLogic::applyToObject( $context, $attrs, $parms, $iterator, $project_it );
					
    					if ( count($parms) > 0 )
    					{
    						$ids_map[$object->getEntityRefName()][$iterator->getId()] = self::duplicate($iterator, $context, $parms);
    					}
					}
					
					break;
					
				case 'pm_ParticipantRole':
					
					$id = CloneLogic::applyToParticipantRole( $context, $attrs, $iterator, $project_it );
					
					if ( $id > 0 )
					{
						$ids_map[$object->getEntityRefName()][$iterator->getId()] = $id;
					}
					else
					{
						$parms = CloneLogic::applyToObject( $context, $attrs, $parms, $iterator, $project_it );
					
    					if ( count($parms) > 0 )
    					{
    						$ids_map[$object->getEntityRefName()][$iterator->getId()] = self::duplicate($iterator, $context, $parms);
    					}
					}
					
					break;

				case 'WikiPage':
					if( $iterator->object instanceof ProjectPage && $iterator->get('ParentPage') == '' ) {
						// special case for KB becuase it has single parent for a project
						$kbrootId = $object->getRootIt()->getId();
						if ( $kbrootId < 1 ) {
							$kbrootId = self::duplicate(
								$iterator,
                                $context,
								CloneLogic::applyToObject($context, $attrs, $parms, $iterator, $project_it)
							);
						}
						$ids_map[$object->getEntityRefName()][$iterator->getId()] = $kbrootId;
					}
					else {
						// duplicate data in the project
						$parms = CloneLogic::applyToObject( $context, $attrs, $parms, $iterator, $project_it );
						if ( count($parms) > 0 ) {
							$ids_map[$object->getEntityRefName()][$iterator->getId()] = self::duplicate( $iterator, $context, $parms );
						}
					}
					break;

				default:
                    if ( array_key_exists($iterator->getId(), $ids_map[$object->getEntityRefName()]) ) {
                        if ( $ids_map[$object->getEntityRefName()][$iterator->getId()] != $iterator->getId() ) {
                            // avoid duplicates are in templates
                            break;
                        }
                    }

                    if ( $context->getRestoreFromTemplate() && ($object instanceof Issue || $object instanceof Increment) ) {
                        if ( getSession()->IsRDD() ) {
                            break;
                        }
                    }

				    if ( count($object->getVpds()) < 1 && !$object instanceof PMCustomAttributeValue ) {
				        // just copy the reference to global object
				        $ids_map[$object->getEntityRefName()][$iterator->getId()] = $iterator->getId();
				    }
				    else {
                        // duplicate data in the project
				    	$parms = CloneLogic::applyToObject( $context, $attrs, $parms, $iterator, $project_it );
    					if ( count($parms) > 0 ) {
    						$ids_map[$object->getEntityRefName()][$iterator->getId()] = self::duplicate( $iterator, $context, $parms );
    					}
				    }
			}
			
		    $context->setIdsMap( $ids_map );

            \ZipSystem::sendResponse();
			$iterator->moveNext();
		}

		// restore broken references skiped first time because of absent dependencies
		$broken = $context->getBrokenReferences();
		
		foreach( $broken as $class_name => $broken_reference )
		{
			foreach( $broken_reference as $object_id => $attribute )
		    {
		    	foreach( $attribute as $ref_name => $value )
    		    {
    			    $object_id = $ids_map[$class_name][$object_id];
    			    if ( $object_id < 1 ) continue;

    			    $duplicated = getFactory()->getObject($class_name);
                    if ( !$duplicated->IsReference($ref_name) ) continue;

    			    $reference = $duplicated->getAttributeObject($ref_name);
    			    $reference_id = $ids_map[$reference->getEntityRefName()][$value];

    		        if ( $reference_id > 0 ) {
        			    $object_it = $duplicated->getExact($object_id);
                        try {
                            getFactory()->modifyEntity($object_it, array($ref_name => $reference_id));
                        }
                        catch( \Exception $e ) {
                            \Logger::getLogger('System')->error($e->getMessage());
                            if ( $context->getRaiseExceptions() ) throw $e;
                        }
        			}
    		    }
		    }
		}
    }
    
    static function getReferenceObject( $iterator, $attr )
    {
        if ( $attr == 'ObjectId' && $iterator->get('ObjectClass') != '' ) {
			$className = getFactory()->getClass($iterator->get('ObjectClass'));
			if ( class_exists($className, false) ) {
				return getFactory()->getObject($className);
			}
        }
		if ( $iterator->object->IsReference($attr) ) {
		    return $iterator->object->getAttributeObject($attr);
		}
    }
    
 	static function applyToObject( & $context, $attrs, & $parms, & $it, & $project_it )
 	{
        $ids_map = $context->getIdsMap();

		foreach ( $attrs as $attr )
		{
			// special case for references
			$reference = CloneLogic::getReferenceObject($it, $attr);
			
			if ( !is_object($reference) ) continue;

			// referenced object was duplicated already
			if ( isset($ids_map[$reference->getEntityRefName()]) && array_key_exists($it->get($attr), $ids_map[$reference->getEntityRefName()]) )
			{
				// then use it instead of the value got from template
				$parms[$attr] = $ids_map[$reference->getEntityRefName()][$it->get($attr)];
			}
			elseif ( $it->get($attr) > 0 )
			{
		        // check the referenced object exists
   			    switch( $reference->getEntityRefName() )
   			    {
   			        case 'pm_Participant':
						if ( $context->getUseExistingReferences() ) {
	   			        	$ref_it = $it->getRef($attr);
	   			            $parms[$attr] =
	   			            	$reference->getRegistry()->Query(
	    			            	array(
                                        new \FilterBaseVpdPredicate(),
	    			            		new \FilterAttributePredicate('SystemUser', $ref_it->get('SystemUser')),
	    			            		new \FilterAttributePredicate('Project', $project_it->getId())
	    			            	)
	    			            )->getId();
						}
						else {
	   			            $parms[$attr] = getSession()->getParticipantIt()->getId();
						}
   			            break;

   			        case 'cms_User':
						if ( !$context->getUseExistingReferences() ) {
	   			            $parms[$attr] = getSession()->getUserIt()->getId();
						}
   			            break;

                    case 'pm_Predicate':
                        $parms[$attr] = $reference->getExact($it->get($attr))->getId();
                        break;

   			        default:
						if ( $context->getUseExistingReferences() ) {
							$parms[$attr] = $reference->getExact($it->get($attr))->getId();
	   			    		if ( $parms[$attr] == '' ) {
							    // use default attribute value if there is no referenced object
							    $parms[$attr] = $it->object->getDefaultAttributeValue( $attr );
							}
						}
						elseif ( $reference->getEntityRefName() != 'entity' && count($reference->getVpds()) > 0 )  {
                            $parms[$attr] = '';
                        }
   			    }
			}

			if ( $it->get($attr) > 0 && $parms[$attr] == '' )
			{
			    $context->addBrokenReference( get_class($it->object), $it->getId(), $attr, $it->get($attr) );
			}
		}

		// use given record id
		if ( $ids_map[$it->object->getEntityRefName()][$it->getId()] > 0 )
		{
			$parms[$it->getIdAttribute()] = $ids_map[$it->object->getEntityRefName()][$it->getId()];
		}

		if ( $context->getReuseProject() ) {
		    if ( $it->get('Project') != '' ) {
                $parms['Project'] = $it->get('Project');
            }
            $parms['VPD'] = $it->get('VPD');
        }
		else {
            $parms['Project'] = $project_it->getId();
        }

		switch ( strtolower(get_class($it->object)) )
		{
			case 'release':
                if ( $project_it->get('Tools') == 'scrumban_ru.xml' ) {
                    $parms['StartDate'] = SystemDateTime::date();
                }
                else {
                    $release_it = getFactory()->getObject('Release')->getRegistry()->Query(
                        array(
                            new SortAttributeClause('StartDate.D'),
                            new FilterBaseVpdPredicate()
                        )
                    );
                    if ( $release_it->count() < 1 ) {
                        $parms['StartDate'] = SystemDateTime::date();
                    }
                    else {
                        $parms['StartDate'] = date('Y-m-j', strtotime('1 day', strtotime($release_it->get('FinishDate'))));
                    }
                }

                $dt1 = new \DateTime($it->get('StartDate'));
                $dt2 = new \DateTime($it->get('FinishDate'));
                $interval = $dt2->diff($dt1);
				$parms['FinishDate'] = date('Y-m-j', strtotime('-1 day', strtotime($interval->days.' day', strtotime( $parms['StartDate']))));
				break;

			case 'iteration':
				$iteration_it = getFactory()->getObject('Iteration')->getRegistry()->Query(
                    array(
                        new SortAttributeClause('StartDate.D'),
                        new FilterBaseVpdPredicate()
                    )
				);
				
				if ( $iteration_it->count() < 1 ) {
					$parms['StartDate'] = SystemDateTime::date();
				}
				else {
					$parms['StartDate'] = date('Y-m-j', strtotime('+1 day', strtotime($iteration_it->get('FinishDate'))));
				}

				$parms['FinishDate'] = date('Y-m-j', strtotime('+7 day', strtotime($parms['StartDate'])));
				break;

			case 'projectrole':
				$parms['ProjectRoleBase'] = getFactory()->getObject('ProjectRoleBase')
                                                ->getByRef('ReferenceName', $it->get('ReferenceName'));
				break;
				
			case 'tasktype':
				if ( in_array($it->get('ReferenceName'), array('support','implementation')) ) {
					$reference_name = 'development';
                    $parms['ReferenceName'] = $reference_name;
                }
				else {
					$reference_name = $it->get('ReferenceName');
				}
			
				$base_it = getFactory()->getObject('TaskTypeBase')->getByRef('ReferenceName', $reference_name);
				if ( $base_it->getId() != '' ) {
					$parms['ParentTaskType'] = $base_it->getId();
				}

				break;

			default:
		}

		switch ( $it->object->getEntityRefName() )
		{
		    case 'pm_Test':
                $parms['Manually'] = 'Y';
                if ( $it->get('Version') != '' && class_exists('Build') ) {
                    $buildIt = getFactory()->getObject('Build')->getByRef('Caption', $it->get('Version'));
                    if ($buildIt->getId() != '') {
                        $parms['Version'] = $buildIt->getId();
                    }
                }
		        break;

            case 'pm_TestExecutionResult':
                if ( $parms['RelatedColor'] == '' ) {
                    switch($it->get('ReferenceName')) {
                        case 'succeeded':
                            $parms['RelatedColor'] = '#5eb95e';
                            break;
                        case 'failed':
                            $parms['RelatedColor'] = '#dd514c';
                            break;
                        case 'blocked':
                            $parms['RelatedColor'] = '#999999';
                            break;
                    }
                }
                break;

            case 'pm_AccessRight':
				if ( $it->get('ReferenceType') == 'PMReport' ) {
					$parms['ReferenceName'] = $ids_map['pm_CustomReport'][$it->get('ReferenceName')];
				}
				break;
				
			case 'WikiPage':
                if ( $context->getResetUids() || $it->get('ReferenceName') == WikiTypeRegistry::KnowledgeBase ) {
                    $parms['UID'] = '';
                }
                else {
                    $parms['UID'] = $it->get('UID');
                }

				if ( $parms['ParentPage'] == '' ) {
					$parms['ParentPath'] = '';
					$parms['SectionNumber'] = '';
					if ( $it->get('IsDocument') == '' && $context->getRestoreFromTemplate() ) {
                        $parms['IsDocument'] = 1;
                    }
				}

				// backward compatibility
				if ( !is_numeric($parms['IsTemplate']) ) {
					$parms['IsTemplate'] = $parms['IsTemplate'] == 'Y' ? 1 : 0;
				}
				if ( !is_numeric($parms['ReferenceName']) ) {
					$parms['ReferenceName'] = getFactory()->getObject('WikiPage')
							->getByRef('ReferenceName', $parms['ReferenceName'])->getId();
				}

				if ( $it->get('DataHash') == '' ) {
				    $data = array_merge($it->getData(), $parms);
				    $parms['DataHash'] = $it->object->createCachedIterator(array($data))->buildDataHash();
                }

                if ( $context->getResetBaseline() ) {
                    $parms['DocumentVersion'] = '';
                }
				break;

            case 'WikiPageType':
                if ( $it->get('IsImplementing') == '' ) $parms['IsImplementing'] = 'Y';
                if ( $it->get('PageReferenceName') == 3 && $it->get('ReferenceName') == 'section' ) {
                    $parms['IsTesting'] = 'N';
                }
                else {
                    if ( $it->get('IsTesting') == '' ) {
                        $parms['IsTesting'] = 'Y';
                    }
                }
                if ( $it->get('ReferenceName') == 'PageType_57f63ce6ae5a7') {
                    $parms['IsNoIdentity'] = 'Y';
                }
                else {
                    $parms['IsNoIdentity'] = 'N';
                }
                break;

            case 'WikiPageTrace':
                $parms['SourceBaseline'] = 'NULL';
                $parms['Baseline'] = 'NULL';
                break;

			case 'pm_Workspace':
				$parms['SystemUser'] = '';
				break;
				
			case 'pm_WorkspaceMenuItem':
				if ( is_numeric($it->get('ReportUID')) ) {
					$parms['ReportUID'] = $ids_map['pm_CustomReport'][$it->get('ReportUID')];
				}
				break;

            case 'pm_DashboardItem':
                if ( is_numeric($it->get('WidgetUID')) ) {
                    $parms['WidgetUID'] = $ids_map['pm_CustomReport'][$it->get('WidgetUID')];
                }
                break;
				
			case 'pm_UserSetting':
				if ( $it->get('Setting') == md5('emailnotification') ) return array();
				
				$it->object->setRegistry(new ObjectRegistrySQL());
				$setting_it = $it->object->getRegistry()->Query(
						array (
								new FilterAttributePredicate('Setting', $it->get('Setting')),
								new SettingGlobalPredicate('dummy'),
								new FilterBaseVpdPredicate()
						)
				); 
				if ( $setting_it->getId() == '' )
				{
					$parms['Participant'] = '-1';
					$parms['Value'] = self::replaceUser($it->get('Value'), $context);
				}
				else
				{
					while( !$setting_it->end() ) {
						$it->object->modify_parms( $setting_it->getId(),
							array (
								'Value' => self::replaceUser($it->get('Value'), $context)
							)
						);
						$setting_it->moveNext();
					}
					return array();
				}
				break;
				
			case 'ObjectChangeLog':
				$parms['ObjectId'] = $ids_map[$it->get('EntityRefName')][$it->get('ObjectId')];
				if ( $parms['ObjectId'] < 1 )
				{
					$parms = array();
				}
				else
				{
					$parms['Content'] = preg_replace_callback('/\&version=([\d]+)/i', 
							function($matches) use ($ids_map) {
									return '&version='.$ids_map['WikiPageChange'][$matches[1]];
							}, $it->get('Content')
						);
					
					$uid = new ObjectUID();
					$parms['Content'] = preg_replace_callback('/\[(([A-Z]{1})-(\d+))\]/i', 
							function($matches) use ($ids_map, $uid) {
									return $matches[2].'-'.$ids_map[$uid->getClassNameByUid($matches[1])][$matches[3]];
							}, $it->get('Content')
						);
				}
				
				break;
				
			case 'pm_Attachment':
			case 'Comment':
                $class_name = getFactory()->getClass($it->get('ObjectClass'));
                if ( !class_exists($class_name) ) return array();

                $anchor_id = $ids_map[getFactory()->getObject($class_name)->getEntityRefName()][$it->get('ObjectId')];
                if ( $anchor_id == '' && $context->getUseExistingReferences() ) {
                    $anchor_id = getFactory()->getObject($class_name)->getExact($it->get('ObjectId'))->getId();
                }
                if ( $anchor_id == '' ) {
                    return array();
                }

                $parms['ObjectId'] = $anchor_id;
                break;

			case 'cms_Snapshot':
				$class_name = getFactory()->getClass($it->get('ObjectClass'));
				if ( !class_exists($class_name) ) return array();

				$anchor_id = $ids_map[getFactory()->getObject($class_name)->getEntityRefName()][$it->get('ObjectId')];
				if ( $anchor_id == '' && $context->getUseExistingReferences() ) {
                    $anchor_id = getFactory()->getObject($class_name)->getExact($it->get('ObjectId'))->getId();
                }
                if ( $anchor_id == '' ) {
                    return array();
                }

				$parms['ObjectId'] = $anchor_id;

                $versions = getFactory()->getObject('cms_Snapshot')->getRegistry()->Count(
                    array (
                        new FilterAttributePredicate('ObjectClass', $class_name),
                        new FilterAttributePredicate('ObjectId', $anchor_id),
                        new FilterAttributeNullPredicate('Type')
                    )
                );
                $parms['RecordCreated'] = $parms['RecordModified'] =
                    date('Y-m-j', strtotime('-'.(abs(3-$versions)*2).' week', strtotime(SystemDateTime::date())));

                if ( $it->get('Stage') != '' ) {
                    $releaseId = $ids_map['pm_Version'][intval(substr($it->get('Stage'), 0, 8))];
                    $iterationId = $ids_map['pm_Release'][intval(substr($it->get('Stage'), 8))];
                    $parms['Stage'] = str_pad($releaseId, 8, '0', STR_PAD_LEFT).
                        str_pad($iterationId, 8, '0', STR_PAD_LEFT);
                }
				break;

            case 'pm_ChangeRequest':
                if ( $it->get('SubmittedVersion') != '' && class_exists('Build') ) {
                    $buildIt = getFactory()->getObject('Build')->getByRef('Caption', $it->get('SubmittedVersion'));
                    if ( $buildIt->getId() != '' ) {
                        $parms['SubmittedVersion'] = $buildIt->getId();
                    }
                }
                if ( $it->get('ClosedInVersion') != '' && class_exists('Build') ) {
                    $buildIt = getFactory()->getObject('Build')->getByRef('Caption', $it->get('ClosedInVersion'));
                    if ($buildIt->getId() != '') {
                        $parms['ClosedInVersion'] = $buildIt->getId();
                    }
                }
                if ( $context->getRestoreFromTemplate() ) {
                    $parms['Customer'] = '';
                    $parms['Author'] = '';
                }

            case 'pm_Task':
                $parms['UID'] = '';
                $parms['StartDate'] = '';
                $parms['DueWeeks'] = '';

                if ( $it->get('FinishDate') != '' && $context->getRestoreFromTemplate() ) {
                    $parms['FinishDate'] = SystemDateTime::date();
                }

				if ( $context->getResetAssignments() ) {
					$parms['Assignee'] = '';
					$parms['Owner'] = '';
				}
                break;

            case 'pm_ChangeRequestTrace':
                $request = getFactory()->getObject('Request');
                $requestId = $ids_map[$request->getEntityRefName()][$it->get('ChangeRequest')];
                $requestIt = $request->getExact($requestId);
                $description = $requestIt->getHtmlDecoded('Description');

                $result = preg_replace_callback(REGEX_INCLUDE_REVISION,
                    function($match) use($ids_map) {
                        $revisions = preg_split('/-/', $match[2]);
                        foreach($revisions as $key => $revision ) {
                            $revisions[$key] = $ids_map['WikiPageChange'][$revision];
                        }
                        $parts = preg_split('/-/', $match[1]);
                        $parts[1] = $ids_map['WikiPage'][$parts[1]];
                        return '{{'.join('-',$parts).':'.join('-', $revisions).'}}';
                    },
                    $description
                );
                if ( $description != $result ) {
                    $requestIt->object->setNotificationEnabled(false);
                    $requestIt->object->getRegistry()->Store($requestIt,
                        array(
                            'Description' => $result
                        )
                    );
                }
                break;

            case 'pm_Milestone':
                $parms['MilestoneDate'] = date('Y-m-j', strtotime('7 day', strtotime(SystemDateTime::date())));
                break;

            case 'pm_State':
                if ( $it->get('ObjectClass') == 'task' && $it->get('ReferenceName') == 'inprogress' ) {
                    $parms['IsTerminal'] = 'I';
                }
                if ( $it->get('ObjectClass') == 'request' && in_array($it->get('ReferenceName'),array('planned','inprogress')) ) {
                    $parms['IsTerminal'] = 'I';
                }
                if ( !in_array($project_it->get('Tools'),array('ticket_en.xml','ticket_ru.xml')) && in_array($it->get('IsTerminal'),array('I','N')) ) {
                    $parms['SkipEmailNotification'] = 'Y';
                }
                if ( $it->get('IsNewArtifacts') == '' ) {
                    $parms['IsNewArtifacts'] = 'Y';
                }
                break;

            case 'pm_IssueType':
                if ( $it->get('Option1') == '' ) {
                    $parms['Option1'] = 'Y';
                }
                break;

            case 'pm_AttributeValue':
                $attributeId = $ids_map['pm_CustomAttribute'][$it->get('CustomAttribute')];
                if ( $attributeId == '' ) $attributeId = $it->get('CustomAttribute');
                $attribute_it = getFactory()->getObject('pm_CustomAttribute')->getExact($attributeId);
                if ( $attribute_it->getId() == '' ) return array();

                if ( !$context->getUseExistingReferences() ) {
                    $refName = getFactory()->getClass($attribute_it->get('EntityReferenceName'));
                    if ( !class_exists($refName) ) return array();

                    $entityRefName = getFactory()->getObject($refName)->getEntityRefName();
                    $object_id = $ids_map[$entityRefName][$it->get('ObjectId')];

                    if ( $object_id != '' ) {
                        $foundValues = $it->object->getRegistry()->Count(
                            array(
                                new FilterAttributePredicate('CustomAttribute', $attributeId),
                                new FilterAttributePredicate('ObjectId', $object_id),
                            )
                        );
                        if ( $foundValues > 0 ) return array();
                    }
                    $parms['ObjectId'] = $object_id;
                }
                break;

            case 'pm_AutoAction':
                $actions = JsonWrapper::decode($it->getHtmlDecoded('Actions'));
                $parms['Actions'] = '';

                $request = getFactory()->getObject('Request');
                foreach( $actions as $key => $value ) {
                    if ( !$request->IsReference($key) ) continue;
                    $attributeObject = $request->getAttributeObject($key);
                    $remappedValue = $ids_map[$attributeObject->getEntityRefName()][$value];
                    if ( $remappedValue != '' ) {
                        $actions[$key] = $remappedValue;
                    }
                }

                $task = getFactory()->getObject('Task');
                foreach( $actions as $key => $value ) {
                    $taskAttribute = array_pop(preg_split('/Task_/i', $key));
                    if ( !$task->IsReference($taskAttribute) ) continue;
                    $attributeObject = $task->getAttributeObject($taskAttribute);
                    $remappedValue = $ids_map[$attributeObject->getEntityRefName()][$value];
                    if ( $remappedValue != '' ) {
                        $actions[$key] = $remappedValue;
                    }
                }

                $parms['Actions'] = JsonWrapper::encode($actions);
                break;

            case 'pm_StateObject':
                $stateIt = $it->getRef('State');
                if ( $stateIt->getId() == '' ) {
                    $stateIt = getFactory()->getObject('pm_State')->getRegistry()->Query(
                        array(
                            new FilterVpdPredicate(),
                            new FilterAttributePredicate('ObjectClass', $parms['ObjectClass']),
                            new SortOrderedClause()
                        )
                    );
                    $parms['State'] = $stateIt->getId();
                }
                break;

            case 'pm_Activity':
                if ( $context->getResetDates() ) {
                    $parms['ReportDate'] = SystemDateTime::date();
                }
                break;

            case 'pm_Release':
                if ( $it->get('ReleaseNumber') != '' ) {
                    $parms['Caption'] = $it->get('ReleaseNumber');
                }
            case 'pm_Version':
            case 'pm_Build':
                if ( $it->get('IsClosed') == '' ) {
                    $parms['IsClosed'] = 'N';
                }
        }

		if ( $it->object instanceof MetaobjectStatable ) {
            if ( $context->getResetState() ) {
                $parms['StateObject'] = '';
                $parms['LifecycleDuration'] = '';
            }
		}

		return $parms;
 	}

 	static function applyToProject( & $context, & $attrs, & $it, & $project_it )
 	{
        $parms = array();
		
		foreach ( $attrs as $attr )
		{
			switch ( $attr )
			{
				case 'Caption':
				case 'CodeName':
				case 'StartDate':
				case 'FinishDate':
				case 'Blog':
				case 'IsTender':
				case 'IsClosed':
                case 'Tools':
				    break;
				
				default:
					$parms[$attr] = $it->get_native($attr);
					if ( $parms[$attr] == '') {
						switch( $attr ) {
							case 'WikiEditorClass':
								$parms[$attr] = 'WikiRtfCKEditor';
								break;
							case 'DaysInWeek':
								$parms[$attr] = '5';
								break;		
							case 'Importance':
								$parms[$attr] = '3';
								break;
                            case 'KnowledgeBaseUseProducts':
                                $parms[$attr] = 'Y';
                                break;
						}
					}
			}
		}
		
		// remember obsolete attributes values
		foreach( array('IsSubversionUsed', 'IsSupportUsed', 'IsFileServer', 'IsKnowledgeUsed') as $attribute )
		{
			if ( $it->get($attribute) != '' ) {
				$context->setDefaultParms(
						array_merge( 
								$context->getDefaultParms(),
								array ( $attribute => $it->get($attribute) )
						)
				);
			}
		}

		$project_it->object->modify_parms($project_it->getId(), $parms);
		$project_it->invalidateCache();
		getSession()->setProjectIt($project_it);
 	}

 	static function applyToLegacy( & $context, $attributes, & $attrs, & $it, & $project_it )
 	{
        try {
            $query = array(
                new FilterBaseVpdPredicate()
            );
            foreach ($attributes as $attribute) {
                if ($it->get_native($attribute) == '') continue;
                if (in_array($it->object->getAttributeType($attribute), array('varchar', 'text'))) {
                    $query[] = new FilterTextExactPredicate($attribute, $it->getHtmlDecoded($attribute));
                    continue;
                }
                $query[] = new FilterAttributePredicate($attribute, $it->get_native($attribute));
            }
            $object_it = $it->object->getRegistry()->Query($query);

            if ($object_it->getId() != '' && !$context->getUseExistingReferences()) {
                $parms = array();
                foreach ($attrs as $attr) {
                    if ($parms[$attr] == '') $parms[$attr] = $it->get_native($attr);
                    if ($attr == 'Project') unset($parms[$attr]);
                    if ($attr == 'VPD') unset($parms[$attr]);
                }
                if (count($parms) > 0 && getFactory()->getAccessPolicy()->can_modify($object_it)) {
                    getFactory()->modifyEntity($object_it, $parms);
                }
            }
            return $object_it->count() < 1 ? 0 : $object_it->getId();
        }
        catch( \Exception $e ) {
            \Logger::getLogger('System')->error($e->getMessage());
            if ( $context->getRaiseExceptions() ) throw $e;
            return 0;
        }
 	}

	static function applyToCustomReport( & $context, & $parms, & $it, & $project_it )
	{
        $parms['IsPublic'] = 'Y';
        $parms['Url'] = self::replaceUser($it->getHtmlDecoded('Url'), $context);

        $id = CloneLogic::applyToLegacy( $context, array('Url'), $attrs, $it, $project_it );
        if ( $id < 1 ) {
            $parms = self::applyToObject( $context, $attrs, $parms, $it, $project_it );
            if ( count($parms) > 0 ) {
                return self::duplicate($it, $context, $parms);
            }
        }
        else {
            $it->object->modify_parms($id, $parms);
            return $id;
        }
	}

 	static function applyToMethodology( & $context, & $attrs, & $it, & $project_it )
 	{
		$parms = array();
		$licensed_attrs = array('IsRequirements','IsTests','IsHelps');
		
		foreach ( $attrs as $attr )
		{
			if ( $attr == 'RequestEstimationRequired' )
			{
				if ( $it->get_native($attr) == 'Y' || $it->get_native($attr) == 'N' )
				{
					$parms[$attr] = $it->get_native($attr) == 'Y' 
						? strtolower('EstimationStoryPointsStrategy') : strtolower('EstimationNoneStrategy');
					continue;
				}
			}
			
			if ( $attr == 'TaskEstimationUsed' && $it->get_native($attr) == '' )
			{
				$parms[$attr] = 'Y';
				continue;
			}
			
			$parms[$attr] = $it->get_native($attr);
		}

		$parms['Project'] = $project_it->getId();
		
		// get values for obsolete attributes
		foreach( array('IsSubversionUsed', 'IsSupportUsed', 'IsFileServer', 'IsKnowledgeUsed') as $attribute )
		{
			if ( $it->get($attribute) == '' ) {
				$defaults = $context->getDefaultParms();
				if ( $defaults[$attribute] != '' ) {
					$parms[$attribute] = $defaults[$attribute]; 
				}
			}
		}

		if ( $parms['MetricsType'] == '' ) $parms['MetricsType'] = 'A';

		$methodology_it = $project_it->getMethodologyIt();
        $methodology_it->object->modify_parms($methodology_it->getId(), $parms);

		getSession()->getProjectIt()->setMethodologyIt(
            $methodology_it->object->getExact($methodology_it->getId())
        );
 	}

	static function duplicate( $iterator, $context, $parms )
	{
		$attributes =
            array_merge(
                array_keys( $iterator->object->getAttributes() ),
                array(
                    'UID'
                )
            );

		if ( $context->getResetDates() ) {
            $attributes = array_diff($attributes, array( 'RecordCreated', 'RecordModified' ));
        }

		$id_attribute = $iterator->getIdAttribute();
		if ( $parms[$id_attribute] > 0 ) {
			// special case for moving objects, use the same record ID
			$temp_it = $iterator->object->getExact($parms[$id_attribute]);
			if ( $temp_it->getId() > 0 ) return $temp_it->getId();
			$attributes[] = $id_attribute;
		}

		$values = $parms;
		foreach ( $attributes as $attribute ) {
			if ( !array_key_exists( $attribute, $parms) ) {
				$values[$attribute] = $iterator->getHtmlDecoded($attribute);
			}
		}

		if ( $iterator->object instanceof Request ) {
            $iterator = $iterator->object->createCachedIterator(array($values))->getSpecifiedIt();
        }

		try {
            $parms = array();
            foreach( $iterator->object->getAttributesByGroup('alternative-key') as $attribute ) {
                if ( $values[$attribute] == '' ) {
                    $parms = array();
                    break;
                }
                $parms[] = new FilterAttributePredicate($attribute, $values[$attribute]);
            }
            if ( count($parms) > 0 ) {
                $parms[] = new FilterBaseVpdPredicate();
                $foundIt = $iterator->object->getRegistry()->Query($parms);
                if ( $foundIt->getId() != '' ) {
                    getFactory()->modifyEntity($foundIt, $values);
                    return $foundIt->getId();
                }
            }

            $createdIt = getFactory()->createEntity($iterator->object, $values);
            return $createdIt->getId();
        }
		catch( \Exception $e ) {
            \Logger::getLogger('System')->error($e->getMessage());
            if ( $context->getRaiseExceptions() ) throw $e;
            return 0;
        }
	}
	
 	static function applyToParticipantRole( & $context, & $attrs, & $it, & $project_it )
 	{
 		$ids_map = $context->getIdsMap();
 	
 		$object_it = $it->object->getByRefArray( array ( 
 				'ProjectRole' => $ids_map['pm_ProjectRole'][$it->get('ProjectRole')],
 				'Participant' => $ids_map['pm_Participant'][$it->get('Participant')]
 		));
 		
 		if ( $object_it->count() < 1 ) return 0;
 		
 		if ( $context->getUseExistingReferences() ) return $object_it->getId();
 		
		$parms = array();
		
		$parms = CloneLogic::applyToObject( $context, $attrs, $parms, $it, $project_it );
			
		foreach ( $attrs as $attr )
		{
			if ( $parms[$attr] == '' )
			{
				$parms[$attr] = $it->get_native($attr);
			}
		}
			
		$it->object->modify_parms($object_it->getId(), $parms);
		
		return $object_it->getId();
 	}
 	
 	static function replaceUser( $value, $context )
 	{
        $ids_map = $context->getIdsMap();
        $value = preg_replace_callback('/release=([\d]+)/i', function($match) use($ids_map) {
            $releaseIt = getFactory()->getObject('Release')->getExact($ids_map['pm_Version'][$match[1]]);
            return $releaseIt->getId() != '' ? 'release='.$releaseIt->getId() : 'release=all';
        }, $value);
		return $value;
 	}

 	static function postProcess( $context )
    {
        $idsMap = $context->getIdsMap();
        $uid = new ObjectUID;

        foreach( $idsMap as $className => $idMap )
        {
            $object = getFactory()->getObject($className);
            $wysiwygAttributes = $object->getAttributesByType('wysiwyg');
            if ( count($wysiwygAttributes) > 0 )
            {
                foreach( $idMap as $wasId => $newId ) {
                    $data = $object->getExact($newId)->getData();
                    $dataToUpdate = array();
                    $updateData = false;

                    foreach( $wysiwygAttributes as $attribute ) {
                        $dataToUpdate[$attribute] = preg_replace_callback(
                            REGEX_INCLUDE_PAGE,
                            function($match) use ($idsMap, $uid, &$updateData) {
                                $objectIt = $uid->getObjectIt($match[1]);
                                list($classLeter, $includedId) = explode('-', $match[1]);
                                $includedId = $idsMap[$objectIt->object->getEntityRefName()][$includedId];
                                if ( $includedId != '' ) {
                                    $updateData = true;
                                    return '{{'.$classLeter.'-'.$includedId.'}}';
                                }
                                return $match[0];
                            },
                            html_entity_decode($data[$attribute])
                        );
                    }

                    if ( $updateData ) {
                        try {
                            getFactory()->modifyEntity($object->getExact($newId), $dataToUpdate);
                        }
                        catch( \Exception $e ) {
                            \Logger::getLogger('System')->error($e->getMessage());
                            if ( $context->getRaiseExceptions() ) throw $e;
                        }
                    }
                }
            }
        }
    }
}