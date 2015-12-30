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
				case 'pm_CustomReport':
					$ids_map[$object->getEntityRefName()][$iterator->getId()] = self::applyToCustomReport( $context, $attrs, $iterator, $project_it );
					break;

				// special case of template importing
				case 'pm_Project':

				    CloneLogic::applyToProject( $context, $attrs, $iterator, $project_it );

				    break;

				// special case of template importing
				case 'pm_Methodology':
					
				    CloneLogic::applyToMethodology( $context, $attrs, $iterator, $project_it );
					
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
				case 'pm_TestExecutionResult':
				case 'pm_CustomAttribute':
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

				// special case of template importing				
				case 'pm_ProjectRole':
					$id = CloneLogic::applyToLegacy( $context, 'Caption', $attrs, $iterator, $project_it );
					
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
    						$ids_map[$object->getEntityRefName()][$iterator->getId()] = self::duplicate($iterator, $parms);
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
    						$ids_map[$object->getEntityRefName()][$iterator->getId()] = self::duplicate($iterator, $parms);
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
        			    
        			    $duplicated->modify_parms($object_it->getId(), array($ref_name => $reference_id));    
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
		
		if ( $context->getResetDates() )
		{
			$parms['RecordCreated'] = '';
			$parms['RecordModified'] = '';
		}
		
		switch ( strtolower(get_class($it->object)) )
		{
			case 'release':
				$release_it = getFactory()->getObject('Release')->getRegistry()->Query(
						array(
								new SortAttributeClause('StartDate.D'),
								new FilterBaseVpdPredicate()
						)
				);
				
				if ( $release_it->count() < 1 )
				{
					$parms['StartDate'] = SystemDateTime::date();
				}
				else
				{
					$parms['StartDate'] = date('Y-m-j', strtotime('1 day', strtotime($release_it->get('FinishDate'))));
				}
				$parms['FinishDate'] = date('Y-m-j', strtotime('-1 day', strtotime('2 month', strtotime( $parms['StartDate'])))); 

				break;

			case 'iteration':
				$iteration_it = getFactory()->getObject('Iteration')->getRegistry()->Query(
						array(
								new SortAttributeClause('StartDate.D'),
								new FilterBaseVpdPredicate()
						)
				);
				
				if ( $iteration_it->count() < 1 )
				{
					$parms['StartDate'] = SystemDateTime::date();
				}
				else
				{
					$parms['StartDate'] = date('Y-m-j', strtotime('1 day', strtotime($iteration_it->get('FinishDate')))); 
				}

				$parms['FinishDate'] = '';
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

				// rebuild hard links to images
				$project_it = getSession()->getProjectIt();
				
				$parms['Content'] = preg_replace_callback('/file\/([^\/]+)\/([^\/]+)\/([\d]+)/i', 
						function($matches) use ($ids_map, $project_it)
						{
								$file_id = $ids_map[$matches[1]][$matches[3]];
								return $file_id != ''
										? 'file/'.$matches[1].'/'.$project_it->get('CodeName').'/'.$file_id
										: $matches[0];
						}, $it->get('Content')
					);
				
				if ( $it->get('ParentPage') == '' && $it->get('ReferenceName') == WikiTypeRegistry::KnowledgeBase )
				{
					$root = getFactory()->getObject('ProjectPage');
					$root_it = $root->getRootIt();

					if ( $root_it->getId() > 0 )
					{
						$root->modify_parms( $root_it->getId(),
								array (
										'Content' => $parms['Content']
								) 
						);
						
						return array();
					}
				}
				
				$parms['DocumentVersion'] = '';
				
				if ( $parms['ParentPage'] == '' )
				{
					$parms['ParentPath'] = '';
					$parms['SectionNumber'] = '';
				}

				// backward compatibility
				if ( !is_numeric($parms['IsTemplate']) )
				{
					$parms['IsTemplate'] = $parms['IsTemplate'] == 'Y' ? 1 : 0;
				}
				
				if ( !is_numeric($parms['ReferenceName']) )
				{
					$parms['ReferenceName'] = getFactory()->getObject('WikiPage')
							->getByRef('ReferenceName', $parms['ReferenceName'])->getId();
				}

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
					$setting_it = $it->object->getRegistry()->Query(
							array (
									new FilterAttributePredicate('Setting', $it->get('Setting')),
									new FilterBaseVpdPredicate()
							)
					);
					if ( $setting_it->getId() == '' ) {
						$parms['Participant'] = '-1';
						$parms['Value'] = self::replaceUser($it->get('Value'));
					} else {
						return array();
					}
				}
				else
				{
					$it->object->modify_parms( $setting_it->getId(),
							array (
									'Value' => self::replaceUser($it->get('Value'))
							)
					);
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
			case 'cms_Snapshot':
				
				$class_name = getFactory()->getClass($it->get('ObjectClass'));
				
				if ( !class_exists($class_name) ) return array();

				$anchor_id = $ids_map[getFactory()->getObject($class_name)->getEntityRefName()][$it->get('ObjectId')];
				
				if ( $anchor_id == '' ) return array();
				
				$parms['ObjectId'] = $anchor_id;

				break;

            case 'pm_ChangeRequest':
            case 'pm_Task':
                $parms['StartDate'] = '';
                if ( $it->get('FinishDate') != '' ) $parms['FinishDate'] = SystemDateTime::date();
                break;
		}

		if ( $it->object instanceof MetaobjectStatable && $context->getResetState() ) {
			$parms['State'] = '';
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
						switch( $attr ) {
							case 'WikiEditorClass':
								$parms[$attr] = 'WikiSyntaxEditor';
								break;
							case 'DaysInWeek':
								$parms[$attr] = '5';
								break;		
							case 'Importance':
								$parms[$attr] = '3';
								break;		
						}
					}
			}
		}
		
		// remember obsolete attributes values
		foreach( array('IsSubversionUsed', 'IsSupportUsed', 'IsFileServer', 'IsBlogUsed', 'IsKnowledgeUsed') as $attribute )
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

		$it->object->modify_parms($object_it->getId(), $parms);
		
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
			$parms = array();
			foreach ( $attrs as $attr )
			{
				if ( $parms[$attr] == '' ) $parms[$attr] = $it->get_native($attr);
				if ( $attr == 'Project' ) unset($parms[$attr]);
				if ( $attr == 'VPD' ) unset($parms[$attr]);
			}
	
			$it->object->modify_parms($object_it->getId(), $parms);
 		}
 		
 		return $object_it->count() < 1 ? 0 : $object_it->getId();
 	}

	static function applyToCustomReport( & $context, & $attrs, & $it, & $project_it )
	{
		$parms = array();

		// each participant should have his own mytasks/mysissues report
		if ( in_array($it->get('ReportBase'), array('mytasks')) )
		{
			$parms = array();
		}
		else
		{
			$parms = self::applyToObject( $context, $attrs, $parms, $it, $project_it );
			$parms['Author'] = -1;
			$parms['Url'] = self::replaceUser($it->getHtmlDecoded('Url'));

			$report_it = $it->object->getRegistry()->Query(
				array(
					new FilterInPredicate($it->getId()),
					new FilterBaseVpdPredicate()
				)
			);
			if ( $report_it->getId() != '' ) {
				$parms[$it->object->getIdAttribute()] = $report_it->getId();
			}
		}

        $has_id = $parms[$it->object->getIdAttribute()];
		if ( $has_id > 0 ) {
            $it->object->modify_parms(
                $has_id, $parms
			);
			return $has_id;
		}
		else {
			return self::duplicate($it, $parms);
		}

		return $parms;
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
		foreach( array('IsSubversionUsed', 'IsSupportUsed', 'IsFileServer', 'IsBlogUsed', 'IsKnowledgeUsed') as $attribute )
		{
			if ( $it->get($attribute) == '' ) {
				$defaults = $context->getDefaultParms();
				if ( $defaults[$attribute] != '' ) {
					$parms[$attribute] = $defaults[$attribute]; 
				}
			}
		}
		
		$methodology_it = $project_it->getMethodologyIt(); 		
		$methodology_it->object->modify_parms($methodology_it->getId(), $parms);
		getSession()->getProjectIt()->invalidateCache();
 	}

	static function duplicate( $iterator, $parms ) 
	{
		$attributes = array_diff(
				array_keys( $iterator->object->getAttributes() ),
				array( 'RecordCreated', 'RecordModified' )
		);

		$id_attribute = $iterator->getIdAttribute();
		if ( $parms[$id_attribute] > 0 ) {
			// special case for moving objects, use the same record ID
			$temp_it = $iterator->object->getExact($parms[$id_attribute]);
			
			if ( $temp_it->getId() == '' ) $attributes[] = $id_attribute;
		}

		$values = $parms;
		foreach ( $attributes as $attribute ) {
			if ( !array_key_exists( $attribute, $parms) ) {
				$values[$attribute] = $iterator->getHtmlDecoded($attribute);
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
			
		$it->object->modify_parms($object_it->getId(), $parms);
		
		return $object_it->getId();
 	}
 	
 	static function replaceUser( $value )
 	{
 		$value = preg_replace('/taskassignee=[\d]+/i', 'taskassignee=user-id', $value);
		$value = preg_replace('/owner=[\d]+/i', 'owner=user-id', $value);
		return $value;
 	}
}