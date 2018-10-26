<?php
 
 class FindUser extends CommandForm
 {
 	function validate()
 	{
		global $_REQUEST, $model_factory, $project_it;

		// proceeds with validation
		$this->checkRequired( array('Email') );

		// check authorization was successfull
		if ( getSession()->getUserIt()->getId() < 1 || !is_object($project_it) )
		{
			return false;
		}
		
		return true;
 	}
 	
 	function create()
	{
		global $_REQUEST, $model_factory, $project_it;
		
		$user_cls = $model_factory->getObject('cms_User');
		
		$this->user_it = $user_cls->getByRef('LCASE(Email)', 
			strtolower($_REQUEST['Email']) );
			
		if($this->user_it->count() < 1) 
		{
			$this->replySuccess( $this->getResultDescription( 2 ) );
		}

		// проверим на уникальность login
		$this->part = $model_factory->getObject('pm_Participant');
		
		while ( !$this->user_it->end() )
		{
			$part_it = $this->part->getByRefArray(
				array( 'Project' => $_REQUEST['project'],
					   'SystemUser' => $this->user_it->getId() ) 
					  );
			
			if ( $part_it->count() > 0 )
			{
				$this->replySuccess( $this->getResultDescription( 3 ) );
			}
			
			$this->user_it->moveNext();
		}
		
		$this->user_it->moveFirst();

		while ( !$this->user_it->end() )
		{
			$this->result .= '<div class="line">'.translate('Добавить участника').': '.
				'<a href="'.$this->part->getPageName().'&SystemUser='.$this->user_it->getId().'">'.
				$this->user_it->getDisplayName().' ['.$this->user_it->get('Login').']</a>'.
				'</div>';
				
			$this->user_it->moveNext();
		}
		
		$this->replySuccess( 
			$this->getResultDescription( -1 ) );
	}

	function getResultDescription( $result )
	{
		global $_REQUEST, $model_factory;
		
		$session = getSession();
		
		switch($result)
		{
			case -1:
				return $this->result;

			case 1:
				return text(200);

			case 2:
			    $menu = $model_factory->getObject('Module')->getExact('procloud/invite')->buildMenuItem('?mode=invite&Email='.$_REQUEST['Email']);
			    
				$message = '<div class="line">'.
					text(217).': ' .'<a href="'.$menu['url'].'">'.translate('пригласить пользователя').'</a></div>';

				return $message;
			
			case 3:
				return text(218);

			default:
				return parent::getResultDescription( $result );
		}
	}
 }
 
?>