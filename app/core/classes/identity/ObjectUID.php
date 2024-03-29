<?php
 
class ObjectUID
{
 	var $map;
 	
 	private $baseline_id = ''; 
 	
 	private $server_url = '';
 	
 	private $object = null;
 	
 	function __construct( $baseline_id = '', $object = null )
 	{
 		$this->setBaseline( $baseline_id );

 		// free letters B J L N Y Z
 		$this->map = array(
 			'Request' => 'I',
            'Increment' => 'I',
            'Issue' => 'U',
 			'pm_Task' => 'T',
 			'Requirement' => 'R',
            'RequirementDoc' => 'R',
 		    'ProjectPage' => 'K',
 		    'HelpPage' => 'D',
 			'HelpDoc' => 'D',
 			'pm_Test' => 'E',
			'pm_TestCaseExecution' => 'E',
 			'pm_Artefact' => 'A',
 			'TestScenario' => 'S',
            'TestScenarioOnly' => 'S',
            'TestPlan' => 'S',
 		    'pm_Project' => 'P',
 			'pm_Question' => 'Q',
 			'pm_Milestone' => 'M',
 			'pm_Function' => 'F',
 			'Comment' => 'O',
 			'pm_Meeting' => 'G',
 			'pm_SubversionRevision' => 'C',
			'pm_ReviewRequest' => 'V',
            'ReviewItem' => 'W',
            'Component' => 'X'
 		);
 		
 		$this->server_url = EnvironmentSettings::getServerUrl().'/pm/';
 		
 		$this->setObject($object);
 	}
 	
 	function setObject( $object )
 	{
 		if ( !is_object($object) ) return;
 		
 		/*
 	 	if ( method_exists($object, 'getTerminalStates') && !is_array(self::$terminal_states[get_class($object)]) )
		{
			self::$terminal_states[get_class($object)] = $object->getTerminalStates();
		}
		*/
		
		$this->object = $object;
 	}
 	
 	function setBaseline( $baseline_id )
 	{
 		$this->baseline_id = $baseline_id;
 	}
 	
 	function getBaseline()
 	{
 		return $this->baseline_id;
 	}
 	
 	function getClassName( $object_it ) 
 	{
 		if ( !is_object($object_it) ) return '';
 		if ( !is_object($object_it->object) ) return '';
 		
 		$class_name = $object_it->object->getEntityRefName();
 		
 		if ( $class_name == 'WikiPage' )
 		{
 			$class_name = get_class($object_it->object);
 			
 			if ( in_array($class_name, array('WikiPage','Metaobject')) )
 			{
 				return getFactory()->getObject('WikiType')->getExact($object_it->get('ReferenceName'))->get('ClassName');
 			}
 			else
 			{
 				return $class_name;
 			}
 		}

 		return $class_name;
 	}
 	
 	function isValidUid( $uid ) 
 	{
 		list($type, $object_id) = preg_split('/-/', trim($uid));
 		return is_numeric($object_id) && array_search($type, $this->map) !== false; 
 	}
 	
 	function hasUid( $object_it ) 
 	{
 		return $this->map[$this->getClassName($object_it)] != ''
            || $this->map[get_class($object_it->object)] != ''
            || get_class($object_it->object) == 'PMWikiPage';
 	}
 	
 	function hasUidObject( $object ) 
 	{
 		return ( $this->map[$object->getClassName()] != '' || $this->map[get_class($object)] != '' );
 	}

 	function getObjectUid( $object_it ) 
 	{
 	    if ( $object_it->getId() < 1 ) return '';

 		$class_name = $this->getClassName($object_it);
 		switch ( $class_name )
 		{
			case 'pm_Project':
				return $object_it->get('CodeName');
			case 'pm_TestCaseExecution':
				return $this->getObjectUidInt($class_name, $object_it->get('Test'));
            case 'pm_ChangeRequest':
                if ( $object_it->get('UID') != '' ) {
                    return $object_it->get('UID');
                }
 			default:
 				return $this->map[$class_name] != ''
                    ? $this->getObjectUidInt($class_name, $object_it->getId())
                    : $this->getObjectUidInt(get_class($object_it->object), $object_it->getId());
 		}
 	}

	function getObjectUidInt( $className, $id ) {
		return $this->map[$className].'-'.$id;
	}

 	function getGotoUrl( $object_it )
 	{
 		switch( $object_it->object->getEntityRefName() )
 		{
 			case 'pm_Project':
			    return '/pm/'.$object_it->get('CodeName').'/?tab='.getSession()->getActiveTab();
 			default:
 				return '/pm/'.$object_it->get('ProjectCodeName').'/'.$this->getObjectUid($object_it);
 		}
 	}
 	
 	function getObjectUrl( $uid ) 
 	{
 		$object_it = $this->getObjectIt($uid);

		if ( !is_object($object_it) ) return '/404';
 		if ( !is_object($object_it->object) ) return '/404';
 		if ( $object_it->count() < 1 ) return '/404';
 		
 		if ( $object_it->object->getEntityRefName() == 'Comment' )
 		{
 			$anchor_it = $object_it->getAnchorIt();
 			$url = $anchor_it->getCommentsUrl();
 			if ( $url == '' ) return '';
 			$parts = preg_split('/#/', $url);
 			return $parts[0].'#comment'.$object_it->getId();
 		}
 		else
 		{
	 		return $object_it->getUidUrl();
 		}
 	}
 	
 	function getClassNameByUid( $uid ) 
 	{
 		list($type, $object_id) = preg_split('/-/', $uid);
 		return array_search(strtoupper($type), $this->map);
 	}
 	
 	function getObjectIt( $uid )
 	{
 	    $objectIt = $this->checkUIDResolvers($uid);
 	    if ( is_object($objectIt) && $objectIt->getId() != '' ) {
 	        return $objectIt;
        }

 		list($type, $object_id) = preg_split('/-/', $uid);
 		$class = array_search(strtoupper($type), $this->map);
 		if($class === false) {
 			return $this->checkUIDResolvers($uid);
 		}

 		$class = getFactory()->getClass($class);
 		if ( !class_exists($class, false)) {
            return getFactory()->getObject('Task')->getEmptyIterator();
		}

 		$object = getFactory()->getObject($class);
		$registry = $object->getRegistry();
		if ( $object instanceof WikiPage ) {
			$registry->setPersisters(array(
			    new EntityProjectPersister()
            ));
		}

		return $registry->QueryById($object_id);
 	}

 	function checkUIDResolvers( $uid ) {
 	    foreach( getSession()->getBuilders('ObjectUIDResolver') as $resolver ) {
 	        $objectIt = $resolver->resolve($uid);
 	        if ( is_object($objectIt) && $objectIt->getId() != '' ) return $objectIt;
        }
        return getFactory()->getObject('cms_TempFile')->getEmptyIterator();
    }

 	function getUidIcon( $object_it, $need_project = true )
 	{
 		return $this->getUidIconGlobal( $object_it, $need_project );
 	}

 	function getUIDInfo( $object_it, $caption = false )
 	{
 	    if ( !$this->hasUid( $object_it ) ) {
 	        return array(
 	            'caption' => $object_it->getDisplayName()
            );
        }

        $object_it = $this->specifyIterator($object_it);
		$this->setObject($object_it->object);
		
	    $uid = $this->getObjectUid($object_it);

		$self_project_name = $object_it->get('ProjectCodeName');
		if ( $object_it->object->getEntityRefName() == 'pm_Project' ) {
            $url = $this->server_url.$uid;
        }
        else {
            $url = $this->server_url.$self_project_name.'/'.$uid;
		}

        if ( $object_it->object instanceof MetaobjectStatable ) {
            $stateIt = $object_it->getStateIt();
            $completed = $stateIt->get('IsTerminal') == 'Y';
            $state_name = $stateIt->get('Caption');
        }
        else {
            $completed = false;
            $state_name = $object_it->get('StateName');
        }

		$result = array(
            'uid' => $object_it->get('IncludesUID') != ''
                        ? $object_it->get('IncludesUID')
                        : ($object_it->object->getAttributeType('UID') != 'integer' && $object_it->get('UID') != ''
                                ? $object_it->getHtmlDecoded('UID')
                                : $uid),
            'project' => $self_project_name,
            'completed' => $completed,
			'state_name' => $state_name,
            'class' => get_class($object_it->object),
            'url' => $url,
            'alien' => $self_project_name != '' && $object_it->get('VPD') != getSession()->getProjectIt()->get('VPD'),
			'tooltip-url' => $self_project_name == ''
				? '/tooltip/'.get_class($object_it->object).'/'.$object_it->getId() 
				: '/pm/'.$self_project_name.'/tooltip/'.get_class($object_it->object).'/'.$object_it->getId()
		);

        if ( $this->getBaseline() != '' ) {
            $result['tooltip-url'] .= '?baseline='.$this->getBaseline();
            $result['url'] .= strpos($result['url'], '?') > 0 ? '&baseline='.$this->getBaseline() : '?baseline='.$this->getBaseline();
        }

		if ( $caption && !$object_it->object instanceof Comment ) {
			$result['caption'] = $object_it->getDisplayNameSearch();
			$additional = array();
			foreach( $object_it->object->getAttributesByGroup('display-name') as $attribute ) {
			    if ( $object_it->get($attribute) == '' ) continue;
			    if ( $object_it->object->IsReference($attribute) ) {
                    $additional[] = $object_it->getRef($attribute)->getDisplayName();
                }
			    else {
                    $additional[] = $object_it->get($attribute);
                }
            }
			if ( count($additional) > 0 ) {
			    $result['caption'] .= ' ('.join(',',$additional).')';
            }
            $result['native'] = $object_it->get('Caption');
		}

		return $result;
 	}
 	
 	function getUidTitle( $object_it )
 	{
 		if ( !$this->hasUid( $object_it ) )
 		{
 		    $result = '';
 		    
 		    $project_it = getSession()->getProjectIt();
    
 		    if ( is_object($project_it) && $project_it->get('VPD') != '' && $object_it->get('VPD') != '' && $project_it->get('VPD') != $object_it->get('VPD') )
 		    {
 		        $code_name = $object_it->get('ProjectCodeName');
 		        if ( $code_name != '' ) $result .= '{'.$code_name.'} ';
 		    }
 		    
 		    $result .= $object_it->getDisplayName();

 		    return $result;
 		}
 		
 		$info = $this->getUIDInfo( $object_it, true );
 		
        if ( $object_it->object->getEntityRefName() != 'pm_Project' && $info['alien'] ) {
            $text = '['.$info['project'].":".$info['uid'].'] ';
        }
        else {
            $text = '['.$info['uid'].'] ';
        }

 		$text .= $info['caption'];
 		
 		return $text;
 	}
 	
  	function getUidOnly( $object_it )
 	{
		if ( $object_it->getId() == '' ) return '';
 		if ( !$this->hasUid( $object_it ) ) return '';
 		
 		$info = $this->getUIDInfo( $object_it );
 		
        $text = '['.$info['uid'].'] ';
		
 		if ( $info['alien'] ) $text .= ' {'.$info['project'].'} ';

 		return $text;
 	}
 	
 	function getUidIconGlobal( $object_it, $need_project = true)
	{
 		if ( !$this->hasUid( $object_it ) ) return '';
 		
		switch( $object_it->object->getClassName() )
		{
			case 'Comment':
				break;
			case 'pm_Project':
				$need_project = false;
				break;
		}
		
		$info = $this->getUIDInfo( $object_it );
		
		$text = $info['uid'];
		
		if ( $need_project && $info['alien'] ) $text = $info['project'].":".$text;

		$text = '['.$text.']';

        if ( $info['completed'] ) $text = '<strike>'.$text.'</strike>';

		if ( $object_it->object instanceof TestCaseExecution ) {
			$info['url'] = $object_it->getUidUrl();
		}

        $html = '<a class="uid with-tooltip" tabindex="-1" data-placement="right" data-original-title="" data-content="" info="'.$info['tooltip-url'].'" href="'.$info['url'].'">'.$text.'</a>';
        
        if ( $object_it->object instanceof TestExecution || $object_it->object instanceof TestCaseExecution )
        {
        	$class = $object_it->get('ResultColor') != ''
                ? '" style="background-color: '. array_shift(\TextUtils::parseItems($object_it->get('ResultColor')))
                : 'label-success';
        	$html = '<span class="label label-uid '.$class.'">'.$html.'</span>';
        }

		if ( $object_it->object instanceof ReviewRequest )
		{
			$class = $object_it->get('State') == 'submitted'
				? 'label-success' : ($object_it->get('State') == 'discarded' ? 'label-inverse' : '');
			$html = '<span class="label label-uid '.$class.'">'.$html.'</span>';
		}
		if ( $object_it->object instanceof Commit && $object_it->get('ReviewState') != '' )
		{
			$class = $object_it->get('ReviewState') == 'submitted'
				? 'label-success' : ($object_it->get('ReviewState') == 'discarded' ? 'label-inverse' : '');
			$html = '<span class="label label-uid '.$class.'">'.$html.'</span>';
		}

        return $html;
	}
	
 	function drawUidIcon( $object_it, $need_project = true ) 
 	{
 		echo $this->getUidIconGlobal($object_it, $need_project);
 	}
 	
 	function drawUidInCaption( $object_it, $words = 15 ) 
 	{
		echo $this->getUidWithCaption($object_it, $words);
 	}

 	function getUidWithCaption( $object_it, $words = 15, $baseline = '', $need_project = true )
 	{
		if ( !is_object($object_it) ) return '';
		if ( $object_it->getId() == '' ) return '';
		$object_it = $this->specifyIterator($object_it);
 		$text = $this->getUidIcon( $object_it, $need_project );
 		if ( $object_it->object instanceof Comment ) return $text;
 		$text .= ' ' . $object_it->getDisplayNameExt();
        return $text;
 	}

    function specifyIterator( $iterator )
    {
        if ( get_class($iterator->object) == 'PMWikiPage' ) {
            $className = getFactory()->getObject('WikiType')->getExact($iterator->get('ReferenceName'))->get('ClassName');
            if ( class_exists($className) ) {
                return getFactory()->getObject($className)->createCachedIterator(array(
                    $iterator->getData()
                ));
            }
        }
        return $iterator;
    }
}