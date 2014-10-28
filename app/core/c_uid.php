<?php
 
 class ObjectUID
 {
 	var $map;
 	
 	private $baseline_id = ''; 
 	
 	private $server_url = '';
 	
 	private $object = null;
 	
 	private static $terminal_states = array();
 	
 	function __construct( $baseline_id = '', $object = null ) 
 	{
 		$this->setBaseline( $baseline_id );
 		
 		$this->map = array(
 			'pm_ChangeRequest' => 'I',
 			'pm_Task' => 'T',
 			'Requirement' => 'R',
 			'RequirementTemplate' => 'R',
 		    'ProjectPage' => 'K',
 		    'KnowledgeBaseTemplate' => 'K',
 		    'HelpPage' => 'D',
 			'pm_Test' => 'E',
 			'pm_Artefact' => 'A',
 			'TestScenario' => 'S',
 			'TestingTemplate' => 'S',
 		    'pm_Project' => 'P',
 			'pm_Poll' => 'U',
 			'pm_Question' => 'Q',
 			'BlogPost' => 'B',
 			'pm_Milestone' => 'M',
 			'pm_Function' => 'F',
 			'Comment' => 'O',
 			'pm_TestPlan' => 'L',
 			'pm_Meeting' => 'G',
 			'pm_SubversionRevision' => 'C'
 		);
 		
 		$this->server_url = EnvironmentSettings::getServerUrl().'/pm/';
 		
 		$this->setObject($object);
 	}
 	
 	function setObject( $object )
 	{
 		if ( !is_object($object) ) return;
 		
 	 	if ( method_exists($object, 'getTerminalStates') && !is_array(self::$terminal_states[get_class($object)]) )
		{
			self::$terminal_states[get_class($object)] = $object->getTerminalStates();
		}
		
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
 		
 	 	if( is_a($object_it->object, 'WikiPageTemplate') ) 
 		{
 			$type_it = getFactory()->getObject('WikiType')->getExact($object_it->get('ReferenceName'));
 			
 			switch($type_it->get('ReferenceName')) 
 			{
 				case 'Requirements': return 'RequirementTemplate';
 				case 'KnowledgeBase': return 'KnowledgeBaseTemplate';
 				case 'TestScenario': return 'TestingTemplate';
 			}
 		}
 		
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
 		list($type, $object_id) = preg_split('/-/', $uid);
 		$class = array_search($type, $this->map);
 		return $class !== false; 
 	}
 	
 	function hasUid( $object_it ) 
 	{
 		return ( $this->map[$this->getClassName($object_it)] != '' );
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
 			default:
 				return $this->map[$this->getClassName($object_it)].'-'.$object_it->getId();	
 		}
 	}
 	
 	function getProject( $object_it )
 	{
 		global $project_cache_it;
 		
 		if ( !is_object($project_cache_it) )
 		{
 			$project_cache_it = getFactory()->getObject('ProjectCache')->getAll();

 			$project_cache_it->buildPositionHash(array('VPD'));
 		}
 		
 		$project_cache_it->moveTo('VPD', $object_it->get('VPD'));
 		
 		return $project_cache_it->get('CodeName');
 	}
 	
 	function getGotoUrl( $object_it ) 
 	{
 		switch( $object_it->object->getEntityRefName() )
 		{
 			case 'pm_Project':
 			    
 			    $session = getSession();
 			    
			    return '/pm/'.$object_it->get('CodeName').'/?tab='.$session->getActiveTab();
 				
 			default:
 			    
 				return '/pm/'.$this->getProject( $object_it ).'/'.$this->getObjectUid($object_it);
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
 			$parts = preg_split('/#/', $anchor_it->getCommentsUrl());
 			return $parts[0].'#comment'.$object_it->getId();
 		}
 		else
 		{
	 		return $object_it->getUidUrl();
 		}
 	}
 	
 	function getObjectIt( $uid ) 
 	{
 		global $model_factory;
 		
 		list($type, $object_id) = preg_split('/-/', $uid);
 		$class = array_search(strtoupper($type), $this->map);
 		if($class === false) 
 		{
 			$object = $model_factory->getObject('cms_TempFile');
 			return $object->getExact(-1);
 		}

 		$class = $model_factory->getClass($class);
 		
 		if ( $class == '' || !class_exists($class, false)) return null;
 		
 		$object = $model_factory->getObject($class);

		switch ( $class )
		{
			default:
 				return $object_id > 0 ? $object->getExact($object_id) : $object->getEmptyIterator();
		}
 	}

 	function drawUidNameIcon( $object_it ) {
 	?>
 		<table cellpadding=0 cellspacing=0 width=100%>
 			<tr>
 				<td align=right><? $this->drawUidIcon($object_it) ?></td>
 			</tr>
 		</table>
 	<?
 	}

 	function getUidIcon( $object_it ) 
 	{
 		return $this->getUidIconGlobal( $object_it );
 	}

 	function getUIDInfo( $object_it )
 	{
 	    if ( !$this->hasUid( $object_it ) ) return array();

		$this->setObject($object_it->object);
		
	    $text = $this->getObjectUid($object_it);

		$self_project_name = $this->getProject( $object_it );

		$url = $this->server_url.$self_project_name;
		
		if ( !$object_it->object instanceof Project )
		{
			$url .= '/'.$text; 
		}
		
		$terminal_states = self::$terminal_states[get_class($object_it->object)];
		
		return array(
            'uid' => $text,
            'project' => $self_project_name,
            'completed' => is_array($terminal_states) && in_array( $object_it->get('State'), $terminal_states),
            'url' => $url,
            'alien' => $self_project_name != '' && $object_it->get('VPD') != getSession()->getProjectIt()->get('VPD'),
            'caption' => $object_it->getDisplayName(),
			'tooltip-url' => $self_project_name == '' 
				? '/tooltip/'.get_class($object_it->object).'/'.$object_it->getId() 
				: '/pm/'.$self_project_name.'/tooltip/'.get_class($object_it->object).'/'.$object_it->getId()
		);
 	}
 	
 	function getUidTitle( $object_it )
 	{
 		if ( !$this->hasUid( $object_it ) )
 		{
 		    $result = '';
 		    
 		    $project_it = getSession()->getProjectIt();
    
 		    if ( is_object($project_it) && $project_it->get('VPD') != '' && $object_it->get('VPD') != '' && $project_it->get('VPD') != $object_it->get('VPD') )
 		    {
 		        $code_name = $this->getProject( $object_it );

 		        if ( $code_name != '' ) $result .= '{'.$code_name.'} ';
 		    }
 		    
 		    $result .= $object_it->getDisplayName();

 		    return $result;
 		}
 		
 		$info = $this->getUIDInfo( $object_it );
 		
        $text = '['.$info['uid'].'] ';
		
 		if ( $info['alien'] ) $text .= ' {'.$info['project'].'} ';

 		$text .= $object_it->getDisplayName();
 		
 		return $text;
 	}
 	
  	function getUidOnly( $object_it )
 	{
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
			default:
			    $title = str_replace('"', "'", html_entity_decode($object_it->getDisplayName(), ENT_COMPAT | ENT_HTML401, 'cp1251'));
				break;
		}
		
		$info = $this->getUIDInfo( $object_it );
		
		$text = $info['uid'];
		
		if ( $info['completed'] ) $text = '<strike>'.$text.'</strike>';

		$text = '['.$text.']';
		
 		if ( $need_project && $info['alien'] ) $text .= ' {'.$info['project'].'}';
 		        
        if ( $this->getBaseline() != '' )
        {
        	$info['tooltip-url'] .= '?baseline='.$this->getBaseline();
        	
        	$info['url'] .= strpos($info['url'], '?') > 0 ? '&baseline='.$this->getBaseline() : '?'.$this->getBaseline(); 
        }
        
        return '<a class="with-tooltip" tabindex="-1" data-placement="right" data-original-title="" data-content="" info="'.$info['tooltip-url'].'" href="'.$info['url'].'">'.$text.'</a>';
	}
	
 	function drawUidIcon( $object_it, $need_project = true ) 
 	{
 		echo $this->getUidIconGlobal($object_it, $need_project);
 	}
 	
 	function drawUidInCaption( $object_it, $words = 15 ) 
 	{
		$this->drawUidIcon( $object_it );
		echo ' '.$object_it->getWordsOnlyValue($object_it->getDisplayName(), $words);
 	}

 	function getUidWithCaption( $object_it, $words = 15, $baseline = '' ) 
 	{
		return $this->getUidIcon( $object_it ).' '.
			$object_it->getWordsOnlyValue($object_it->getDisplayName(), $words);
 	}
 }
 
?>