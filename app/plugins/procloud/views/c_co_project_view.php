<?php

/////////////////////////////////////////////////////////////////////////////////
class CoProjectPageContent extends CoPageContent
{
	function validate()
	{
		global $project_it, $_REQUEST, $model_factory, $user_it;
		
		if ( !is_object($project_it) )
		{
			return false;
		}
		
		if ( $_REQUEST['action'] != '' )
		{
			switch ( $_REQUEST['action'] )
			{
				case 'join':
					if ( !$user_it->IsReal() || $project_it->IsSubscribed() || !$project_it->IsPublic() )
					{
						return false;
					}
					
					$sub = $model_factory->getObject('co_ProjectSubscription');
					$sub->add_parms( array( 'Project' => $project_it->getId(), 'SystemUser' => $user_it->getId() ) );

					exit(header('Location: /main/'.$project_it->get('CodeName')));				

				case 'leave':
					if ( !$user_it->IsReal() || !$project_it->IsSubscribed() )
					{
						return false;
					}
					
					$sub = $model_factory->getObject('co_ProjectSubscription');
					$it = $sub->getByRefArray( array( 'Project' => $project_it->getId(), 'SystemUser' => $user_it->getId() ) );

					if ( $it->count() > 0 )
					{
						$sub->delete( $it->getId() );
					}
					
					exit(header('Location: /room'));				
			}
		}
		
		if ( !$project_it->IsPublic() )
		{
			if ( !$project_it->hasUserAccess( $user_it->getId() ) )
			{
				return false;
			}
		}
		
		/*
		if ( $project_it->HasProductSite() )
		{
			exit(header('Location: '.ParserPageUrl::parse($project_it)));				
		}
		*/

		$session = new PMSession($project_it->getId());
		
		$this->canmanage = false;
		
		return true;
	}

	function draw()
	{
		global $model_factory, $project_it, $user_it, $_REQUEST;
		
		$page = $this->getPage();
		$this->canmanage = $project_it->hasUserAccess( $user_it->getId() );
		
		$this->drawProjectHeaderWithActions( 
			translate('Проект'), $this->getActions() );
			
		// introduction
		echo '<div style="width:100%;">';
			echo '<div id="projectpage">';
				echo '<div style="clear:both;">';
					$page->drawWhiteBoxBegin();
	
					if ( !$project_it->IsPublic() )
					{
						echo '<div style="clear:both;color:grey;">'.text('procloud639').'</div>';
					}
					else
					{
						$page_it = $project_it->getProductPageIt();
						if ( $page_it->getId() > 0 && $page_it->get('Content') != '' )
						{
				 			$parser = new SiteWikiParser( $page_it, $project_it );
							echo $parser->parse();

							$children_it = $page_it->getChildrenIt();
							$has_buttons = $children_it->count() > 0 || $this->canmanage;
							
							if ( $has_buttons )
							{
								echo '<br/>';
								echo '<br/>';
							}
							
							if ( $children_it->count() > 0 )
							{
								$page->drawBlackButton( 
									'<a href="'.ParserPageUrl::parse($page_it).'">'.translate('Подробное описание').'</a>' );
							}

							if ( $this->canmanage )
							{
								$page->drawBlackButton( 
									'<a href="/pm/'.$project_it->get('CodeName').'/'.$page_it->getEditUrl().'">'.translate('Редактировать').'</a>' );
							}
						}
						else
						{
							echo $project_it->getHtml('Description');
						}
					}
			
					$page->drawWhiteBoxEnd();
				echo '</div>';
	
				echo '<br/>';
	
				$methodology_it = $project_it->getMethodologyIt();
				
				echo '<div style="float:left;width:48%;">';
					echo '<div class="section" id="function1">';
						$this->drawQuestions();
					
						if ( $project_it->IsPublicBlog() )
						{
							$this->drawBlog();
						}
						/*
						if ( $methodology_it->IsUsedPolls() )
						{
							$this->drawPolls();
						}
						*/
						
						/*
						if ( $project_it->IsPublicArtefacts() && !$methodology_it->IsUsedPolls() )
						{
							$this->drawChanges();
						}
						*/
					echo '</div>';
				echo '</div>';
		
				echo '<div style="float:right;width:48%;">';
					echo '<div class="section" id="function1">';
						if ( !$project_it->HasProductSite() )
						{	
							if ( $project_it->IsPublicArtefacts() )
							{
								$this->drawFiles();
							}
							
						}

						$this->drawRequests();
						
						if ( $project_it->IsUsedPolls() )
						{
							$this->drawPolls();
						}

						/*
						if ( !$project_it->IsPublicArtefacts() || $methodology_it->IsUsedPolls() )
						{
							$this->drawChanges();
						}
						*/
					echo '</div>';
				echo '</div>';
	
			echo '</div>';
			
			echo '<div id="user_actions">';
				$this->drawAdditional();
			echo '</div>';

		echo '</div>';

		echo '<div style="clear:both;width:100%;"></div>';
	}
	
	function getActions()
	{
		global $project_it, $user_it;
		
		$actions = array();

		if ( !$project_it->HasUserAccess($user_it->getId()) ) return $actions;

		array_push($actions, array(
			'url' => '/pm/'.$project_it->get('CodeName').'/',
			'name' => translate('Управление проектом'),
			'title' => text('procloud612')
			));

		if ( !$project_it->IsPublic() )
		{
			array_push($actions, array(
				'url' => '/room/'.$project_it->get('CodeName').'/action/publish',
				'name' => translate('Опубликовать'),
				'title' => text('procloud613')
				));
		}
		else
		{
			array_push($actions, array(
				'url' => '/room/'.$project_it->get('CodeName').'/action/publish',
				'name' => translate('Настройки'),
				'title' => text('procloud614')
				));
		}
		
		return $actions;
	}
	
	function drawAdditional()
	{
		global $model_factory, $project_it, $user_it;
		
		$page = $this->getPage();

		if ( !$project_it->HasProductSite() )
		{
			$page->drawShareActionBox();
		}
			
		if ( $project_it->IsPublicParticipants() )
		{
			echo '<div class="action_box">';
				$page->drawGreyBoxBegin();
	
				echo '<div id="title">';
					echo translate('Участники');
				echo '</div>';
	
		 		$part_it = $project_it->getUserIt();
		 		
				echo '<div>';
			 		while( !$part_it->end() )
			 		{
			 			$this->drawUser( $part_it );
			 			$part_it->moveNext();
			 		}
				echo '</div>';
	
				$page->drawGreyBoxEnd();
			echo '</div>';
		}
						
		echo '<div class="action_box">';
			$page->drawGreyBoxBegin();

			echo '<div id="title">';
				echo translate('Пользователи');
			echo '</div>';

	 		$sql = " SELECT u.* FROM co_ProjectSubscription s, cms_User u " .
	 			   "  WHERE s.Project = ".$project_it->getId().
	 			   "    AND s.SystemUser = u.cms_UserId ".
	 			   "    AND u.PhotoExt IS NOT NULL ".
	 			   "  ORDER BY RAND() DESC LIMIT 18 ";
	 			    
	 		$user = $model_factory->getObject('cms_User');
	 		$cust_it = $user->createSQLIterator($sql);
	 		
			echo '<div>';
	
	 		while( !$cust_it->end() )
	 		{
	 			$this->drawUser( $cust_it, 24 );
	 			$cust_it->moveNext();
	 		}
	
			echo '</div>';

			echo '<div style="clear:both;"></div>';
			
			$count = $project_it->getSubscribersCount();
			if ( $count > $cust_it->count() )
			{
				echo translate('Всего пользователей').': '.$count;
				echo '<br/>';
			}

			$page->drawGreyBoxEnd();
		echo '</div>';

		if ( !$project_it->HasProductSite() )
		{
			echo '<div class="action_box">';
				$page->drawGreyBoxBegin();
	
				echo '<div id="title">';
					echo translate('Обсуждения');
				echo '</div>';
	
				$comment = $model_factory->getObject('Comment');
				
				$comment_it = $comment->getByEntities(
					array('helppage', 'request', 'pmblogpost', 'knowledgebase', 'projectpage', 'question'), 10 );
				
				while ( !$comment_it->end() )
				{
					$object_it = $comment_it->getAnchorIt();
	
					if ( $object_it->count() < 1 )
					{
						$comment_it->moveNext();
						continue;
					}
					
					echo '<div id="comcount" title="'.translate('Комментарии').'">';
						echo '<a href="'.ParserPageUrl::parse($object_it).'#comment">'.$comment->getCount($object_it).'</a>';
					echo '</div>';
	
					echo '<div style="float:left;">'.$comment_it->getDateTimeFormat('RecordModified').'</div>';
						
					echo '<div style="clear:both;padding-bottom:16px;">'.
							$object_it->getWordsOnly('Caption', 5).'</div>';
	
					$comment_it->moveNext();
				}
	
				$page->drawGreyBoxEnd();
			echo '</div>';
		}
		
		echo '<div class="action_box">';
			$page->drawGreyBoxBegin();

			echo '<div id="title">';
				echo translate('Похожие проекты');
			echo '</div>';

			$project = $model_factory->getObject('pm_Project');
			$sql = " SELECT p.* " .
				   "   FROM pm_Project p, pm_PublicInfo i" .
				   "  WHERE p.pm_ProjectId = i.Project ".
				   "    AND i.IsProjectInfo = 'Y' ".
				   "    AND (SELECT COUNT(1) FROM pm_ProjectTag t1, pm_ProjectTag t2 " .
				   "				 WHERE p.pm_ProjectId = t1.Project" .
				   "				   AND t1.Caption = t2.Caption " .
				   "    			   AND t2.Project = ".$project_it->getId().") > 1";
				   
			$other_it = $project->createSQLIterator( $sql );
			
			while ( !$other_it->end() )
			{
				if ( $other_it->getId() != $project_it->getId() )
				{
					echo '<div><a href="'.ParserPageUrl::parse($other_it).'">'.
						$other_it->getWordsOnly('Caption', 5).'</a></div>';
				}
				$other_it->moveNext();				
			}

			$page->drawGreyBoxEnd();
		echo '</div>';
	}
	
	function drawUser( $user_it, $width = 36 )
	{
		global $parser;
		
 		echo '<a href="'.ParserPageUrl::parse($user_it).'" title="'.$user_it->getDisplayName().'">'.
 			'<img class="photo" style="width:'.$width.'px;height:'.$width.'px;padding:2px;" src="'.
 				ParserPageUrl::getPhotoUrl($user_it).'" border=0></a> ';
	}
	
	function drawBlog()
	{
		global $model_factory, $project_it;
		
		$page = $this->getPage();

		$post = $model_factory->getObject('BlogPost');
		$comment = $model_factory->getObject('Comment');
	
		$post_it = $post->getByRefArray(
			array( 'Blog' => $project_it->get('Blog') ), 4);
		
		echo '<div class="'.($post_it->count() > 0 ? 'active' : 'nonactive').'" id="button" name="but1">';
			echo '<div id="lt">&nbsp;</div>';
			echo '<div id="bd"><div id="txt">'.
					'<a href="javascript: choosebutton(1);">'.translate('Новости').'</a>'.
				 '</div></div>';
			echo '<div id="rt"><a href="javascript: choosebutton(1);" style="text-decoration:none;">&nbsp;&nbsp;&nbsp;&nbsp;</a></div>';
		echo '</div>';

		echo '<div style="clear:both;"></div>';
		echo '<br/>';
		
		echo '<div class="description" id="desc1" style="display:'.($post_it->count() > 0 ? 'block' : 'none').'">';
			$page->drawWhiteBoxBegin();
		
			while ( !$post_it->end() && $post_it->getPos() < 3 )
			{
				echo '<div id="comcount" title="'.translate('Комментарии').'">';
					echo '<a href="'.ParserPageUrl::parse($post_it).'#comment">'.$comment->getCount($post_it).'</a>';
				echo '</div>';

				echo '<div>';
					echo '<a class="post" href="'.ParserPageUrl::parse($post_it).'">'.
						$post_it->getWordsOnly('Caption', 7).'</a>';
				echo '</div>';
				
				echo '<div style="float:left;">';
				echo '</div>';
				
				echo '<br/>';

				$post_it->moveNext();
			}	
			
			if ( $post_it->count() < 1 )
			{			
				echo text('procloud509');
			}
			else if ( $post_it->count() > 3 )
			{
				if ( $project_it->HasProductSite() )
				{
					$url = CoController::getProductUrl($project_it->get('CodeName')).'news';
				}
				else
				{
					$url = '/news/'.$project_it->get('CodeName');
				}
				
				$page->drawBlackButton( 
					'<a href="'.$url.'">'.translate('Все новости').'</a>' );
			}
			
			if ( $this->canmanage )
			{
				$blog_it = $project_it->getRef('Blog');
				$page->drawBlackButton( 
					'<a href="/pm/'.$project_it->get('CodeName').'/'.$blog_it->getViewUrl().'">'.translate('Редактировать').'</a>' );
			}

			$page->drawWhiteBoxEnd();

			echo '<br/>';

		echo '</div>';
	}
	
	function drawFiles()
	{
		global $project_it, $model_factory;
		
		$page = $this->getPage();
		$artefact = $model_factory->getObject('pm_Artefact');
		$artefact_it = $artefact->getLatestDisplayed(4);
		
		echo '<div class="'.($artefact_it->count() > 0 ? 'active' : 'nonactive').'" id="button" name="but2">';
			echo '<div id="lt">&nbsp;</div>';
			echo '<div id="bd"><div id="txt">'.
					'<a href="javascript: choosebutton(2);">'.translate('Файлы').'</a>'.
				 '</div></div>';
			echo '<div id="rt"><a href="javascript: choosebutton(2);" style="text-decoration:none;">&nbsp;&nbsp;&nbsp;&nbsp;</a></div>';
		echo '</div>';

		echo '<div style="clear:both;"></div>';
		echo '<br/>';
		
		echo '<div class="description" id="desc2" style="display:'.($artefact_it->count() > 0 ? 'block' : 'none').';">';
			$page->drawWhiteBoxBegin();
		
			while ( !$artefact_it->end() && $artefact_it->getPos() < 3 )
			{
				echo '<h3>';
					echo '<a href="'.ParserPageUrl::parse($artefact_it).'" title="'.$artefact_it->get('Description').'">'.
							$artefact_it->getDisplayName().'</a>';
				echo '</h3>';
				
				echo '<div>';
					echo translate('Размер').': '.$artefact_it->getFileSizeKb('Content').' Kb, ';
					
					$version = $artefact_it->getVersion();
					if ( $version != '' )
					{
						echo translate('версия').': '.$version.', ';
					}
					
					echo translate('загрузок').': '.$artefact_it->getDownloadsAmount();
				echo '</div>';	
				echo '<br/>';
				
				$artefact_it->moveNext();
			} 		 		

			if ( $artefact_it->count() < 1 )
			{
				echo text('procloud499');

				echo '<div style="clear:both;"></div>';
				echo '<br/>';
			}
			else if ( $artefact_it->count() > 3 )
			{
				$page->drawBlackButton( 
					'<a href="/files/'.$project_it->get('CodeName').'">'.translate('Все файлы').'</a>' );
			}

			if ( $this->canmanage )
			{
				$page->drawBlackButton( 
					'<a href="/pm/'.$project_it->get('CodeName').'/artefacts.php">'.translate('Редактировать').'</a>' );
			}

			$page->drawWhiteBoxEnd();
			
			echo '<br/>';
		echo '</div>';		
	}

	function drawQuestions()
	{
		global $project_it, $model_factory, $user_it;
		
		$page = $this->getPage();
		
		echo '<a name="changes">';
		
		$comment = $model_factory->getObject('Comment');
		$question = $model_factory->getObject('pm_Question');
		$question->defaultsort = ' RecordCreated DESC ';
		$question_it = $question->getLatest( 4 );
		
		echo '<div class="'.($question_it->count() > 0 ? 'active' : 'nonactive').'" id="button" name="but5">';
			echo '<div id="lt">&nbsp;</div>';
			echo '<div id="bd"><div id="txt">'.
					'<a href="javascript: choosebutton(5);">'.translate('Вопросы').'</a>'.
				 '</div></div>';
			echo '<div id="rt"><a href="javascript: choosebutton(5);" style="text-decoration:none;">&nbsp;&nbsp;&nbsp;&nbsp;</a></div>';
		echo '</div>';

		echo '<div style="clear:both;"></div>';
		echo '<br/>';
		
		echo '<div class="description" id="desc5" style="display:'.($question_it->count() > 0 ? 'block' : 'none').';">';
			$page->drawWhiteBoxBegin();
		
			while ( !$question_it->end() && $question_it->getPos() < 3 )
			{
				echo '<div id="comcount" title="'.translate('Комментарии').'">';
					echo '<a href="'.ParserPageUrl::parse($question_it).'">'.$comment->getCount($question_it).'</a>';
				echo '</div>';

				echo '<div>';
					echo '<a class="post" href="'.ParserPageUrl::parse($question_it).'">'.
						$question_it->getWordsOnly('Caption', 7).'</a>';
				echo '</div>';

				echo '<div style="clear:both"></div>';				
				echo '<br/>';

				$question_it->moveNext();
			}	
			
			if ( $question_it->count() < 1 )
			{			
				echo text('procloud510');

				echo '<div style="clear:both;"></div>';
				echo '<br/>';
			}
			else if ( $question_it->count() > 3 )
			{
				$page->drawBlackButton( 
					'<a href="/questions/'.$project_it->get('CodeName').'">'.translate('Все вопросы').'</a>' );
			}
		
			$url = '/questions/'.$project_it->get('CodeName').'/action/ask';
			if ( !$user_it->IsReal() )
			{
				$url = "javascript: getLoginForm('".$url."')";
			}

			$page->drawBlackButton( 
				'<a href="'.$url.'">'.translate('Задать вопрос').'</a>' );
		
			if ( $this->canmanage )
			{
				$page->drawBlackButton( 
					'<a href="/pm/'.$project_it->get('CodeName').'/project/question">'.translate('Редактировать').'</a>' );
			}

			$page->drawWhiteBoxEnd();
			echo '<br/>';
		echo '</div>';		
	}
	
	function drawRequests()
	{
		global $model_factory, $project_it, $user_it;
		
		$page = $this->getPage();
		$comment = $model_factory->getObject('Comment');
		
		$request = $model_factory->getObject('pm_ChangeRequest');
		$request->addFilter( new StatePredicate('submitted') );
		$request->addFilter( new RequestAuthorFilter('external') );
		
		$request_it = $request->getLatest( 4 );

		echo '<div class="'.($request_it->count() > 0 ? 'active' : 'nonactive').'" id="button" name="but3">';
			echo '<div id="lt">&nbsp;</div>';
			echo '<div id="bd"><div id="txt">'.
					'<a href="javascript: choosebutton(3);">'.translate('Пожелания').'</a>'.
				 '</div></div>';
			echo '<div id="rt"><a href="javascript: choosebutton(3);" style="text-decoration:none;">&nbsp;&nbsp;&nbsp;&nbsp;</a></div>';
		echo '</div>';

		echo '<div style="clear:both;"></div>';
		echo '<br/>';
		
		echo '<div class="description" id="desc3" style="display:'.($request_it->count() > 0 ? 'block' : 'none').'">';
			$page->drawWhiteBoxBegin();
		
			while ( !$request_it->end() && $request_it->getPos() < 3 )
			{
				echo '<div id="comcount" title="'.translate('Комментарии').'">';
					echo '<a href="'.ParserPageUrl::parse($request_it).'">'.$comment->getCount($request_it).'</a>';
				echo '</div>';

				echo '<div>';
					echo '<a class="post" href="'.ParserPageUrl::parse($request_it).'">'.
						$request_it->getWordsOnly('Caption', 7).'</a>';
				echo '</div>';
				
				echo '<div style="clear:both;"></div>';
				echo '<br/>';
				
				$request_it->moveNext();
			}
			
			if ( $request_it->count() < 1 )
			{
				echo text('procloud511');
			}

			echo '<div style="clear:both;"></div>';
			echo '<br/>';
			
			if ( $request_it->count() > 3 )
			{
				$page->drawBlackButton( 
					'<a href="/requests/'.$project_it->get('CodeName').'">'.translate('Остальные').'</a>' );
			}

			$url = '/requests/'.$project_it->get('CodeName').'/action/report';
			if ( !$user_it->IsReal() )
			{
				$url = "javascript: getLoginForm('".$url."')";
			}

			$page->drawBlackButton( 
				'<a href="'.$url.'">'.translate('Добавить').'</a>' );
			
			if ( $this->canmanage )
			{
				$page->drawBlackButton( 
					'<a href="/pm/'.$project_it->get('CodeName').'/requests.php">'.translate('Редактировать').'</a>' );
			}
			
			$page->drawWhiteBoxEnd();

			echo '<br/>';
		echo '</div>';		
	}

	function drawChanges()
	{
		global $project_it;
		
		$page = $this->getPage();
		
		echo '<a name="changes">';
		
		echo '<div class="nonactive" id="button" name="but4">';
			echo '<div id="lt">&nbsp;</div>';
			echo '<div id="bd"><div id="txt">'.
					'<a href="javascript: choosebutton(4);">'.translate('Журнал изменений').'</a>'.
				 '</div></div>';
			echo '<div id="rt"><a href="javascript: choosebutton(4);" style="text-decoration:none;">&nbsp;&nbsp;&nbsp;&nbsp;</a></div>';
		echo '</div>';

		echo '<div style="clear:both;"></div>';
		echo '<br/>';
		
		echo '<div class="description" id="desc4" style="display:none;">';
			$page->drawWhiteBoxBegin();
		
			$change_it = $project_it->getRecentChangeIt( 10 );
			while ( !$change_it->end() )
			{
				echo '<div style="padding-bottom:6px;">';
					echo $change_it->getDateFormat('RecordCreated');
					echo ' &nbsp; ';
					echo $change_it->get('EntityName').': '.
						$change_it->get('Caption').' ('.$change_it->getChangeKind().')';
				echo '</div>';
				
				$change_it->moveNext();
			}

			$page->drawWhiteBoxEnd();
			echo '<br/>';
		echo '</div>';		
	}
	
	function drawPolls()
	{
		global $model_factory, $project_it;
		
		$page = $this->getPage();
		$poll = $model_factory->getObject('pm_Poll');
		$poll_it = $poll->getPublicIt(4);
		
		echo '<div class="'.($poll_it->count() > 0 ? 'active' : 'nonactive').'" id="button" name="but7">';
			echo '<div id="lt">&nbsp;</div>';
			echo '<div id="bd"><div id="txt">'.
					'<a href="javascript: choosebutton(7);">'.translate('Опросы').'</a>'.
				 '</div></div>';
			echo '<div id="rt"><a href="javascript: choosebutton(7);" style="text-decoration:none;">&nbsp;&nbsp;&nbsp;&nbsp;</a></div>';
		echo '</div>';

		echo '<div style="clear:both;"></div>';
		echo '<br/>';
		
		echo '<div class="description" id="desc7" style="display:'.($poll_it->count() > 0 ? 'block' : 'none').'">';
			$page->drawWhiteBoxBegin();
			$items = 3;
		
			while ( !$poll_it->end() && $items > 0 )
			{
				echo '<div>';
					echo '<a class="post" href="'.ParserPageUrl::parse($poll_it).'">'.
						$poll_it->getWordsOnly('Caption', 7).'</a>';
				echo '</div>';
				
				echo '<div style="clear:both;"></div>';
				echo '<br/>';
				
				$poll_it->moveNext();
				$items--;
			}

			if ( $poll_it->count() < 1 )
			{
				echo text('procloud603');

				echo '<div style="clear:both;"></div>';
				echo '<br/>';
			}

			if ( $this->canmanage )
			{
				$page->drawBlackButton( 
					'<a href="/pm/'.$project_it->get('CodeName').'/polls.php'.'">'.translate('Редактировать').'</a>' );
			}

			if ( $poll_it->count() > 3 )
			{
				$page->drawBlackButton( 
					'<a href="/polls/'.$project_it->get('CodeName').'">'.translate('Остальные').'</a>' );
			}

			$page->drawWhiteBoxEnd();
		echo '</div>';
	}	
}

?>
