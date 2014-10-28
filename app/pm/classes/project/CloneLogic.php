<?php

include_once "CloneContext.php";

class CloneLogic
{
    static function Run( & $context, $object, $iterator, $project_it )
    {
        global $model_factory;
        
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
				// special case of template importing
				case 'pm_Project':
					
				    CloneLogic::applyToProject( $context, $attrs, $iterator, $project_it );
					
				    break;

				// special case of template importing
				case 'pm_Methodology':
					
				    CloneLogic::applyToMethodology( $context, $attrs, $iterator, $project_it );
					
				    break;
					
				// special case of template importing				
				case 'pm_VersionSettings':
					
				    CloneLogic::applyToVersionSettings( $context, $attrs, $iterator, $project_it );
					
				    break;
					
				// special case of template importing				
				case 'pm_State':
					
				    $id = CloneLogic::applyToState( $context, $attrs, $iterator, $project_it );
					
					if ( $id > 0 )
					{
						$ids_map[$object->getEntityRefName()][$iterator->getId()] = $id;
					}
					else
					{
						$parms = CloneLogic::applyToObject( $context, $attrs, $parms, $iterator, $project_it );
						
						if ( count($parms) > 0 )
						{
							$ids_map[$object->getEntityRefName()][$iterator->getId()] = self::duplicate( $iterator, $parms );
						}
					}
					
					break;

				case 'pm_Version':
					
				    $parms = CloneLogic::applyToObject( $context, $attrs, $parms, $iterator, $project_it );
					
					if ( count($parms) > 0 )
					{
						$ids_map[$object->getEntityRefName()][$iterator->getId()] = self::duplicate( $iterator, $parms );
					}
					else
					{
						$ref_it = $object->getFirst();
						
						$ids_map[$object->getEntityRefName()][$iterator->getId()] = $ref_it->getId();  
					}
					
					break;
					
				// special case of template importing				
				case 'pm_TaskType':
				case 'WikiPageType':
				case 'pm_IssueType':
				case 'pm_Environment':
				case 'pm_TestExecutionResult':
				case 'pm_CustomAttribute':
				case 'pm_ProjectRole':
					
					$id = CloneLogic::applyToLegacy( $context, 'ReferenceName', $attrs, $iterator, $project_it );
					
					if ( $id > 0 )
					{
						$ids_map[$object->getEntityRefName()][$iterator->getId()] = $id;
					}
					elseif ( !$context->getUseExistingReferences() )
					{
						$parms = CloneLogic::applyToObject( $context, $attrs, $parms, $iterator, $project_it );
						
    					if ( count($parms) > 0 )
    					{
    						$ids_map[$object->getEntityRefName()][$iterator->getId()] = self::duplicate( $iterator, $parms );
    					}
					}
					else
					{
						unset($ids_map[$object->getEntityRefName()][$iterator->getId()]);
					}
					
					break;
					
				case 'pm_Participant':
					
					$id = CloneLogic::applyToLegacy( $context, 'SystemUser', $attrs, $iterator, $project_it );
					
					if ( $id > 0 )
					{
						$ids_map[$object->getEntityRefName()][$iterator->getId()] = $id;
					}
					else
					{
						$parms = CloneLogic::applyToObject( $context, $attrs, $parms, $iterator, $project_it );
					
    					if ( count($parms) > 0 )
    					{
    						$ids_map[$object->getEntityRefName()][$iterator->getId()] = $iterator->duplicate( $parms );
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
    						$ids_map[$object->getEntityRefName()][$iterator->getId()] = $iterator->duplicate( $parms );
    					}
					}
					
					break;
					
				default:
				    
				    if ( count($object->getVpds()) < 1 )
				    {
				        // just copy the reference to global object
				        $ids_map[$object->getEntityRefName()][$iterator->getId()] = $iterator->getId();
				    }
				    else
				    {
				        // duplicate data in the project
				    	$parms = CloneLogic::applyToObject( $context, $attrs, $parms, $iterator, $project_it );
					
    					if ( count($parms) > 0 )
    					{
    						$ids_map[$object->getEntityRefName()][$iterator->getId()] = self::duplicate( $iterator, $parms );
    					}
				    }
			}
			
		    $context->setIdsMap( $ids_map );
		    
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
    		        
    			    $duplicated = $model_factory->getObject($class_name);

    			    $reference = $duplicated->getAttributeObject($ref_name);
    			    
    			    $reference_id = $ids_map[$reference->getEntityRefName()][$value];

    		        if ( $reference_id > 0 )
        			{
        			    $object_it = $duplicated->getExact($object_id); 
        			    
        			    $object_it->modify( array($ref_name => $reference_id) );    
        			}
    		    }
		    }
		}
    }
    
    static function getReferenceObject( $iterator, $attr )
    {
        global $model_factory;
        
        if ( $attr == 'ObjectId' && $iterator->get('ObjectClass') != '' )
        {
            return $model_factory->getObject($iterator->get('ObjectClass'));
        }
        
		if ( $iterator->object->IsReference($attr) )
		{
		    return $iterator->object->getAttributeObject($attr);
		}
    }
    
 	static function applyToObject( & $context, $attrs, & $parms, & $it, & $project_it )
 	{
 		global $model_factory;

        $ids_map = $context->getIdsMap();
 		
		foreach ( $attrs as $attr )
		{
			// special case for references
			$reference = CloneLogic::getReferenceObject($it, $attr);
			
			if ( !is_object($reference) ) continue;

			$reference->addFilter( new \FilterBaseVpdPredicate() );
			
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
    			            
						if ( $context->getUseExistingReferences() )
						{
	   			        	$ref_it = $it->getRef($attr);
	    			            
	   			            $parms[$attr] = 
	   			            	$reference->getRegistry()->Query(
	    			            	array( 
	    			            		new FilterAttributePredicate('SystemUser', $ref_it->get('SystemUser')),
	    			            		new FilterAttributePredicate('Project', $project_it->getId())
	    			            	)
	    			            )->getId();
						}
						else
						{
	   			            $parms[$attr] = getSession()->getParticipantIt()->getId();
						}

   			            break;

   			        case 'cms_User':
    			            
						if ( !$context->getUseExistingReferences() )
						{
	   			            $parms[$attr] = getSession()->getUserIt()->getId();
						}

   			            break;
   			            
   			        default:

						if ( $context->getUseExistingReferences() )
						{
							$parms[$attr] = $reference->getExact($it->get($attr))->getId();
							
	   			    		if ( $parms[$attr] == '' )
							{
							    // use default attribute value if there is no referenced object
							    $parms[$attr] = $it->object->getDefaultAttributeValue( $attr );
							}
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
		
		$parms['Project'] = $project_it->getId();
		$parms['RecordCreated'] = '';
		$parms['RecordModified'] = '';
		
		switch ( strtolower(get_class($it->object)) )
		{
			case 'release':
				
				$release = getFactory()->getObject('Release');
				
				$release_it = $release->getByRef('Project', $project_it->getId());
				
				if ( $release_it->count() < 1 )
				{
					$parms['StartDate'] = SystemDateTime::date();
					
					$parms['FinishDate'] = $release->getDefaultFinishDate(
							SystemDateTime::date(), 
							date('Y-m-d H:i:s', strtotime('-1 day', strtotime('1 month', strtotime(SystemDateTime::date()))))
	   			    );
				}
				else
				{
					$parms['StartDate'] = $release->getDefaultAttributeValue('StartDate');
					
					$parms['FinishDate'] = $release->getDefaultFinishDate($parms['StartDate']);
				}

				break;

			case 'iteration':
				$iteration = $model_factory->getObject('Iteration');
				$iteration_it = $iteration->getAll();
				
				if ( $iteration_it->count() > 0 )
				{
					$parms = array();
					return;
				}

				if ( $context->getIterationStart() == 'NOW()' )
				{
					$parms['StartDate'] = $context->getIterationStart(); 
					$start = $parms['StartDate'];
				}
				else
				{
					$parms['StartDate'] = $context->getIterationStart(); 
					$start = "'".$context->getIterationStart()."'";
				}

				$methodology_it = $project_it->getMethodologyIt();

				$duration_days = $methodology_it->get('ReleaseDuration') * 7;
				if ( $duration_days < 1 ) $duration_days = 28;
				
				$sql = " SELECT FROM_DAYS(TO_DAYS(".$start.") + ".$duration_days.") FinishDate ";
				$date_it = $project_it->object->createSQLIterator( $sql );
				
				$context->setIterationStart($date_it->get('FinishDate'));
				 
				$parms['FinishDate'] = $date_it->get_native('FinishDate');
				$parms['InitialVelocity'] = '0';

				break;

			case 'projectrole':
				$base = $model_factory->getObject('ProjectRoleBase');
				$base_it = $base->getByRef('ReferenceName', $it->get('ReferenceName'));
				
				$parms['ProjectRoleBase'] = $base_it->getId();
				
				break;
				
			case 'tasktype':
				if ( $it->get('ReferenceName') == 'support' )
				{
					$reference_name = 'development';
					$parms['ReferenceName'] = 'support';
				}
				else
				{
					$reference_name = $it->get('ReferenceName');
				}
			
				$base = $model_factory->getObject('TaskTypeBase');
				$base_it = $base->getByRef('ReferenceName', $reference_name);
				
				$parms['ParentTaskType'] = $base_it->getId();
				
				break;

			case 'attachment':
			case 'comment':
				
				$class_name = getFactory()->getClass($it->get('ObjectClass'));
				
				if ( !class_exists($class_name) ) return array();

				$anchor_id = $ids_map[getFactory()->getObject($class_name)->getEntityRefName()][$it->get('ObjectId')];
				
				if ( $anchor_id == '' ) return array();
				
				$parms['ObjectId'] = $anchor_id; 
				
			default:
		}
		
		switch ( $it->object->getEntityRefName() )
		{
			case 'pm_AccessRight':
				if ( $it->get('ReferenceType') == 'PMReport' )
				{
					$parms['ReferenceName'] = $ids_map['pm_CustomReport'][$it->get('ReferenceName')];
				}
				break;
				
			case 'WikiPage':
				
				$parms['DocumentVersion'] = '';
				
				if ( $parms['ParentPage'] == '' )
				{
					$parms['ParentPath'] = '';
					$parms['SectionNumber'] = '';
				}
				
				if ( !is_numeric($parms['IsTemplate']) )
				{
					$parms['IsTemplate'] = $parms['IsTemplate'] == 'Y' ? 1 : 0;
				}
				
				if ( !is_numeric($parms['ReferenceName']) )
				{
					$parms['ReferenceName'] = getFactory()->getObject('WikiPage')
							->getByRef('ReferenceName', $parms['ReferenceName'])->getId();
				}
				
				$project_it = getSession()->getProjectIt();
				
				$parms['Content'] = preg_replace_callback('/file\/([^\/]+)\/([^\/]+)\/([\d]+)/i', 
						function($matches) use ($ids_map, $project_it)
						{
								$file_id = $ids_map[$matches[1]][$matches[3]];
								return $file_id != ''
										? 'file/'.$matches[1].'/'.$project_it->get('CodeName').'/'.$file_id
										: $matches[0];
						}, $it->getHtmlDecoded('Content')
					);

				break;
			
			case 'pm_Workspace':
				
				$parms['SystemUser'] = '';
				
				break;
				
			case 'pm_WorkspaceMenuItem':
				
				if ( is_numeric($it->get('ReportUID')) )
				{
					$parms['ReportUID'] = $ids_map['pm_CustomReport'][$it->get('ReportUID')];
				}
				
				break;
				
			case 'pm_UserSetting':
				
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
					$parms['Value'] = self::replaceUser($it->get('Value'));
				}
				else
				{
					$setting_it->modify(
							array (
									'Value' => self::replaceUser($it->get('Value'))
							)
					);
									
					$parms = array();
				}
				
				break;
				
			case 'pm_CustomReport':
				
				// each participant should have his own mytasks/mysissues report
				if ( in_array($it->get('ReportBase'), array('mytasks', 'myissues')) )
				{
					$parms = array();
				}
				else
				{
					$parms['Author'] = -1;
					$parms['Url'] = self::replaceUser($it->get('Url'));
				}

				break;
				
			case 'ObjectChangeLog':

				$parms['ObjectId'] = $ids_map[$it->get('EntityRefName')][$it->get('ObjectId')];
				
				//$parms['RecordCreated'] = $it->get('RecordCreated');
				//$parms['RecordModified'] = $it->get('RecordModified');
				
				if ( $parms['ObjectId'] < 1 )
				{
					$parms = array();
				}
				else
				{
					$parms['Content'] = preg_replace_callback('/\&version=([\d]+)/i', 
							function($matches) use ($ids_map) {
									return '&version='.$ids_map['WikiPageChange'][$matches[1]];
							}, $it->getHtmlDecoded('Content')
						);
				}
				
				break;
		}
		
		if ( $context->getResetState() && is_a($it->object, 'MetaobjectStatable') )
		{
		    if ( !in_array($parms['State'], $it->object->getStates()) )
		    { 
		        $parms['State'] = array_shift($it->object->getNonTerminalStates());
		    }
		    else
		    {
		    	unset($parms['State']);
		    }
		    
		    $parms['StateObject'] = '';
		    $parms['LifecycleDuration'] = '';
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
				case 'MainWikiPage':
				case 'Blog':
				case 'IsTender':
				case 'IsClosed':
				    break;
				
				default:
					$parms[$attr] = $it->get_native($attr);
					
					if ( $parms[$attr] == '' && $attr == 'WikiEditorClass' )
					{
						$parms[$attr] = 'WikiSyntaxEditor';
					}
					if ( $parms[$attr] == '' && $attr == 'DaysInWeek' )
					{
						$parms[$attr] = '5';
					}
			}
		}

		$project_it->modify( $parms );
		
		$project_it->invalidateCache();
		
		getSession()->setProjectIt($project_it);
 	}

 	static function applyToState( & $context, & $attrs, & $it, & $project_it )
 	{
 		global $model_factory;

 		$object_it = $it->object->getByRefArray(
 			array ( 'ObjectClass' => $it->get_native('ObjectClass'),
 					'ReferenceName' => $it->get_native('ReferenceName') )
 			);
 		
 		if ( $object_it->count() < 1 )
 		{
 			return 0;
 		}
 		
		$parms = array();
		
		$parms = CloneLogic::applyToObject( $context, $attrs, $parms, $it, $project_it );

		foreach ( $attrs as $attr )
		{
			if ( $parms[$attr] == '' )
			{
				$parms[$attr] = $it->get_native($attr);
			}
		}

		$object_it->modify( $parms );
		
		return $object_it->getId();
 	}

 	static function applyToLegacy( & $context, $check_attribute, & $attrs, & $it, & $project_it )
 	{
 		$object_it = $it->object->getRegistry()->Query(
 				array (
 						new FilterAttributePredicate($check_attribute, $it->get_native($check_attribute)),
 						new FilterBaseVpdPredicate()
 				)
 		);
 		
 		if ( $object_it->getId() != '' )
 		{
			foreach ( $attrs as $attr )
			{
				if ( $parms[$attr] == '' ) $parms[$attr] = $it->get_native($attr);
			}
	
			$object_it->modify( $parms );
 		}
 		
 		return $object_it->count() < 1 ? 0 : $object_it->getId();
 	}
 	
 	static function applyToMethodology( & $context, & $attrs, & $it, & $project_it )
 	{
		$parms = array();
		
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
		
		$methodology_it = $project_it->getMethodologyIt(); 		
						
		$methodology_it->modify( $parms );
		
		getSession()->getProjectIt()->invalidateCache();
 	}

 	static function applyToVersionSettings( & $context, & $attrs, & $it, & $project_it )
 	{
 		global $model_factory;
 		
		$parms = array();
		
		foreach ( $attrs as $attr )
		{
			$parms[$attr] = $it->get_native($attr);
		}

		$parms['Project'] = $project_it->getId();
		
		$settings = $model_factory->getObject('pm_VersionSettings');

		$settings_it = $settings->getByRef('Project', $project_it->getId());
		
		$settings_it->modify( $parms );
 	}

	static function duplicate( $iterator, $parms ) 
	{
		$attributes = array_keys( $iterator->object->getAttributes() );

		$id_attribute = $iterator->getIdAttribute();
		
		if ( $parms[$id_attribute] > 0 )
		{
			// special case for moving objects, use the same record ID
			$temp_it = $iterator->object->getExact($parms[$id_attribute]);
			
			if ( $temp_it->getId() == '' ) $attributes[] = $id_attribute;
		}  
		
		$values = array();

		foreach ( $attributes as $attribute )
		{
			if ( array_key_exists( $attribute, $parms) )
			{
				$values[$attribute] = $parms[$attribute];
			}
			else
			{
				$values[$attribute] = $iterator->get_native($attribute);
			}
		}
		
		return $iterator->object->add_parms( $values );
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
			
		$object_it->modify( $parms );
		
		return $object_it->getId();
 	}
 	
 	static function replaceUser( $value )
 	{
 		$user_it = getSession()->getUserIt();
 		
 		$value = preg_replace('/taskassignee=[^;&]+/i', 'taskassignee='.$user_it->getId(), $value);
				
		$value = preg_replace('/owner=[^;&]+/i', 'owner='.$user_it->getId(), $value);
		
		return $value;
 	}
}