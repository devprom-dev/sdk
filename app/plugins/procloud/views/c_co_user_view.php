<?php

/////////////////////////////////////////////////////////////////////////////////
class CoUserPageContent extends CoPageContent
{
	var $profile_it;
	
	function validate()
	{
		global $project_it, $_REQUEST, $model_factory, $user_it;
		
		if ( $_REQUEST['id'] != '' )
		{
			$user = $model_factory->getObject('cms_User');
			$this->profile_it = $user->getExact($_REQUEST['id']);
			
			if ( $this->profile_it->count() < 1 )
			{
				return false;
			}
		}
		else
		{
			if ( !is_object($user_it) )
			{
				return false;
			}

			if ( !$user_it->IsReal() )
			{
				return false;
			}
			
			$this->profile_it = $user_it; 
		}

		return true;
	}
	
	function getTitle()
	{
		if ( is_object($this->profile_it) )
		{
			return $this->profile_it->getDisplayName().' - '.parent::getTitle();
		}
		else
		{
			return translate('Участник').' - '.parent::getTitle();
		}
	}
	
	function getKeywords()
	{
		return '';
	}

	function getActions()
	{
		global $_REQUEST, $user_it;
		
		$actions = array();
		
		if ( $this->profile_it->getId() != $user_it->getId() )
		{
			$url = '/message/send/'.$this->profile_it->getId();
			if ( !$user_it->IsReal() )
			{
				$url = "javascript: getLoginForm('".$url."')";
			}
			
			array_push($actions, array(
				'url' => $url,
				'name' => translate('Отправить сообщение')
				));
		}
		else
		{
			if ( $_REQUEST['action'] != 'modify' )
			{
				array_push($actions, array(
					'url' => '/profile/action/modify',
					'name' => translate('Настроить профиль')
					));
			}

			if ( $_REQUEST['action'] != 'reset' && $user_it->get('Password') != $user_it->object->getHashedPassword('') )
			{
				array_push($actions, array(
					'url' => '/profile/action/reset',
					'name' => translate('Изменить пароль')
					));
			}
		}

		return $actions;
	}

	function draw()
	{
		global $model_factory, $project_it, $user_it, $_REQUEST;
		
		$page = $this->getPage();
		$title = $this->profile_it->getDisplayName();
		
		$this->drawHeaderWithTitle( translate('Профиль'), $title, $this->getActions());

		if ( $_REQUEST['action'] != '' )
		{
			echo '<div id="bloglist" style="width:100%;">';
				switch ( $_REQUEST['action'] )
				{
					case 'modify':
						$this->drawProfileForm();
						break;

					case 'reset':
						$this->drawPasswordForm();
						break;
				}
			echo '</div>';
		}
		else
		{
			echo '<div id="bloglist">';
				$this->drawProfile();
			echo '</div>';
			
			echo '<div id="user_actions" style="width:200px;padding-top:56px;">';
				echo '<div class="action_box">';
					$page->drawGreyBoxBegin();
	 					echo '<img class="photo" width=170 height=170 src="'.ParserPageUrl::getPhotoUrl($this->profile_it).'">';
					$page->drawGreyBoxEnd();
				echo '</div>';
			echo '</div>';
		}
		
		echo '<div style="clear:both;">&nbsp;</div>';
	}

	function profileFilledTotally()
	{
		if ( $this->profile_it->get('Email') == '' ) return false;
		if ( $this->profile_it->get('Caption') == $this->profile_it->get('Login') ) return false;
		
		return true;
	}
	
	function drawProfile()
	{	
		global $model_factory, $project_it, $user_it;

		$page = $this->getPage();
		$user = $model_factory->getObject('cms_User');

		echo '<div class="section" id="function1">';

			if ( $this->profile_it->getId() == $user_it->getId() && !$this->profileFilledTotally() )
			{
				echo '<div class="active" id="button" name="but4">';
					echo '<div id="lt">&nbsp;</div>';
					echo '<div id="bd"><div id="txt">'.
							'<a href="javascript: choosebutton(4);">'.translate('Настройки профиля').'</a>'.
						 '</div></div>';
					echo '<div id="rt"><a href="javascript: choosebutton(4);" style="text-decoration:none;">&nbsp;&nbsp;&nbsp;&nbsp;</a></div>';
				echo '</div>';
		
				echo '<div style="clear:both;"></div>';
				echo '<br/>';
				
				echo '<div class="description" id="desc3">';
					$page->drawWhiteBoxBegin();

					if ( $this->profile_it->get('Email') == '' )
					{
						echo '<div>'.text('procloud623').'</div>';
					}
	
					if ( $this->profile_it->get('Caption') == $this->profile_it->get('Login') )
					{
						echo '<br/><div>'.text('procloud624').'</div>';
					}

					$page->drawWhiteBoxEnd();
					echo '<br/>';
				echo '</div>';				
			}

			$comment = $model_factory->getObject('Comment');
			$project = $model_factory->getObject('pm_Project');

			$sql = " SELECT c.ObjectId, c.ObjectClass, " .
				   "		MAX(c.RecordModified) as RecordModified," .
				   "        (SELECT COUNT(1) FROM Comment c2 " .
				   "	      WHERE c2.ObjectId = c.ObjectId " .
				   "            AND c2.ObjectClass = c.ObjectClass ) as CommentsCount," .
				   "		i.Project " .
				   "   FROM Comment c, pm_PublicInfo i".
				   "  WHERE c.ObjectClass IN ('pmblogpost', 'request', 'knowledgebase', 'question', 'helppage')" .
				   "    AND c.VPD = i.VPD " .
				   "    AND c.AuthorId = ".$this->profile_it->getId().
				   "    AND i.IsProjectInfo = 'Y' ".
				   "  GROUP BY c.ObjectId, c.ObjectClass, i.Project ".
				   "  ORDER BY RecordModified DESC LIMIT 10 ";
			
			$comment_it = $comment->createSQLIterator( $sql );
			
			echo '<div class="active" id="button" name="but3">';
				echo '<div id="lt">&nbsp;</div>';
				echo '<div id="bd"><div id="txt">'.
						'<a href="javascript: choosebutton(3);">'.translate('Обсуждения').'</a>'.
					 '</div></div>';
				echo '<div id="rt"><a href="javascript: choosebutton(3);" style="text-decoration:none;">&nbsp;&nbsp;&nbsp;&nbsp;</a></div>';
			echo '</div>';
	
			echo '<div style="clear:both;"></div>';
			echo '<br/>';
			
			echo '<div class="description" id="desc3">';
				$page->drawWhiteBoxBegin();
			
				while ( !$comment_it->end() )
				{
					$project_it = $project->getExact($comment_it->get('Project'));
					$session = new PMSession($project_it->getId());

					$object_it = $comment_it->getAnchorIt();
					
					if ( $object_it->count() < 1 )
					{
						$comment_it->moveNext();
						continue;
					}
					
					if ( $project_it->count() < 1 )
					{
						$comment_it->moveNext();
						continue;
					}

					echo '<div id="comcount" title="'.translate('Комментарии').'">';
						echo '<a href="'.ParserPageUrl::parse($object_it).'#comment">'.$comment_it->get('CommentsCount').'</a>';
					echo '</div>';

					echo '<div style="float:left;">';
						echo '<a class="author" href="'.ParserPageUrl::parse($project_it).'">'.$project_it->getDisplayName().'</a> &nbsp; ';
					echo '</div>';

					echo '<div style="float:left;">'.$object_it->getWordsOnly('Caption', 20).'</div>';

					echo '<div style="clear:both;"></div>';
					echo '<br/>';

					$comment_it->moveNext();
				}
				
				if ( $comment_it->count() < 1 )
				{
					echo $this->profile_it->getId() == $user_it->getId() ? text('procloud550') : text('procloud553');
				}

				$page->drawWhiteBoxEnd();

				echo '<br/>';
			echo '</div>';
			
	 		$project_it = $user->getPublicProjectIt($this->profile_it);
	 		
			echo '<div class="active" id="button" name="but1">';
				echo '<div id="lt">&nbsp;</div>';
				echo '<div id="bd"><div id="txt">'.
						'<a href="javascript: choosebutton(1);">'.translate('Участник проектов').'</a>'.
					 '</div></div>';
				echo '<div id="rt"><a href="javascript: choosebutton(1);" style="text-decoration:none;">&nbsp;&nbsp;&nbsp;&nbsp;</a></div>';
			echo '</div>';
	
			echo '<div style="clear:both;"></div>';
			echo '<br/>';
			
			echo '<div class="description" id="desc1">';
				$page->drawWhiteBoxBegin();
			
		 		while ( !$project_it->end() )
		 		{
			 		echo '<div class="post">';
			 			echo '<h3><a href="'.ParserPageUrl::parse($project_it).'">'.$project_it->getDisplayName().'</a></h3>';
			 		echo '</div>';

			 		echo '<div class="post">';
			 			echo $project_it->getHtmlValue($project_it->getWordsOnly('Description', 10));
			 		echo '</div>';
			 		
			 		echo '<br/>';
		 		
		 			$project_it->moveNext();
		 		}
	
				if ( $project_it->count() < 1 )
				{
					echo $this->profile_it->getId() == $user_it->getId() ? text('procloud551') : text('procloud554');
				}

				$page->drawWhiteBoxEnd();
	
				echo '<br/>';
	
			echo '</div>';
	 		
	 		$project_it = $user->getSubscribedProjectIt($this->profile_it);
	 		
			echo '<div class="active" id="button" name="but2">';
				echo '<div id="lt">&nbsp;</div>';
				echo '<div id="bd"><div id="txt">'.
						'<a href="javascript: choosebutton(2);">'.translate('Пользователь проектов').'</a>'.
					 '</div></div>';
				echo '<div id="rt"><a href="javascript: choosebutton(2);" style="text-decoration:none;">&nbsp;&nbsp;&nbsp;&nbsp;</a></div>';
			echo '</div>';
	
			echo '<div style="clear:both;"></div>';
			echo '<br/>';
			
			echo '<div class="description" id="desc2">';
				$page->drawWhiteBoxBegin();
			
		 		while ( !$project_it->end() )
		 		{
			 		echo '<div class="post">';
			 			echo '<h3><a href="'.ParserPageUrl::parse($project_it).'">'.$project_it->getDisplayName().'</a></h3>';
			 		echo '</div>';

			 		echo '<div class="post">';
			 			echo $project_it->getHtmlValue($project_it->getWordsOnly('Description', 10));
			 		echo '</div>';
			 		
			 		echo '<br/>';
		 		
		 			$project_it->moveNext();
		 		}
	
				if ( $project_it->count() < 1 )
				{
					echo $this->profile_it->getId() == $user_it->getId() ? text('procloud552') : text('procloud555');
				}

				$page->drawWhiteBoxEnd();
	
				echo '<br/>';
	
			echo '</div>';
			
		echo '</div>';
	}

	function drawProfileForm()
	{
		global $model_factory, $user_it;
		
		$page = $this->getPage();
		$form = new CoProfileForm( $user_it );

		echo '<div class="post">';
			$page->drawWhiteBoxBegin();
			echo '<div style="float:left;width:60%;">';
				$form->draw();
			echo '</div>';
			
			echo '<div style="float:right;width:30%;text-align:right;">';
 				echo '<img class="photo" width=170 height=170 src="'.ParserPageUrl::getPhotoUrl($user_it).'">';
			echo '</div>';
			$page->drawWhiteBoxEnd();
		echo '</div>';
	}

	function drawPasswordForm()
	{
		global $model_factory, $user_it;
		
		$page = $this->getPage();
		$form = new CoPasswordForm( $user_it );

		echo '<div class="post">';
			$page->drawWhiteBoxBegin();
			echo '<div style="float:left;width:60%;">';
				$form->draw();
			echo '</div>';
			$page->drawWhiteBoxEnd();
		echo '</div>';
	}
}

 ////////////////////////////////////////////////////////////////////////////////
 class CoProfileForm extends CoPageForm
 {
 	function getModifyCaption()
 	{
 		return text('procloud268');
 	}

 	function getCommandClass()
 	{
 		return 'coprofile';
 	}
 	
	function getAttributes()
	{
		$attributes = parent::getAttributes();
		
		array_push($attributes, 'Photo');

		return $attributes; 	
	}

	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Photo':
				return 'file';
			
			default:
				return parent::getAttributeType( $attribute );
		}
	}

	function IsAttributeVisible( $attribute )
	{
		global $model_factory;

		switch ( $attribute )
		{
			case 'Password':
			case 'IsAdmin':
			case 'IsShared':
			case 'Rating':
			case 'Language':
			case 'ICQ':
			case 'Phone':
			case 'Skype':
				return false;
				
			case 'Photo':
				return true;

			default:
				return parent::IsAttributeVisible( $attribute );
		}
	}
 	
	function IsAttributeModifable( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			case 'Password':
			case 'IsAdmin':
			case 'IsShared':
				return false;
			
			case 'Login':
				$settings = $model_factory->getObject('cms_SystemSettings');
				$settings_it = $settings->getAll();
				
				return $settings_it->get('AllowToChangeLogin') == 'Y';

			default:
				return true;
		}
	}

 	function getDescription( $attribute )
 	{
 		switch( $attribute )
 		{
 			case 'Caption':
 				return text('procloud269');

 			case 'Email':
 				return text('procloud270');

 			case 'Login':
 				return text('procloud271');

 			case 'Language':
 				return text('procloud272');

 			case 'Skills':
 				return text('procloud273');

 			case 'Tools':
 				return text('procloud274');

 			case 'Photo':
 				return text('procloud47');
 		}
 	}
 }

 ////////////////////////////////////////////////////////////////////////////////
 class CoPasswordForm extends CoPageForm
 {
 	function getModifyCaption()
 	{
 		return text('procloud544');
 	}

 	function getCommandClass()
 	{
 		return 'copassword';
 	}
 	
	function getAttributes()
	{
		return array('OldPassword', 'NewPassword', 'RepeatPassword'); 	
	}

	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'OldPassword':
			case 'NewPassword':
			case 'RepeatPassword':
				return 'password';
		}
	}

	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'OldPassword':
				return translate('Текущий пароль'); 	
			case 'NewPassword':
				return translate('Новый пароль'); 	
			case 'RepeatPassword':
				return translate('Повтор нового пароля'); 	
		}
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}
 	
	function IsAttributeModifable( $attribute )
	{
		return true;
	}

	function IsAttributeRequired( $attribute )
	{
		return true;
	}
 }

?>
