<?php
use Devprom\CommonBundle\Service\Emails\RenderService;

class EmailNotificatorHandler
{
	private $system_attributes = array();
	
	function getTemplate()
	{
		return 'object-changed.twig';
	}
	
	function getSubject( $subject, $object_it, $prev_object_it, $action, $recipient )
	{
        $render_service = new RenderService(
            getSession(), SERVER_ROOT_PATH."pm/bundles/Devprom/ProjectBundle/Resources/views/EmailSubject"
        );

        $projectIt = $object_it->getRef('Project');
        if ( $projectIt->getId() == '' ) $projectIt = getSession()->getProjectIt();

        $uid = new ObjectUid;
        return $render_service->getContent('subject-changed.twig', array(
            'entityName' => $object_it->object->getDisplayName(),
            'codeName' => $projectIt->getHtmlDecoded('CodeName'),
            'projectName' => $projectIt->getHtmlDecoded('Caption'),
            'uid' => $uid->getObjectUid($object_it),
            'title' => $object_it->getHtmlDecoded('Caption')
        ));
	}
	
	function getUsers( $object_it, $prev_object_it, $action )
	{
		return array();
	}	

	function getProject( $object_it ) {
	    if ( $object_it->get('VPD') == '' ) {
	        getSession()->getProjectIt();
        }
        else {
            return getFactory()->getObject('Project')->getByRef(
                'VPD', $object_it->get('VPD')
            );
        }
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
		return $participant_it->get('NotificationEmailType') == 'direct';
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
	        
	        $policy = new $class_name(getFactory()->getCacheService(), getSession());
	        
	        $policy->setRoles($roles);
		}
		
		return $policy->getObjectAccess(ACCESS_READ, $object_it) && $policy->getEntityAccess(ACCESS_READ, $object_it->object);
	}

	protected function getFields( $action, $object_it, $prev_object_it )
	{
		$parms = array();
		
		foreach( array_keys($object_it->object->getAttributes()) as $attribute )
		{
			if( !$this->isAttributeVisible($attribute, $object_it, $action) ) continue;

			$attribute_title = translate($object_it->object->getAttributeUserName($attribute));
			$was_value = $this->getWasValue( $prev_object_it, $attribute );
			$now_value = $this->getValue( $object_it, $attribute );

			if( $was_value != '' && $was_value != $now_value )
			{
				if($object_it->object->IsReference($attribute)) 
				{
					$parms[$attribute] = array (
							'name' => $attribute_title,
							'value' => $object_it->getRef($attribute)->getDisplayName(),
							'was_value' => $prev_object_it->getRef($attribute)->getDisplayName(),
							'type' => 'ref'
					    );
				}
				else 
				{
					$parms[$attribute] = array (
							'name' => $attribute_title,
							'value' => $now_value,
							'was_value' => $was_value,
							'type' => $object_it->object->getAttributeType($attribute)
					    );
				}
			}

			if( $was_value == '' && $now_value != '' && !array_key_exists($attribute, $parms) )
			{
				if($object_it->object->IsReference($attribute))
				{
					$parms[$attribute] = array (
							'name' => $attribute_title,
							'value' => $object_it->getRef($attribute)->getDisplayName(),
							'type' => 'ref'
					    );
				}
				else
				{
					$parms[$attribute] = array (
							'name' => $attribute_title,
							'value' => $now_value,
							'type' => $object_it->object->getAttributeType($attribute)
					    );
				}
			}
		}

		if ( array_key_exists('Assignee', $parms) || array_key_exists('Owner', $parms) ) {
            $value = $this->getField($object_it, 'Description');
            if ( $parms['Description'] == '' && $value != '' ) {
                $parms['Description'] = $value;
            }
        }

		return $parms;
	}

	function getField( $object_it, $attribute )
    {
        if ( $object_it->object->getAttributeType($attribute) != '' ) {
             return array (
                'name' => translate($object_it->object->getAttributeUserName($attribute)),
                'value' => $this->getValue( $object_it, $attribute),
                'type' => $object_it->object->getAttributeType($attribute)
            );
        }
        return array();
    }

	public static function getWasValue( $object_it, $attr )
	{
	    switch( $attr ) {
            case 'TransitionComment':
                return "";
            default:
                return self::getValue( $object_it, $attr );
        }
	}

	public static function getValue( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'State':
				if ( method_exists($object_it, 'getStateIt') ) {
					$state_it = $object_it->getStateIt();
					return $state_it->getDisplayName();
				}
				break;
		}
		
		$att_type = $object_it->object->getAttributeType($attr);
		switch( $att_type ) {
            case 'wysiwyg':
                $editor = WikiEditorBuilder::build();

                $parser = $editor->getHtmlParser();
                $parser->setObjectIt( $object_it );

                $value = $parser->parse( $object_it->getHtmlDecoded($attr) );
                return preg_replace('/\r|\n/', '', $value);

            case 'file':
                return $object_it->getFileName($attr);

            case 'date':
                return $object_it->getDateFormattedShort($attr);

            case 'datetime':
                return $object_it->getDateTimeFormat($attr);

            default:
                $value = $object_it->getHtmlDecoded($attr);
        }

		if ( $value == 'N' ) {
			$value = translate('Нет');
		}
		
		if ( $value == 'Y' ) {
			$value = translate('Да');
		}

		return $value;
	}

	protected function isAttributeVisible( $attribute_name, $object_it, $action )
	{
		if ( $attribute_name == 'Caption' ) return false;

        $groups = $object_it->object->getAttributeGroups($attribute_name);
        if ( in_array('skip-notification', $groups) ) return false;

		switch ( $object_it->object->getClassName() )
		{
			case 'pm_Project':
				switch ( $attribute_name )
				{
					case 'Version':
					case 'Platform': 
					case 'Tools':
					case 'Blog':
					case 'IsConfigurations':
					case 'Rating':
					case 'IsTender':
						return false;
				}
				break;
				
			case 'pm_ChangeRequest':
                switch ( $attribute_name )
                {
                    default:
                        if ( $action == 'add' && $attribute_name == 'ExternalAuthor' && $object_it->get('ExternalAuthor') != '' ) {
                            return true;
                        }
                }
				break;
		}

		switch ( $attribute_name )
		{
			case 'Password':
                return false;

            case 'State':
                return true;
			
			default:
				if ( $object_it->object->getAttributeType( $attribute_name ) == 'password' ) return false;
				if ( in_array($attribute_name, $this->getSystemAttributes($object_it)) ) return false;
				
				return $object_it->object->IsAttributeVisible( $attribute_name );
		}
	}

	protected function getSystemAttributes( $object_it )
	{
		if ( isset($this->system_attributes[get_class($object_it->object)]) ) {
			return $this->system_attributes[get_class($object_it->object)];
		}
		
		return $this->system_attributes[get_class($object_it->object)] =
				array_merge(
						$object_it->object->getAttributesByGroup('system'),
						$object_it->object->getAttributesByGroup('trace')
				);
	}

	public function getEntityIt( $objectIt ) {
	    return $objectIt;
    }
}
