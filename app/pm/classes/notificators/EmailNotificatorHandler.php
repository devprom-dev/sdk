<?php

class EmailNotificatorHandler
{
	private $system_attributes = array();
	
	function getTemplate()
	{
		return 'object-changed.twig';
	}
	
	function getSubject( $subject, $object_it, $prev_object_it, $action, $recipient )
	{
		$uid = new ObjectUid;
		return $object_it->object->getDisplayName().' {'.getSession()->getProjectIt()->get('CodeName').'} ['.$uid->getObjectUid($object_it).']';
	}
	
	function getParticipants( $object_it, $prev_object_it, $action ) 
	{
		return array();
	}	
	
	function getUsers( $object_it, $prev_object_it, $action ) 
	{
		return array();
	}	
	
	function getRenderParms($action, $object_it, $prev_object_it)
	{
		$uid = new ObjectUID();
		$info = $uid->getUidInfo($object_it);

		return array (
				'title' => $object_it->getDisplayName(),
				'author' => getSession()->getUserIt()->getDisplayName(),
				'url' => $info['url'],
				'fields' => $this->getFields($action, $object_it, $prev_object_it)
		);
	}
	
	function IsParticipantNotified( $participant_it )
	{
		$notification_type = getFactory()->getObject('Notification')->getType( $participant_it );
		return $notification_type == 'all' || $notification_type == 'system';
	}
	
	function participantHasAccess( $participant_it, $object_it )
	{
		if ( $object_it->getId() == '' ) return false;
		
		$policy = getFactory()->getAccessPolicy();
		
		if ( $policy instanceof AccessPolicyBase )
		{
			$roles = $participant_it->getRoles();
			 
		    foreach( $roles as $key => $role )
	        {
	            if ( $role < 1 ) $roles[$key] = $policy->getRoleByBase( $role );
	        }
			
	        $class_name = get_class($policy);
	        
	        $policy = new $class_name(getFactory()->getCacheService());
	        
	        $policy->setRoles($roles);
		}
		
		return $policy->getObjectAccess(ACCESS_READ, $object_it) && $policy->getEntityAccess(ACCESS_READ, $object_it->object);
	}

	protected function getFields( $action, $object_it, $prev_object_it )
	{
		$parms = array();
		
		foreach( array_keys($object_it->object->getAttributesSorted()) as $attribute ) 
		{
			if( !$this->isAttributeVisible($attribute, $object_it, $action) ) continue;
	
			$was_value = $this->getValue( $prev_object_it, $attribute );
			$now_value = $this->getValue( $object_it, $attribute );
			$attribute_title = translate($object_it->object->getAttributeUserName($attribute));
			
			if( $was_value != '' || $now_value != '' ) 
			{
				if($object_it->object->IsReference($attribute)) 
				{
					if( $was_value != $now_value ) 
					{
						$parms[$attribute] = array (
								'name' => $attribute_title,
								'value' => $object_it->getRef($attribute)->getDisplayName(),
								'was_value' => $prev_object_it->getRef($attribute)->getDisplayName(),
								'type' => 'ref'
						);
					}
					elseif( $this->isAttributeRequired($attribute, $object_it, $action) )
					{
						$parms[$attribute] = array (
								'name' => $attribute_title,
								'value' => $object_it->getRef($attribute)->getDisplayName(),
								'type' => 'ref'
						);
					}
				} 
				else 
				{
					if ( $was_value != $now_value ) 
					{
						$parms[$attribute] = array (
								'name' => $attribute_title,
								'value' => $now_value,
								'was_value' => $was_value,
								'type' => $object_it->object->getAttributeType($attribute)
						);
					}
					else
					{
						if( $this->isAttributeRequired($attribute, $object_it, $action) )
						{
							$parms[$attribute] = array (
									'name' => $attribute_title,
									'value' => $now_value,
									'type' => $object_it->object->getAttributeType($attribute)
							);
						}
					}
				}
			}
		}		
		
		return $parms;
	}
		
	public static function getValue( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'State':
				if ( method_exists($object_it, 'getStateIt') )
				{
					$state_it = $object_it->getStateIt();
					return $state_it->getDisplayName();
				}
				break;
		}
		
		$att_type = $object_it->object->getAttributeType($attr);

		if ( $att_type == 'wysiwyg' )
		{ 
			$editor = WikiEditorBuilder::build();

			$parser = $editor->getHtmlParser();
			$parser->setObjectIt( $object_it );
				
			$value = $parser->parse( $object_it->getHtmlDecoded($attr) );
			return preg_replace('/\r|\n/', '', $value); 
		}
		
		if ( $att_type == 'file' )
		{
			return $object_it->getFileName($attr);
		}
		else
		{
			$value = $object_it->getHtmlDecoded($attr);
		}
		
		if ( $value == 'N' )
		{
			$value = translate('Нет');
		}
		
		if ( $value == 'Y' )
		{
			$value = translate('Да');
		}

		return $value;
	}
	
	protected function isAttributeVisible( $attribute_name, $object_it, $action )
	{
		if ( $attribute_name == 'Caption' ) return false;
		
		switch ( $object_it->object->getClassName() )
		{
			case 'pm_Participant':
				if ( $attribute_name == 'Salary' )
				{
					return false;
				}
				break;
			
			case 'pm_Task':
				if ( $attribute_name == 'PercentComplete' )
				{
					return false;
				}				
				break;

			case 'pm_Project':
				switch ( $attribute_name )
				{
					case 'Version':
					case 'Platform': 
					case 'Tools':
					case 'MainWikiPage':
					case 'RequirementsWikiPage':
					case 'Blog':
					case 'IsConfigurations':
					case 'Rating':
					case 'IsTender':
						return false;
				}
				break;
				
			case 'pm_ChangeRequest':
				if ( $action == 'add' && $attribute_name == 'ExternalAuthor' && $object_it->get('ExternalAuthor') != '' )
				{
					return true;
				}
				break;
		}

		switch ( $attribute_name )
		{
			case 'Password': return false;
			
			default:
				if ( $object_it->object->getAttributeType( $attribute_name ) == 'password' ) return false;
				if ( in_array($attribute_name, $this->getSystemAttributes($object_it)) ) return false;
				
				return $object_it->object->IsAttributeVisible( $attribute_name );
		}
	}

	protected function isAttributeRequired( $attribute_name, $object_it, $action )
	{
		switch ( $object_it->object->getClassName() )
		{
			case 'pm_ChangeRequest':
				switch ( $attribute_name )
				{
					case 'ClosedInVersion':
					case 'Project':
					case 'Owner':
						return false;
				}				
				return true;

			case 'pm_Task':
				switch ( $attribute_name )
				{
					case 'ChangeRequest':
					case 'LeftWork':
					case 'Assignee':
						return false;					
				}
				return true;

			case 'pm_Question':
				switch ( $attribute_name )
				{
					case 'Caption':
					case 'Author':
						return false;					
				}
				return true;

			case 'BlogPost':
				switch ( $attribute_name )
				{
					case 'Caption':
						return false;					
				}
				return true;
				
			default:	
				return true;
		}
	}
	
	protected function getSystemAttributes( $object_it )
	{
		if ( isset($this->system_attributes[get_class($object_it->object)]) )
		{
			return $this->system_attributes[get_class($object_it->object)];
		}
		
		return $this->system_attributes[get_class($object_it->object)] = $object_it->object->getAttributesByGroup('system');
	}	
}
