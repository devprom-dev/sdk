<?php

/////////////////////////////////////////////////////////////////////////////////
class CoLoggedContent extends CoPageContent
{
	var $script;
	
	function validate()
	{
		global $_REQUEST, $model_factory, $project_it, $user_it;
		
		if ( !$user_it->IsReal() )
		{
			if ( $_REQUEST['Email'] != '' )
			{
				$this->script = '<script type="text/javascript">var email = '.JsonWrapper::encode($_REQUEST['Email']).'; $(document).ready(function(){getJoinForm();});</script>';
			}
			else
			{
				$this->script = '<script type="text/javascript">$(document).ready(function(){getLoginForm("/room");});</script>';
			}
			
			return true;
		}
		
		if ( $_REQUEST['action'] != '' )
		{
			if ( !is_object($project_it) )
			{
				return false;
			}
			
			if ( !$project_it->HasUserAccess($user_it->getId()) )
			{
				return false;
			}

			switch ( $_REQUEST['action'] )
			{
				case 'archive':
					$project_it->modify ( array( 'IsClosed' => 'Y' ) );
					exit(header('Location: /room'));

				case 'repair':
					$project_it->modify ( array( 'IsClosed' => 'N' ) );
					exit(header('Location: /archive'));
			}
		}
		
		return true;
	}

	function getTitle()
	{
		global $_REQUEST;
		
		if ( $_REQUEST['filter'] == 'archive' )
		{
			return translate('Архив проектов').' - '.parent::getTitle();
		}
		else
		{
			return translate('Мои проекты').' - '.parent::getTitle();
		}
	}
	
	function getKeywords()
	{
		return '';
	}

	function draw()
	{
		global $_REQUEST, $model_factory, $user_it;
		
		echo $this->script;
		
		$page = $this->getPage();

		echo '<div style="float:left;">';
			echo '<div id="grbutton" style="width:220px;">';
				echo '<div id="lt">&nbsp;</div>';
				echo '<div id="bd"><div style="padding-top:4px;">';
					if ( $_REQUEST['filter'] == 'archive' )
					{
						echo '<a href="/archive">'.translate('Архив').'</a>';
					}
					else
					{
						echo '<a href="/room">'.translate('Мои проекты').'</a>';
					}
				echo '</div></div>';
				echo '<div id="rt">&nbsp;</div>';
				echo '<div id="an"></div>';
			echo '</div>';
		echo '</div>';

		echo '<div style="clear:both;"></div>';
		echo '<br/>';						

		$project = $model_factory->getObject('pm_Project');
		
		echo '<div id="user_projects">';

			if ( $_REQUEST['action'] == 'publish' )
			{
				$this->drawPublishForm();
			}
			else
			{
				$project->addFilter( new ProjectParticipatePredicate($user_it->getId()) );
				
				if ( $_REQUEST['filter'] == 'archive' )
				{
					$project->addFilter( new ProjectStatePredicate('closed') );
				}
				else
				{
					$project->addFilter( new ProjectStatePredicate('active') );
				}
				
				$it = $project->getAll();
				
				$i = 0;
				
				while ( !$it->end() )
				{
					$session = new PMSession( $it->copy() );
					
					if ( $i % 2 == 0 )
					{
						echo '<div class="user_project" style="margin-right:20px;">';
					}
					else
					{
						echo '<div class="user_project" style="float:right;">';
					}
					
					$page->drawWhiteBoxBegin();
					
					$actions = array();
					$user_has_access = $it->HasUserAccess($user_it->getId());

					if ( $user_has_access )
					{
						array_push($actions, array(
							'url' => '/pm/'.$it->get('CodeName').'/',
							'name' => translate('Управление'),
							'title' => text('procloud612')
							));
	
						if ( !$it->IsPublic() )
						{
							array_push($actions, array(
								'url' => '/room/'.$it->get('CodeName').'/action/publish',
								'name' => translate('Опубликовать'),
								'title' => text('procloud613')
								));
						}
						else
						{
							array_push($actions, array(
								'url' => '/room/'.$it->get('CodeName').'/action/publish',
								'name' => translate('Настройки'),
								'title' => text('procloud614')
								));
						}
					}
					elseif ( $it->IsSubscribed() )
					{
						array_push($actions, array(
							'url' => '/project/'.$it->get('CodeName').'/action/leave',
							'name' => translate('Покинуть проект'),
							'title' => text('procloud570')
							));
					}
					
					if ( $it->HasProductSite() )
					{
						array_push($actions, array(
							'url' => CoController::getProductUrl($it->get('CodeName')),
							'name' => translate('Сайт продукта'),
							'title' => translate('Перейти на сайт продукта')
							));
					}
					else if ( $it->IsPublic() )
					{
						array_push($actions, array(
							'url' => ParserPageUrl::parse($it),
							'name' => translate('Сайт проекта'),
							'title' => translate('Перейти на страницу проекта, доступную пользователям')
							));
					}

					if ( $user_has_access )
					{
						$blog_it = $it->getBlogIt();
						
						array_push($actions, array(
							'url' => $blog_it->getNewUrl(),
							'name' => translate('Новость'),
							'title' => translate('Добавить новость по проекту')
							));

						if ( $it->IsActive() )
						{
							array_push($actions, array(
								'url' => '/room/'.$it->get('CodeName').'/action/archive',
								'name' => translate('В архив'),
								'title' => text('procloud563')
								));
						}
						else
						{
							array_push($actions, array(
								'url' => '/room/'.$it->get('CodeName').'/action/repair',
								'name' => translate('Извлечь'),
								'title' => text('procloud564')
								));
						}
					}

					echo '<div id="maininfo" style="width:100%">';
						echo '<div id="title" style="float:left;">';
							echo '<a href="'.ParserPageUrl::parse($it).'">'.$it->getWordsOnly('Caption', 3).'</a>';
						echo '</div>';
						
						echo '<div style="float:right;">';
							echo '<div class="bmi_left"></div>';
							echo '<ul class="button_menu" style="float:left;">';
				    			echo '<li><a style="float:left;width:110px;" href="'.$actions[0]['url'].'" title="'.$actions[0]['title'].'">'.$actions[0]['name'].'</a>';
			        				array_shift($actions);
			        				if ( count($actions) > 0 )
			        				{
					        			echo '<div style="clear:both;"></div><ul>';
			           					echo '<li class="disabled"><a href="javascript: return false;"></a></li>';
	
				        				foreach ( $actions as $action )
				        				{
				            				echo '<li><a href="'.$action['url'].'" title="'.$action['title'].'">'.$action['name'].'</a></li>';
				        				}
				           				echo '<li class="disabled"><div class="bmbi_left"></div><a style="float:left;width:90px;" href="javascript: return false;"></a><div class="bmbi_right"></div></li>';
					        			echo '</ul>';
			        				}
							    echo '</li>';
							echo '</ul>';				
							echo '<div class="bmi_right"></div>';
						echo '</div>';
					echo '</div>';
					
					echo '<div style="clear:both;"></div>';
					
					echo '<div id="body" style="width:100%;">';
						$has_iteration = $this->drawProject( $it );

						$meth_it = $it->getMethodologyIt();
						$alignment = '';
						
						if ( !$user_has_access || !$meth_it->HasPlanning() || !$has_iteration )
						{
							echo '<div style="clear:both;"></div>';
		
							$alignment = 'float:left;';
							$items = 3;
						}
						else
						{
							$items = 2;
						}
		
						if ( !$this->drawNews( $it, $items, $alignment ) && $alignment != '' )
						{
							$alignment = 'float:left;';
						}
						else
						{
							$alignment = '';
						}
						
						$this->drawTimeline( $it, $items, $alignment );
					echo '</div>';
	
					if ( !$it->IsPublic() )
					{
						echo '<div style="clear:both;color:grey;">'.text('procloud639').'</div>';
					}
					
					$page->drawWhiteBoxEnd();
				
					echo '</div>';
					
					$i++;
					
					$it->moveNext();
				}
				
				if ( $it->count() < 1 )
				{
					$page->drawWhiteBoxBegin();
					
					if ( $_REQUEST['filter'] == 'archive' )
					{
						echo text('procloud548');
					}
					else
					{
						echo text('procloud547');
					}
					
					$page->drawWhiteBoxEnd();
					
					echo '<br/>';
				}
				
				if ( $it->count() != 1 )
				{
					echo '<div style="clear:both;"></div>';
				}
	
				$this->drawDiscussions();
				
				$this->drawProjectsNews();
			}
	
		echo '</div>';
		
		?>
		<script type="text/javascript">
			$('.button_menu > li').bind('mouseover', dropdown_open)
		    $('.button_menu > li').bind('mouseout',  dropdown_timer)
			document.onclick = dropdown_close;
		</script>
		<?
	}
	
	function drawProject( $project_it )
	{
		global $user_it, $model_factory;
		
		$iteration = $model_factory->getObject('Iteration');
		$iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::CURRENT) );

		$iteration_it = $iteration->getAll();
		$meth_it = $project_it->getMethodologyIt();

		if ( $project_it->HasUserAccess($user_it) && $meth_it->HasPlanning() && 
			 is_object($iteration_it) && $iteration_it->count() > 0 )
		{
			$this->drawIteration( $project_it, $iteration_it );
			return true;
		}
		else
		{
			$release = $model_factory->getObject('Release');
			$release->addFilter( new ReleaseTimelinePredicate('current') );
			
			$release_it = $release->getAll();
			if ( is_object($release_it) && $release_it->count() > 0 )
			{
				$this->drawRelease( $project_it, $release_it );
			}
		}
		
		return false;
	}
	
	function drawIteration( $project_it, $iteration_it )
	{
		global $user_it, $language;
		
		$language = getLanguage();
		$page = $this->getPage();

		echo '<div id="iteration" style="float:left;">';
			echo '<div id="description">';
				echo translate('Текущая итерация').' '.$iteration_it->getFullNumber();
			echo '</div>';
			echo '<div id="dates">';
				echo '<div id="start">';
					echo $language->getDateFormattedShort($iteration_it->get('StartDate'));
				echo '</div>';
				echo '<div id="finish">';
					echo $language->getDateFormattedShort($iteration_it->get('FinishDate'));
				echo '</div>';
				echo '<div id="duration">';
					$value = $iteration_it->getLeftCapacity();
					echo $value.' '.$language->getDaysWording($value);
				echo '</div>';
			echo '</div>';
			echo '<div id="chart">';
				echo '<img width=243 height=150 src="/pm/'.$project_it->get('CodeName').
					'/chartburndown.php?release_id='.$iteration_it->getId().'&width=243">';
			echo '</div>';
			
		echo '</div>';
	}

	function drawRelease( $project_it, $release_it )
	{
		global $user_it;
		
		$language = getLanguage();
		$page = $this->getPage();
		return;
		echo '<div id="iteration">';
			echo '<div id="description">';
				echo translate('Текущая версия').' '.$release_it->getDisplayName();
			echo '</div>';
			echo '<div id="chart">';
			echo '</div>';
		echo '</div>';
	}
	
	function drawProgress( $percent )
	{
		echo '<div id="progress">';
			echo '<div id="plt">&nbsp;</div>';
			echo '<div id="full" style="width:'.($percent - 2).'%;">&nbsp;</div>';
			echo '<div id="empty" style="width:'.(100 - $percent - 2).'%;">&nbsp;</div>';
			echo '<div id="prt">&nbsp;</div>';
		echo '</div>';
	}
	
	function drawTimeline( $project_it, $items = 2, $alignment )
	{ 
		echo '<div id="timeline" style="'.$alignment.'">';
			echo '<div id="header">';
				if ( !$project_it->IsPublic() )
				{
						echo '<a href="/pm/'.$project_it->get('CodeName').'/project/log">'.translate('Журнал').'</a>';
				}
				else
				{
					if ( $project_it->HasProductSite()  )
					{
						echo translate('Журнал');
					}
					else
					{
						echo '<a href="/main/'.$project_it->get('CodeName').'#changes">'.translate('Журнал').'</a>';
					}
				}
			echo '</div>';

			$change_it = $project_it->getRecentChangeIt( $items );
			while ( !$change_it->end() )
			{
				echo '<div id="post">';
					echo '<div id="cloud">';
						echo '<div id="begin">';
						echo '</div>';
						echo '<div id="middle">';
							echo '<div id="text">';
								echo $change_it->getDateFormat('RecordCreated');
								echo ' ';
								echo $change_it->get('EntityName').': '.
									$change_it->get('Caption').' ('.$change_it->getChangeKind().')';
							echo '</div>';
							echo '<div id="tag">';
							echo '</div>';
						echo '</div>';
						echo '<div id="end">';
						echo '</div>';
					echo '</div>';
				echo '</div>';
				
				$change_it->moveNext();
			}

		echo '</div>';
	}
	
	function drawNews( $project_it, $items = 2, $alignment )
	{
		$post_it = $project_it->getPostIt( $items );

		if ( $post_it->count() < 1 )
		{
			return false;
		}
		
		echo '<div id="blog" style="'.$alignment.'">';
			echo '<div id="header">';
				if ( !$project_it->IsPublic() )
				{
					echo '<a href="/pm/'.
						$project_it->get('CodeName').'/index.php?blog_id='.$project_it->get('Blog').'">'.
							translate('Новости').'</a>';
				}
				else
				{
					if ( $project_it->HasProductSite() )
					{
						echo '<a href="'.CoController::getProductUrl($project_it->get('CodeName')).'news">'.
							translate('Новости').'</a>';
					}
					else
					{
						echo '<a href="'.'/news/'.$project_it->get('CodeName').'">'.translate('Новости').'</a>';
					}
				}
			echo '</div>';

			while ( !$post_it->end() )
			{
				echo '<div id="post">';
					echo '<div id="cloud">';
						echo '<div id="begin">';
						echo '</div>';
						echo '<div id="middle">';
							echo '<div id="text">';
								echo $post_it->getDateFormat('RecordCreated').' ';
								echo '<a href="'.ParserPageUrl::parse($post_it).'">'.$post_it->getDisplayName().'</a>';
							echo '</div>';
							echo '<div id="tag">';
							echo '</div>';
						echo '</div>';
						echo '<div id="end">';
						echo '</div>';
					echo '</div>';
				echo '</div>';
				
				$post_it->moveNext();
			}

		echo '</div>';
		
		return true;
	}
	
	function drawDiscussions()
	{
		global $user_it, $model_factory, $project_it;
		
		$page = $this->getPage();
		$project = $model_factory->getObject('pm_Project');
		$project_it = $project->getUserRelatedProjects( $user_it->getId() );
		
		echo '<div id="project_news" style="float:left;">';
			$page->drawGreyBoxBegin();
		
			echo '<div id="title">';
				echo translate('Обсуждения в проектах');
			echo '</div>';

			echo '<div id="content">';
				$comment = $model_factory->getObject('Comment');
				$project = $model_factory->getObject('pm_Project');

				$projects = $project_it->idsToArray();
				if ( count($projects) < 1 )
				{
					array_push($projects, 0);
				}
				
				$sql = " SELECT c.ObjectId, c.ObjectClass, " .
					   "		MAX(c.RecordModified) as RecordModified," .
					   "        (SELECT COUNT(1) FROM Comment c2 " .
					   "	      WHERE c2.ObjectId = c.ObjectId " .
					   "            AND c2.ObjectClass = c.ObjectClass ) as CommentsCount," .
					   "		i.Project " .
					   "   FROM Comment c, pm_PublicInfo i, pm_Project p".
					   "  WHERE c.ObjectClass IN ('pmblogpost', 'request', 'knowledgebase', 'question', 'helppage')" .
					   "    AND c.VPD = i.VPD " .
					   "    AND p.pm_ProjectId = i.Project" .
					   "    AND p.pm_ProjectId IN (".join(',', $projects).") ".
					   "  GROUP BY c.ObjectId, c.ObjectClass, i.Project ".
					   "  ORDER BY RecordModified DESC LIMIT 5 ";
				
				$comment_it = $comment->createSQLIterator( $sql );
				
				while ( !$comment_it->end() )
				{
					$session = new PMSession($comment_it->get('Project'));
					$project_it = $project->getExact($comment_it->get('Project'));
					
					$object_it = $comment_it->getAnchorIt();

					if ( $object_it->count() > 0 )
					{
						$last_it = $comment->getLastCommentIt( $object_it );
 						$author_it = $user_it->object->getExact($last_it->get('AuthorId'));
						
						echo '<div id="row">';
							echo '<div id="title">';
								echo ' <a id="project" href="'.ParserPageUrl::parse($project_it).'">'.$project_it->getDisplayName().'</a> | ';
								echo '<a id="post" href="'.ParserPageUrl::parse($object_it).'">'.$object_it->getWordsOnly('Caption', 6).'</a>';
							echo '</div>';
							echo '<div id="basement">';
								echo $comment_it->getDateTimeFormat('RecordModified');
								echo ' <a class="author" href="'.ParserPageUrl::parse($author_it).'">'.$author_it->getDisplayName().'</a>';
							echo '</div>';
						echo '</div>';
					}

					$comment_it->moveNext();
				}				
			echo '</div>';
			
			$page->drawGreyBoxEnd();
			
			echo '<br/>';
		echo '</div>';
	}
	
	function drawProjectsNews()
	{
		global $user_it, $model_factory, $project_it;
		
		$page = $this->getPage();
		$project = $model_factory->getObject('pm_Project');

		$project_it = $project->getUserRelatedProjects( $user_it->getId() );

		echo '<div id="project_news" style="float:right;">';
			$page->drawGreyBoxBegin();

			echo '<div id="title">';
				echo translate('Новости моих проектов');
			echo '</div>';

			echo '<div id="content">';
				if ( $project_it->count() > 0 )
				{
					$post_it = $project_it->getRelatedPostIt( 5 );

					while ( !$post_it->end() )
					{
						$project_it = $project->getExact($post_it->get('Project'));
						$session = new PMSession( $project_it );
						
						//$author_it = $user_it->object->getExact($post_it->get('AuthorId'));
						
						echo '<div id="row">';
							echo '<div id="title">';
								echo '<a id="project" href="'.ParserPageUrl::parse($project_it).'">'.$project_it->getDisplayName().'</a> | ';
								echo '<a id="post" href="'.ParserPageUrl::parse($post_it).'">'.$post_it->getWordsOnly('Caption', 6).'</a>';
							echo '</div>';
							echo '<div id="basement">';
								echo $post_it->getDateTimeFormat('RecordCreated');
								//echo ' <a class="author" href="'.ParserPageUrl::parse($author_it).'">'.$author_it->getDisplayName().'</a>';
							echo '</div>';
						echo '</div>';
								
						$post_it->moveNext();
					}
				}
			echo '</div>';

			$page->drawGreyBoxEnd();
		echo '</div>';
	}

	function drawPublishForm()
	{
		global $model_factory, $user_it, $project_it;
		
		$page = $this->getPage();
		$form = new CoPublishForm( $project_it );

		echo '<div class="post">';
			$page->drawWhiteBoxBegin();

			if ( !$user_it->IsReal() )
			{
				echo text('procloud590');
			}
			else
			{
				echo '<div style="float:left;">';
					$form->draw();
				echo '</div>';
			}

			$page->drawWhiteBoxEnd();
		echo '</div>';
	}
}

 ////////////////////////////////////////////////////////////////////////////////
 class CoPublishForm extends CoPageForm
 {
 	function getModifyCaption()
 	{
		$project_it = $this->getObjectIt();

		if ( $project_it->IsPublic() )
		{
 			return translate('Изменение настроек публикации');
		}
		else
		{
 			return translate('Публикация проекта в каталоге');
		}
 	}

 	function getCommandClass()
 	{
 		return 'copublishproject';
 	}
 	
	function getAttributes()
	{
		$project_it = $this->getObjectIt();
		
		$attrs = array ();

		$attrs = array_merge( $attrs, array( 'project' ) );
		
		if ( $project_it->IsPublic() )
		{
			$attrs = array_merge( $attrs, array( 'IsPublic') );
		}
		
		$attrs = array_merge( $attrs, array( 'Description') );

		$attrs = array_merge( $attrs,
			array( 'Tags', 'IsBlog', 'IsParticipants', 'IsArtefacts') 
			);
			
		if ( $project_it->HasProductSite() )
		{
			$attrs = array_merge( $attrs, array( 'IsPublicDocumentation' ) );
		}

		$attrs = array_merge( $attrs, array( 'Template') );
		$attrs = array_merge( $attrs, array( 'IsCustomDesign') );

    	return $attrs;
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Description':
			case 'News':
			case 'FullDescription':
			case 'Tags':
				return 'largetext'; 	

			case 'project':
			case 'Template':
				return 'text'; 	

			case 'IsPublic':
			case 'IsBlog':
			case 'IsParticipants':
			case 'IsKnowledgeBase':
			case 'IsArtefacts':
			case 'IsCustomDesign':
			case 'IsPublicDocumentation':
			case 'IsProductSite':
				return 'char';
		}
	}

	function getAttributeValue( $attribute )
	{
		$project_it = $this->getObjectIt();

		switch ( $attribute )
		{
			case 'project':
				return $project_it->get('CodeName');
				
			case 'News':
				$post_it = $project_it->getPostIt();
				return $post_it->get_native('Content');

			case 'Tags':
				$tag_it = $project_it->getTagsIt();
				if ( $tag_it->count() > 0 )
				{
					return join(', ', $tag_it->fieldToArray('Caption'));
				}
				return '';

			case 'FullDescription':
				if ( $project_it->HasProductSite() )
				{
					$kb_it = $project_it->getSitePageIt('main');
				}
				else
				{
					$kb_it = $project_it->getProductPageIt();
				}
				return $kb_it->get_native('Content');

			case 'Description':
				return $project_it->get_native('Description');

			case 'IsPublic':
				return $project_it->IsPublic() ? 'Y' : 'N';

			case 'IsBlog':
				return $project_it->IsPublicBlog() ? 'Y' : 'N';
				
			case 'IsParticipants':
				return $project_it->IsPublicParticipants() ? 'Y' : 'N';

			case 'IsKnowledgeBase':
				return $project_it->IsPublicKnowledgeBase() ? 'Y' : 'N';

			case 'IsArtefacts':
				return $project_it->IsPublicArtefacts() ? 'Y' : 'N';

			case 'IsPublicDocumentation':
				return $project_it->IsPublicDocumentation() ? 'Y' : 'N';
				
			case 'Template':
				return $project_it->get('Tools');
				
			case 'IsCustomDesign':
				return $project_it->get('Tools') == 'custom' ? 'Y' : 'N';

			case 'IsProductSite':
				return $project_it->HasProductSite() ? 'Y' : 'N';

			default:
				return '';
		}
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}

	function getName( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			case 'Description':
				return translate('Краткое описание проекта');
				
			case 'News':
				return translate('Первая новость по проекту');

			case 'FullDescription':
				return translate('Подробное описание проекта');

			case 'Tags':
				return translate('Тэги проекта');

			case 'project':
				return translate('Название проекта');

			case 'IsBlog':
				return translate('Публиковать новости по проекту');
				
			case 'IsPublic':
				return translate('Проект является публичным');

			case 'IsParticipants':
				return translate('Публиковать состав участников проекта');

			case 'IsKnowledgeBase':
				return translate('Публиковать подробное описание проекта');

			case 'IsPublicDocumentation':
				return translate('Публиковать документацию по продукту');

			case 'IsArtefacts':
				return translate('Позволять загружать файлы');

			case 'Template':
				return translate('Выберите внешний вид страниц проекта');

			case 'IsCustomDesign':
				return translate('Индивидуальный дизайн страниц проекта');

			case 'IsProductSite':
				return translate('Использовать сайт продукта');

			default:
				return parent::getName( $attribute );
				
		}
	}

 	function getDescription( $attribute )
 	{
		$project_it = $this->getObjectIt();
		$uid = new ObjectUID;

 		switch( $attribute )
 		{
			case 'Description':
				return text('procloud591');

			case 'News':
				$post_it = $project_it->getPostIt();
				
				if ( $post_it->count() > 0 )
				{
					return str_replace('%1', $uid->getGotoUrl($post_it), text('procloud592'));
				}
				else
				{
					return str_replace('%1', '/pm/'.$project_it->get('CodeName').
						'/index.php?blog_id='.$project_it->getBlogId(), text('procloud592'));
				}

 			case 'FullDescription':
 				if ( $project_it->HasProductSite() )
 				{
					$kb_it = $project_it->getSitePageIt('main');
 				}
 				else
 				{
					$kb_it = $project_it->getProductPageIt();
 				}
				return str_replace('%1', $uid->getGotoUrl($kb_it), text('procloud593'));

			case 'Tags':
				return text('procloud244');

			case 'IsBlog':
				return text('procloud42');
				
			case 'IsPublic':
				return text('procloud39');

			case 'IsParticipants':
				return text('procloud41');

			case 'IsKnowledgeBase':
				return text('procloud43');

			case 'IsArtefacts':
				return text('procloud46');

			case 'IsPublicDocumentation':
				return text('procloud45');

 			case 'Template':
 				return text('procloud475');

 			case 'IsCustomDesign':
 				return text('procloud617');

 			case 'IsProductSite':
 				return text('procloud619');
 		}
 	}

	function getButtonText()
	{
		$project_it = $this->getObjectIt();
		
		if ( !$project_it->IsPublic() )
		{
			return translate('Опубликовать проект');
		}
		else
		{
			return translate('Сохранить настройки');
		}
	}

	function getPanes()
	{
		return 2;
	}
	
	function drawAttribute ( $attribute, $pane )
	{
		global $project_it;

		switch ( $attribute )
		{
			case 'Template':
			case 'IsCustomDesign':
			case 'IsProductSite':
				if ( $pane == 2 )
				{
					parent::drawAttribute( $attribute );
				}
				break;
							
			default:
				if ( $pane == 1 )
				{
					parent::drawAttribute( $attribute );
				}
		}
	}

	function drawCustomAttribute( $attribute, $value, $tab_index )
	{
		global $tab_index;
		
		if ( $attribute == 'project' )
		{
			$project_it = $this->getObjectIt();
			
			?>
			<input class=input_value disabled value="<? echo $project_it->getDisplayName() ?>" tabindex="<? echo $tab_index ?>">
			<input type="hidden" id="<? echo $attribute; ?>" name="<? echo $attribute; ?>" value="<? echo $project_it->get('CodeName'); ?>">
			<?	
			
			$tab_index++;						
		}
		elseif ( $attribute == 'Template' )
		{
			$templates = CoController::getTemplates();
			$templates = array_merge( array('common'), $templates );
			
			foreach ( $templates as $template )
			{
				echo '<div style="float:left;padding:10px 15px 12px 0;">';

					if ( $template == $value || $value == '' && $template == 'common' )
					{
						$checked = ' checked ';
					}
					else
					{
						$checked = '';
					}
				
					echo '<input type="radio" style="width:18px;" name="'.$attribute.'" value="'.$template.'" '.$checked.'>' .
						'<label><img src="/plugins/procloud/templates/mini/images/'.$template.'.png"></label></input>';
				echo '</div>';
			}
			
			echo '<div style="clear:both;"></div>';
			
			$tab_index++;						
		}
		else
		{
			parent::drawCustomAttribute( $attribute, $value, $tab_index );
		}
	}
 }
 
 ?>
