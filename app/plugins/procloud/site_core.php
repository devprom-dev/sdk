<?php
/*
 * DEVPROM (http://www.devprom.net)
 * state.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */

 use Intercom\IntercomBasicAuthClient; 
		
 $path = dirname(dirname(__FILE__));
 
 include('common.php');
 include('system/c_controller.php');
 include('system/c_co_url.php');

 include(SERVER_ROOT_PATH.'core/views/Page.php');
 include(SERVER_ROOT_PATH.'pm/views/wiki/parsers/WikiConverterRtf.php');
 include(SERVER_ROOT_PATH.'pm/views/wiki/parsers/WikiConverterMPdf.php');
  
 include('system/c_co_wiki_parser.php');
 include('views/c_co_form.php');
 include('views/c_co_login_forms.php');
  
 ////////////////////////////////////////////////////////////////////////////////
 class SiteTableBase
 {
 	function getRtfExportUrl( $wiki_it )
 	{
 		global $project_it;
 		
 		return CoController::getProductUrl($project_it->get('CodeName')).'export/rtf/'.$wiki_it->getId();
 	}
 	
 	function getPdfExportUrl( $wiki_it )
 	{
 		global $project_it;
 		
 		return CoController::getProductUrl($project_it->get('CodeName')).'export/pdf/'.$wiki_it->getId();
 	}

 	function getTitle()
 	{
 		global $project_it;
 		return $project_it->get('Description');
 	}
 	
 	function getDescription()
 	{
 		return $this->getTitle();
 	}

	function drawComments( $object_it )
	{
		global $project_it, $model_factory;
		
		echo '<a name="comments"></a>';

		$script = "javascript: initComments('".$project_it->get('CodeName').
			"', '".get_class($object_it->object)."', '".$object_it->getId()."')";

		echo '<div class="commentsholder" id="comments'.$object_it->getId().'">';
			$comment = $model_factory->getObject2('Comment', $object_it);
			echo '<a id="commentslink" href="'.$script.'">'.translate('Комментарии').'</a> ('.$comment->getCount($object_it).')';
		echo '</div>';
	}

	function initComments( $object_it )
	{
		global $project_it, $model_factory;

		$script = "javascript: initComments('".$project_it->get('CodeName').
			"', '".get_class($object_it->object)."', '".$object_it->getId()."')";

 		echo '<script type="text/javascript">$(document).ready(function() { '.$script.'; });</script>';
	}
	
	function getKeywords()
	{
		return array();
	}

	function getPageWikiIt( $name )
	{
		global $model_factory, $project_it;
		
		$sql = "SELECT p.*, (SELECT COUNT(1) FROM WikiPage t WHERE t.ParentPage = p.WikiPageId) TotalCount " .
				" FROM WikiPage p " .
				"WHERE p.ReferenceName = " .getFactory()->getObject('ProjectPage')->getReferenceName().
				"  AND (SELECT COUNT(1) FROM WikiTag wt, Tag t " .
				"		 WHERE wt.Wiki = p.WikiPageId AND t.TagId = wt.Tag " .
				"		   AND t.Caption IN ('sitepage', '".mysql_real_escape_string($name)."') ) = 2 ".
				"  AND p.Project = ".$project_it->getId().
				" ORDER BY p.WikiPageId ASC";

 		$page = $model_factory->getObject('ProjectPage');
 		$page_it = $page->createSQLIterator( $sql );
 		
 		return $page_it;
	}

	function getMenuItemsOld()
	{
 		global $model_factory, $project_it, $_REQUEST, $user_it;

		$menu_items = array ( 
			'main' => translate('Главная')
		);

		if ( $project_it->IsPublicBlog() )
		{
			$menu_items = array_merge($menu_items, array ( 
				'news' => translate('Новости'),
			) ); 
		}		

		$wiki_it = $project_it->getRef('MainWikiPage');
		$product_feature_it = $wiki_it->getChildrenIt();
		
		if ( $product_feature_it->count() > 0 )
		{
			$menu_items = array_merge($menu_items, array ( 
				'product' => translate('Продукт'),
			) ); 
		}
		
		if ( $project_it->IsPublicArtefacts() )
		{		
			$menu_items = array_merge($menu_items, array ( 
				'download' => translate('Загрузить'),
			) ); 
		}
		
		if ( $project_it->IsPublicDocumentation() )
		{		
			$menu_items = array_merge($menu_items, array ( 
				'docs' => translate('Документация'),
			) ); 
		}

		$menu_items = array_merge($menu_items, array ( 
			'support' => translate('Поддержка'),
			) ); 

		if ( $project_it->IsPublicParticipants() )
		{		
			//$menu_items = array_merge($menu_items, array ( 
			//	'team' => translate('Контакты')
			//) ); 
		}
		
		return 	$menu_items;
	}

	function getMenuItems()
	{
 		global $model_factory, $project_it, $_REQUEST, $user_it;

		$sql = "SELECT p.*," .
				"	   (SELECT MIN(t.Caption) FROM WikiTag wt, Tag t" .
				"		 WHERE wt.Wiki = p.WikiPageId AND t.TagId = wt.Tag" .
				"		   AND t.Caption <> 'sitepage' ) PageType " .
				" FROM WikiPage p " .
				"WHERE p.ReferenceName = " .getFactory()->getObject('ProjectPage')->getReferenceName().
				"  AND EXISTS (SELECT 1 FROM WikiTag wt, Tag t" .
				"			    WHERE wt.Wiki = p.WikiPageId".
				"  			      AND t.TagId = wt.Tag AND t.Caption = 'sitepage' )".
				"  AND p.Project = ".$project_it->getId().
				" ORDER BY p.OrderNum, p.WikiPageId ASC";

 		$page = $model_factory->getObject('ProjectPage');
 		$page_it = $page->createSQLIterator( $sql );
 		
 		while ( !$page_it->end() )
 		{
 			$page = $page_it->get('PageType');
 			
 			switch ( $page )
 			{
 				case 'news':
					if ( !$project_it->IsPublicBlog() )
					{
	 					$page = '';
					}
 					break;

 				case 'team':
 					$page = '';
					if ( !$project_it->IsPublicParticipants() )
					{		
					}
 					break;

 				case 'docs':
					if ( !$project_it->IsPublicDocumentation() )
					{		
	 					$page = '';
					}
 					break;

 				case 'download':
					if ( !$project_it->IsPublicArtefacts() )
					{		
	 					$page = '';
					}
 					break;
 			}
 			
 			if ( $page != '' )
 			{
				$menu_items[$page] = $page_it->getDisplayName();
 			}

 			$page_it->moveNext(); 
 		}
 		
 		return $menu_items;
	}

	function drawPaging( $total_items, $limited_on_page = 10 )
	{
		global $_REQUEST;
		
		if ( $total_items > $limited_on_page )
		{
			if ( $_REQUEST['page'] != '' && $_REQUEST['page'] > 0 )
			{
				echo '<div style="float:left;">';
					echo '<a href="?page='.($_REQUEST['page'] - 1).'">'.translate('Предыдущая страница').'</a>';
				echo '</div>';
			}
	
			if ( $total_items > ($_REQUEST['page'] + 1) * $limited_on_page )
			{
				echo '<div style="float:right;">';
					echo '<a href="?page='.($_REQUEST['page'] + 1).'">'.translate('Следующая страница').'</a>';
				echo '</div>';
			}
		}
	}

	function draw()
	{
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
		
		exit(header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"));
	}
 }

 ////////////////////////////////////////////////////////////////////////////////
 class NewsPageTable extends SiteTableBase
 {
 	function draw()
 	{
 		global $project_it, $_REQUEST, $model_factory;
 		
		echo '<div id="colOne">';
			echo '<div id="content">';

 			$page_it = $this->getPageWikiIt('news');
 			if ( $page_it->count() > 0 && $page_it->get('ContentPresents') == 'Y' )
 			{
		 		echo '<div id="post">';
			 		echo '<div id="entry">';
				 		$parser = new SiteWikiParser( $page_it, $project_it );
						echo $parser->parse();
					echo '</div>';
				echo '</div>';

				echo '<br/>';
 			}

			$post = $model_factory->getObject('BlogPost');
			$comment = $model_factory->getObject('Comment');
			$was_rss_icon = false;
	
			$total = $post->getByRefArrayCount(
				array( 'Blog' => $project_it->get('Blog') ) );
				
			$post_it = $post->getByRefArray(
				array( 'Blog' => $project_it->get('Blog') ), 10, $_REQUEST['page']);
									
			for ( $i = 0; $i < $post_it->count(); $i++ )
			{
				echo '<div class="post">';
				
					echo '<h2 class="title">';
						echo '<a href="'.SitePageUrl::parse($post_it).'">'.
							$post_it->getDisplayName().'</a>';
					echo '</h2>';
					
					$tag_it = $post_it->getTagsIt();
					$tags = array();
					
					while ( !$tag_it->end() )
					{
						$tags[$tag_it->getPos()] = '<a class="tag" href="'.
							CoController::getProductUrl($project_it->get('CodeName')).'news/tag/'.$tag_it->getSearchName().'">'.
								$tag_it->getDisplayName().'</a>';
							
						$tag_it->moveNext();	
					}

					echo '<p class="byline" style="clear:both;"><small>'.
						$post_it->getDateTimeFormat('RecordCreated').' &nbsp; '.join($tags, ', ').'</small></p>';
					
					echo '<div id="entry" style="clear:both;">';
						$parser = new SiteBlogParser($post_it, $project_it);
						
						$more_text = false;
						echo $parser->parse_substr( null, 840, $more_text );
						
						if ( $more_text )
						{
							echo '<div id="more" style="padding-top:6px;"><a href="'.SitePageUrl::parse($post_it).'">'.
								translate('читать дальше').'</a></div>';
						}
			
					echo '</div>';

					echo '<br/>';
					$this->drawComments($post_it);					

					$post_it->moveNext();
	
				echo '</div>';	
			}

			$this->drawPaging( $total, 10 );
			
			echo '</div>';
		echo '</div>';

		$this->drawSideBar();
 	}
 	
 	function drawSideBar()
 	{
 		global $project_it, $model_factory;
 		
		echo '<div id="colTwo">';
			echo '<div id="sidebar">';
				echo '<ul>';
				echo '<li>';
					echo '<h2>'.translate('Подписка').'</h2>';
					echo '<ul>';
						echo '<li>';
							echo '<a href="'.CoController::getProductUrl($project_it->get('CodeName')).'rss" style="float:left;">' .
								'<img border=0 src="/images/atom.png"></a> &nbsp; RSS';
						echo '</li>';
					echo '</ul>';				
				echo '</li>';
				echo '</ul>';				

				echo '<ul>';
				echo '<li>';
					echo '<h2>'.translate('Тэги').'</h2>';
					echo '<ul>';
						$tag = $model_factory->getObject('BlogPostTag');
						$tag_it = $tag->getAllTags();
						
						while ( !$tag_it->end() )
						{
							echo '<li>';
								echo '<a href="'.CoController::getProductUrl($project_it->get('CodeName')).'news/tag/'.$tag_it->getSearchName().'">' .
									$tag_it->getDisplayName().'</a>';
							echo '</li>';
				
							$tag_it->moveNext();
						}
					echo '</ul>';				
				echo '</li>';
				echo '</ul>';				
			echo '</div>';
		echo '</div>';
 	}

 	function getKeywords()
 	{
 		return array (
 			translate('новости'),	
 			translate('читать'),	
 			translate('подписка'),	
 			translate('rss')	
 		);
 	}
 }

 ////////////////////////////////////////////////////////////////////////////////
 class NewsPageTablePage extends SiteTableBase
 {
 	function draw()
 	{
 		global $project_it, $_REQUEST, $model_factory;
 		
		$post = $model_factory->getObject('BlogPost');
		$user = $model_factory->getObject('pm_Participant');
		$comment = $model_factory->getObject('Comment');
		
		$post_it = $post->getExact($_REQUEST['id']);

		if ( $post_it->count() < 1 )
		{
			return;
		}
		
		echo '<div id="content">';
			echo '<div class="post">';
			
				echo '<div style="float:left;">';
					echo '<h2 class="title">';
						echo $post_it->getDisplayName();
					echo '</h2>';
				echo '</div>';
			
				echo '<div id="entry" style="clear:both;">';
					echo '<div style="float:left;margin-top:2px;">';
						echo $post_it->getDateTimeFormat('RecordCreated');

						$user_it = $user->getExact($post_it->get('AuthorId'));
						$user_it = $user_it->getRef('SystemUser');
						
						echo ', <a href="'.SitePageUrl::parse($user_it).'">'.$user_it->getDisplayName().'</a>';
						
					echo '</div>';

					echo '<div style="float:left;margin-left:20px;">';
						echo '<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like ref="top_left" show_faces="false" layout="standard"></fb:like>';
					echo '</div>';

					echo '<div style="clear:both;"></div>';
					echo '<br/>';
										
					echo '<div>';
						$parser = new SiteBlogParser($post_it, $project_it);
						echo $parser->parse();
					echo '</div>';

					echo '<br/>';

					echo '<div style="float:left;">';
						$this->drawComments($post_it);
					echo '</div>';
					echo '<div style="float:left;margin-top:13px;margin-left:20px;">';
						echo '<div class="addthis_toolbox addthis_default_style">';
						echo '<a href="http://www.addthis.com/bookmark.php?v=250&amp;username=devprom" class="addthis_button_compact">Поделиться</a>';
						echo '<span class="addthis_separator">|</span>';
						echo '<a class="addthis_button_facebook"></a>';
						echo '<a class="addthis_button_myspace"></a>';
						echo '<a class="addthis_button_google"></a>';
						echo '<a class="addthis_button_twitter"></a>';
						echo '</div>';
						echo '<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=devprom"></script>';
					echo '</div>';

					echo '<div style="clear:both;"></div>';
				echo '</div>';
	
	 			$this->initComments($post_it);

				$post_it = $post_it->getWithSameTags( 3 );
				
				if ( $post_it->count() > 0 )
				{
					echo '<div style="padding-top:18px;">';
						echo '<h4>'.translate('Другие новости по этой теме').':</h4>';
					echo '</div>';
				}
				
				while ( !$post_it->end() )
				{
					echo '<div>';
						echo '<a href="'.SitePageUrl::parse($post_it).'">'.$post_it->getDisplayName().'</a>';
					echo '</div>';
						
					$post_it->moveNext();
				}
			echo '</div>';
			
			
		echo '</div>';
 	}
 	
 	function getTitle()
 	{
 		global $_REQUEST, $_SERVER, $model_factory;
 		
		$post = $model_factory->getObject('BlogPost');
		$post_it = $post->getExact($_REQUEST['id']);

		if ( $post_it->count() > 0 )
		{
			return $post_it->getDisplayName();
		}
		else
		{
			header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
			exit(header('Location: /404'));
		}
 	}

 	function getKeywords()
 	{
 		return array (
 			translate('новости'),	
 			translate('читать'),	
 			translate('подписка'),	
 			translate('rss')	
 		);
 	}
 }
 
 ////////////////////////////////////////////////////////////////////////////////
 class ProductTagsPage extends SiteTableBase
 {
 	function draw()
 	{
 		global $project_it, $_REQUEST, $model_factory;
 		
		echo '<div id="content">';

			$blog_it = $project_it->getRef("Blog");
			$post_it = $blog_it->getPostsByTag($_REQUEST['tag']);
			$comment = $model_factory->getObject('Comment');
									
			for ( $i = 0; $i < $post_it->count(); $i++ )
			{
				echo '<div class="post">';
				
					echo '<h2 class="title">';
						echo '<a href="'.SitePageUrl::parse($post_it).'">'.
								$post_it->getDisplayName().'</a>';
					echo '</h2>';
					
					echo '<p class="byline" style="clear:both;"><small>'.$post_it->getDateTimeFormat('RecordCreated').' ';
					
					$tag_it = $post_it->getTagsIt();
					$tags = array();
					
					while ( !$tag_it->end() )
					{
						$tags[$tag_it->getPos()] = '<a class="tag" href="'.
							CoController::getProductUrl($project_it->get('CodeName')).'news/tag/'.$tag_it->getSearchName().'">'.
								$tag_it->getDisplayName().'</a>';
							
						$tag_it->moveNext();	
					}

					echo join($tags, ', ').'</small></p>';

					echo '<div id="entry" style="clear:both;">';
						$parser = new SiteBlogParser($post_it, $project_it);
						
						$more_text = false;
						echo $parser->parse_substr( null, 320, $more_text );
					echo '</div>';
						
					if ( $more_text )
					{
						echo '<div id="more"><a href="'.SitePageUrl::parse($post_it).'">'.
							translate('читать дальше').'</a></div>';
					}
		
					echo '<br/>';
					$this->drawComments( $post_it );

					$post_it->moveNext();
	
				echo '</div>';	
			}

		echo '</div>';
 	}
 	
 	function getTitle()
 	{
 		global $_REQUEST, $model_factory;
 		
 		$tag = $model_factory->getObject('Tag');
 		$tag_it = $tag->getExact( $_REQUEST['tag'] );
 		
 		if ( $tag_it->count() > 0 )
 		{
 			return $tag_it->getDisplayName();
 		}
		else
		{
			return parent::getTitle();
		}
 	}

 	function getKeywords()
 	{
		global $_REQUEST, $project_it;

 		return array (
			$project_it->utf8towin($_REQUEST['tag']),
 			translate('новости'),	
 			translate('подписка')
 		);
 	}
 }

 ////////////////////////////////////////////////////////////////////////////////
 class ArtefactsTable extends SiteTableBase
 {
 	var $script;
 	
 	function ArtefactsTable()
 	{
 		global $project_it, $model_factory, $methodology_it, $_REQUEST, $user_it;
 		
 		if ( $_REQUEST['id'] != '' )
 		{
			 $artefact = $model_factory->getObject('pm_Artefact');
			 if ( $_REQUEST['version'] != '' )
			 {
			 	$artefact->addFilter( new ArtefactVersionFilter($_REQUEST['version']) );
			 }
			 $artefact_it = $artefact->getExact($_REQUEST['id']);
			 
			 if ( $artefact_it->count() < 1 )
			 {
			 	die();
			 }
			 
			 if ( $_REQUEST['iid'] == '' && $artefact_it->IsAuthorizedDownload() && !$user_it->IsReal() )
			 {
				$this->script = '<script type="text/javascript">'.
					'var url = '.JsonWrapper::encode($_REQUEST['id'].'?version='.$_REQUEST['version']).'; '.
					'$().ready(function(){authorizedDownload(url);});'.
					'</script>';
				return;
			 }
			 
			 if ( $this->paymentRequired($_REQUEST['iid'], $artefact_it) ) die();

			 $action = $model_factory->getObject('pm_DownloadAction');
			 $action->process($artefact_it->getId(), 'pm_Artefact');
			 
			 if ( is_object($user_it) && $user_it->IsReal() )
			 {
				 $sub = $model_factory->getObject('co_ProjectSubscription');
		
				 $it = $sub->getByRefArray( 
					array( 'Project' => $project_it->getId(), 
						   'SystemUser' => $user_it->getId() ) );
		
				 if ( $it->count() < 1 )
				 {
					$sub->add_parms( 
						array( 'Project' => $project_it->getId(), 
							   'SystemUser' => $user_it->getId() ) );
				 }
				 
				 $settings = $model_factory->getObject('cms_SystemSettings');
				 $settings_it = $settings->getAll();
				  
				 $mail = new HtmlMailbox;
				 $mail->appendAddress('marketing@devprom.ru');
				 
				 $body = 'Пользователь %1 (%4) загрузил файл: %3<br/><br/>%5<br/>';
				 
				 $body = str_replace('%1', $user_it->getDisplayName(), $body);
				 $body = str_replace('%4', $user_it->get('Email'), $body);
				 $body = str_replace('%3', $artefact_it->getFileName('Content'), $body);
				 $body = str_replace('%5', $user_it->getHtmlDecoded('Description'), $body);
				 
				 $mail->setBody($body);
				 $mail->setSubject( 'Загружен файл: '.$artefact_it->getFileName('Content') );
				 $mail->setFrom($settings_it->getHtmlDecoded('AdminEmail'));
				  
				 $mail->send();
				 
				 $intercom = IntercomBasicAuthClient::factory(array(
				    'app_id' => 'yv4mj125',
				    'api_key' => 'c65c2cd2d0ea40f19c66a367bfc8cb472c786794'
				 ));
				 
				 $user_data = array(
				    "email" => $user_it->get('Email'),
				 	"name" => $user_it->get('Caption'),
				    "last_request_at" => time(),
				 	"custom_data" => array(
				 		"host_name" => 'download.myalm.ru'
				 	),
				 	"host_name" => 'download.myalm.ru'
				 );
	
				 try {
				    $user = $intercom->createUser($user_data);
				 }
				 catch (Exception $e) {
				 	$mail->setBody(get_class($e).': '.$e->getMessage().'<br/><br/>'.$e->getTraceAsString());
				 	$mail->setSubject( 'Ошибка загрузки файла: '.$artefact_it->getFileName('Content') );
				 	$mail->setFrom($settings_it->getHtmlDecoded('AdminEmail'));
				 	$mail->send();
				 }
			 }

			 $downloader = new Downloader;
			
			 $parts = pathinfo($artefact_it->getFileName('Content'));
			  
			 $title = $_REQUEST['version'] != '' 
			 	? $parts['filename'].'.'.$_REQUEST['version'].'.'.$parts['extension']
			 	: $artefact_it->getFileName('Content');
			 	 
		  	 $downloader->echoFile(SERVER_FILES_PATH.$artefact->getClassName().'/'.
		  	 	basename($artefact_it->getFilePath( 'Content' )), 
			 		$title,	$artefact_it->getFileMime( 'Content' ));
			 			
			 die();
 		}
 	}
 	
 	function paymentRequired( $iid, $artefact_it )
 	{
 		return false;
 	}
 	
 	function drawScript()
 	{
 		echo $this->script;
 	}
 	
 	function draw()
 	{
 		global $project_it, $model_factory, $methodology_it;
 		
 		$uid = new ObjectUid;
 		$uid->project_it = $project_it;
 		
		$artefact = $model_factory->getObject('pm_Artefact');
		$artefact->defaultsort = ' (SELECT k.OrderNum FROM pm_ArtefactType k WHERE k.pm_ArtefactTypeId = t.Kind) ASC, t.Kind, t.RecordModified DESC';
		
		$artefact_it = $artefact->getByRefArray(
			array ( 'Project' => $project_it->getId(), "IFNULL(IsArchived, 'N')" => 'N' ) );
		
		$this->drawScript();
		
		echo '<div id="colOne">';
		echo '<div id="content">';
		
 			$root_it = $this->getPageWikiIt('download');
			if ( $root_it->count() > 0 && $root_it->get('ContentPresents') == 'Y' )
			{
		 		echo '<div class="post">';
			 		echo '<div id="entry">';
						$parser = new SiteWikiParser($root_it, $project_it);
						echo $parser->parse();
			 		echo '</div>';
	 			echo '</div>';
			}
		
			$prev_kind = 0;
			$visible_kinds = array();
			
			while ( !$artefact_it->end() )
			{
				if ( $prev_kind != $artefact_it->get('Kind') )
				{
					$type_it = $artefact_it->getRef('Kind');
					
					if ( $type_it->get('IsDisplayedOnSite') == 'N' )
					{
						$artefact_it->moveNext();
						continue;
					}
					
					if ( $prev_kind != 0 )
					{
						echo '<div style="padding-top:40px;">';
						echo '</div>';
					}
	
					echo '<div class="post">';
						echo '<h2 class="title">';
							echo $type_it->getDisplayName();
							echo '<a name="'.$type_it->getId().'"></a>';
						echo '</h2>';
					echo '</div>';
					
					$prev_kind = $type_it->getId(); 
					array_push($visible_kinds, $prev_kind);
				}
				
				echo '<div class="post">';
					echo '<div id="entry">';
						echo '<a href="'.SitePageUrl::parse($artefact_it).'">'.
							$artefact_it->getDisplayName().'</a> ';
	
						echo '<div>'.$artefact_it->getHtml('Description').'</div>';
					echo '</div>';
					
					echo '<div id="version">';
						echo translate('Версия').': '.$artefact_it->getVersion().', '.
							translate('объем').': '.$artefact_it->getFileSizeKb('Content').' Kb, '.
								translate('загрузок').': '.$artefact_it->getDownloadsAmount();
					echo '</div>';
				echo '</div>';
	
				$artefact_it->moveNext();
			}
		
		echo '</div>';
		echo '</div>';
 	
		$artefact_type = $model_factory->getObject('pm_ArtefactType');
		$artefact_type_it = $artefact_type->getInArray('pm_ArtefactTypeId', $visible_kinds);

		echo '<div id="colTwo">';
			echo '<div id="sidebar">';
				echo '<ul>';
				echo '<li>';
					echo '<h2>'.translate('Разделы').'</h2>';
					echo '<ul>';
						while ( !$artefact_type_it->end() )
						{
							if ( $artefact_type_it->get('IsDisplayedOnSite') == 'Y' )
							{
								echo '<li>';
									echo '<a href="#'.$artefact_type_it->getId().'">' .
										$artefact_type_it->getDisplayName().'</a>';
								echo '</li>';
							}
							
							$artefact_type_it->moveNext();
						}
					echo '</ul>';				
				echo '</li>';
				echo '</ul>';				

				echo '<ul>';
				echo '<li>';
					echo '<h2>'.translate('Новые файлы').'</h2>';
					echo '<ul>';
		
					$artefact = $model_factory->getObject('pm_Artefact');
					
					$artefact_it = $artefact->getLatestDisplayed(5);
										
					while ( !$artefact_it->end() )
					{
						echo '<li>';
						echo '<a href="'.SitePageUrl::parse($artefact_it).'">'.
								$artefact_it->getDisplayName().'</a>';
						echo '</li>';	

						$artefact_it->moveNext();
					} 		 		
					echo '</ul>';
				echo '</li>';
				echo '</ul>';
			echo '</div>';
		echo '</div>';
 	}

 	function getKeywords()
 	{
 		return array (
 			translate('скачать'),	
 			translate('бесплатно'),	
 			translate('файлы'),	
 			translate('download'),	
 			translate('file'),
 		);
 	}
 }
 
 ////////////////////////////////////////////////////////////////////////////////
 class DocsTable extends SiteTableBase
 {
 	function draw()
 	{
 		global $project_it, $model_factory, $methodology_it, $_REQUEST;
 		
 		$uid = new ObjectUid;
 		$uid->project_it = $project_it;
 		
		$doc = $model_factory->getObject('HelpPage');
		$doc->defaultsort = 'OrderNum ASC';
		
		echo '<div id="content" style="width:90%;">';

 			$root_it = $this->getPageWikiIt('docs');
			if ( $root_it->count() > 0 && $root_it->get('ContentPresents') == 'Y' )
			{
		 		echo '<div class="post">';
			 		echo '<div id="entry">';
						$parser = new SiteWikiParser($root_it, $project_it);
						echo $parser->parse();
			 		echo '</div>';
	 			echo '</div>';
			}

			echo '<a name="top"/>';
			
			if ( $_REQUEST['id'] != '' )
			{
				$wiki_it = $doc->getExact($_REQUEST['id']);
	
				if ( $wiki_it->count() < 1 )
				{
					die();
				}
				
				echo '<div class="post">';
					echo '<h2 class="title" style="float:left;">';
								echo $wiki_it->getDisplayName();
					echo '</h2>';
					
					echo '<div class="export" style="float:left;padding:6px 0 0 6px;">';
						echo '<a href="'.$this->getPdfExportUrl($wiki_it).'">' .
							'<img border=0 style="width:16px;" src="/images/pdf.png"></a>';
					echo '</div>';
	
					echo '<div class="export" style="float:left;padding:6px 0 0 6px;">';
						echo '<a href="'.$this->getRtfExportUrl($wiki_it).'">' .
							'<img border=0 style="width:16px;" src="/images/msword.png"></a>';
					echo '</div>';

					echo '<div id="entry" style="clear:both;">';
						echo '<div id="index" style="padding: 12px 0 20px 20px">';
							$this->drawIndex($wiki_it);
						echo '</div>';
						echo '<div id="content">';
							$this->drawPage($wiki_it);
						echo '</div>';
					echo '</div>';
				echo '</div>';
			}
			else
			{
				$wiki_it = $doc->getAll();
	
				while ( !$wiki_it->end() )
				{
					if ( !$wiki_it->IsArchived() )
					{
				 		echo '<div class="post">';
					 		echo '<h2 class="title" style="float:left;">';
								echo '<a href="docs/'.$wiki_it->getSearchName().'">'.$wiki_it->getDisplayName().'</a>';
							echo '</h2>';
			
							echo '<div class="export" style="float:left;padding:6px 0 0 6px;">';
								echo '<a href="'.$this->getPdfExportUrl($wiki_it).'">' .
									'<img border=0 style="width:16px;" src="/images/pdf.png"></a>';
							echo '</div>';

							echo '<div class="export" style="float:left;padding:6px 0 0 6px;">';
								echo '<a href="'.$this->getRtfExportUrl($wiki_it).'">' .
									'<img border=0 style="width:16px;" src="/images/msword.png"></a>';
							echo '</div>';
						echo '</div>';
					}
		
					$wiki_it->moveNext();
				}
			}

		echo '</div>';

 	}
 	
 	function drawIndex( $it, $level )
 	{
 		$parent_id = $it->getId();
 		$children_it = $it->getChildrenIt();

 		while ( $children_it->get('ParentPage') == $parent_id )
 		{
	 		echo '<div class="item" style="padding-left:'.($level*30).'px;">';
				echo '<a href="#'.$children_it->getId().'">'.$children_it->getDisplayName().'</a>';
	 		echo '</div>';

			$id = $children_it->getId();
 			$this->drawIndex( $children_it, $level + 1 );
 			
 			$children_it->moveToId( $id );
 			$children_it->moveNext();
 		}
 	}
 	
 	function drawPage( $it, $parent_page )
 	{
 		global $project_it, $model_factory;
 		$comment = $model_factory->getObject('Comment');
 		
 		while ( !$it->end() && $it->get('ParentPage') == $parent_page )
 		{
	 		echo '<div class="post">';
				echo '<a name="'.$it->getId().'"/>';
				if ( $it->get('ParentPage') > 0 )
				{
		 			echo '<h2 class="title">';
						echo $it->getDisplayName();
					echo '</h2>';
				}
	 			echo '<div id="entry">';
					$parser = new SiteWikiParser($it, $project_it);
					echo $parser->parse();

					echo '<div>';
						echo '<br/>';
						echo '<div id="comments" style="float:left;width:80%;">';
							echo $this->drawComments($it);
						echo '</div>';
						echo '<div style="float:right">';
							echo '^ <a href="#top">'.translate('к содержанию').'</a>';
						echo '</div>';
						echo '<div style="clear:both;">';
						echo '</div>';
					echo '</div>';
				echo '</div>';
	 		echo '</div>';

			$id = $it->getId();
			$children_it = $it->getChildrenIt();
			
 			$this->drawPage( $children_it, $id );
 			
 			$it->moveToId( $id );
 			$it->moveNext();
 		}
 	}
 	
 	function getTitle()
 	{
 		global $_REQUEST, $model_factory;
 		
		$doc = $model_factory->getObject('HelpPage');
		$doc_it = $doc->getExact( $_REQUEST['id'] );
		
		if ( $doc_it->count() > 0 )
		{
			return $doc_it->getDisplayName();
		}
		else
		{
			return parent::getTitle();
		}
 	}

 	function getKeywords()
 	{
 		return array (
 			translate('документация'),	
 			translate('помощь'),	
 			translate('справка'),	
 			translate('руководство'),	
 			translate('guide'),	
 			translate('help'),	
 			translate('doc'),	
 		);
 	}
 }
 
 ////////////////////////////////////////////////////////////////////////////////
 class TeamTable extends SiteTableBase
 {
 	function draw()
 	{
 		global $project_it, $model_factory, $methodology_it;
 		
		$part = $model_factory->getObject('pm_Participant');
		$part_it = $part->getAll();
		
		echo '<div id="content">';
		
 			$root_it = $this->getPageWikiIt('team');
			if ( $root_it->count() > 0 && $root_it->get('ContentPresents') == 'Y' )
			{
		 		echo '<div class="post">';
			 		echo '<div id="entry">';
						$parser = new SiteWikiParser($root_it, $project_it);
						echo $parser->parse();
			 		echo '</div>';
	 			echo '</div>';
			}

			while ( !$part_it->end() )
			{
				echo '<div class="post">';
					echo '<h2 class="title">';
						echo $part_it->getDisplayName();
					echo '</h2>';
	
					echo '<div id="entry">';
						echo '<div class="email">';
							echo $part_it->get('Email');
						echo '</div>';
						if ( $part_it->get('Skype') != '' )
						{
							echo '<div class="skype">';
								echo 'Skype: '.$part_it->get('Skype');
							echo '</div>';
						}
						if ( $part_it->get('Phone') != '' )
						{
							echo '<div class="phone">';
								echo 'Phone: '.$part_it->get('Phone');
							echo '</div>';
						}
					echo '</div>';
				echo '</div>';
	
				$part_it->moveNext();
			}

		echo '</div>';
 	}
 }
  
 ////////////////////////////////////////////////////////////////////////////////
 class MainPageTable extends SiteTableBase
 {
 	function draw()
 	{
 		global $project_it, $model_factory;
 		
 		$page_it = $this->getPageWikiIt('main');

		if ( $project_it->IsPublicBlog() || $project_it->IsPublicArtefacts() )
		{
			echo '<div id="colOne">';
		}

		echo '<div id="content">';
	 		echo '<div id="post">';
		 		echo '<div id="entry">';
			 		$parser = new SiteWikiParser( $page_it, $project_it );
					echo $parser->parse();
				echo '</div>';
			echo '</div>';
		echo '</div>';

		if ( $project_it->IsPublicBlog() || $project_it->IsPublicArtefacts() )
		{
			echo '</div>';
		
	 		echo '<div id="colTwo">';
				echo '<div id="sidebar">';
					if ( $project_it->IsPublicBlog() )
					{
						echo '<ul>';
						echo '<li>';
							echo '<h2>'.translate('Новости').'</h2>';
							echo '<ul>';
				
							$post = $model_factory->getObject('BlogPost');
							$post_it = $post->getByRefArray(
								array( 'Blog' => $project_it->get('Blog') ), 5);
												
							for ( $i = 0; $i < $post_it->count(); $i++ )
							{
								echo '<li>';
								
									echo '<a href="'.SitePageUrl::parse($post_it).'">'.
											$post_it->getDisplayName().'</a>';
					
									$post_it->moveNext();
					
								echo '</li>';	
							} 		 		
							echo '</ul>';
						echo '</li>';
						echo '</ul>';
					}
	
					if ( $project_it->get('CodeName') == 'devprom' )
					{
						echo '<ul>';
						echo '<li>';
							echo '<iframe src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fpages%2FDevprom%2F155761881109975&amp;width=210&amp;connections=0&amp;stream=true&amp;header=true&amp;height=437" scrolling="no" frameborder="0" style="border:none; margin-left:0px; overflow:hidden; width:210px; height:437px;" allowTransparency="true"></iframe>';							
						echo '</li>';
						echo '</ul>';

						echo '<ul>';
						echo '<li>';
							echo '<h2>'.translate('Партнеры').'</h2>';
							echo '<ul style="list-style-image:;">';
				
								echo '<li style="background:none;padding-top:20px;">';
								echo '<a href="http://www.agilerussia.ru"><img src="/images/logo1.png" width="155" border=0></a>';
								echo '</li>';	
		
								echo '<li style="background:none;">';
								echo '<a href="http://scrumtrek.ru/"><img src="/images/logo3.png" width="155" border=0></a>';
								echo '</li>';	
	
								echo '<li style="background:none;">';
								echo '<a href="http://www.sqalab.ru/" title="Компания SQALab предоставляет широкий cпектр услуг по разработке программного обеспечения, тестированию, разработке технической документации, консалтингу и обучению в сфере IT, проведению IT конференций, тренингов, семинаров."><img src="http://software-testing.ru/images/stories/logo/sqalab-round_182x79.jpg" width="155" border=0></a>';
								echo '</li>';	

								echo '<li style="background:none;">';
								echo '<a href="http://agiledays.ru"><img src="http://agiledays.ru/resources/img/logo.png" width="155" border=0></a>';
								echo '</li>';	
	
							echo '</ul>';
						echo '</li>';
						echo '</ul>';
					}
					
				echo '</div>';
			echo '</div>';
		}
 	}
 }

 ////////////////////////////////////////////////////////////////////////////////
 class CustomPageTable extends SiteTableBase
 {
 	function draw()
 	{
 		global $project_it, $model_factory;
 		
 		if ( $_REQUEST['id'] != '' )
 		{
 			$comment = $model_factory->getObject('Comment');
 			$page = $model_factory->getObject('ProjectPage');
 			$page_it = $page->getExact($_REQUEST['id']); 

			if ( $page_it->count() < 1 )
			{
				return;
			}

			$parent_it = $page_it->getRef('ParentPage');
			if ( $parent_it->count() < 1 )
			{
				exit(header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"));
			}

			$tag_it = $parent_it->getTagsIt();

			$public = false;
			while ( !$tag_it->end() )
			{
				if ( $tag_it->getDisplayName() == 'public' || $tag_it->getDisplayName() == 'sitepage' )
				{
					$public = true;
					break;
				}
				$tag_it->moveNext();
			}

			if ( !$public )
			{
				exit(header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"));
			}

 			echo '<div id="content">';
	 		echo '<div class="post">';
		 		echo '<h2 class="title">';
					echo $page_it->getDisplayName();
		 		echo '</h2>';
		 		echo '<br/>';
		 		echo '<div id="entry">';
					$parser = new SiteWikiParser($page_it, $project_it);
					echo $parser->parse();
		 		echo '</div>';
	 		echo '</div>';
 			echo '</div>';
 		}
 		else
 		{
 			$root_it = $this->getPageWikiIt($_REQUEST['mode']);

	 		if ( !is_object($root_it) )
	 		{
	 			parent::draw();
	 			return;
	 		}
	 		
	 		if ( $root_it->count() < 1 )
	 		{
	 			parent::draw();
	 			return;
	 		}

			$children_it = $root_it->getChildrenIt();
			if ( $root_it->get('TotalCount') < 1 )
			{
				echo '<div id="content">';
			 		echo '<div id="post">';
				 		echo '<div id="entry">';
					 		$parser = new SiteWikiParser( $root_it, $project_it );
							echo $parser->parse();
						echo '</div>';
					echo '</div>';
				echo '</div>';
			}
			else
			{
				echo '<div id="colOne">';
				echo '<div id="content">';
		 			if ( $root_it->count() > 0 && $root_it->get('ContentPresents') == 'Y' )
		 			{
				 		echo '<div class="post">';
					 		echo '<div id="entry">';
								$parser = new SiteWikiParser($root_it, $project_it);
								echo $parser->parse();
					 		echo '</div>';
			 			echo '</div>';
		 			}
	
	 				$this->drawPages( $root_it, 0 );
	 			echo '</div>';
	 			echo '</div>';
	
				$parent_id = $root_it->getId();
				$children_it = $root_it->getChildrenIt();

				echo '<div id="colTwo">';
					echo '<div id="sidebar">';
						echo '<ul>';
						echo '<li>';
							echo '<h2>'.translate('Разделы').'</h2>';
							echo '<ul>';
								while ( $children_it->get('ParentPage') == $parent_id )
								{
									if ( $children_it->get('ContentPresents') == 'Y' && $children_it->get('IsArchived') != 'Y' )
									{
										echo '<li>';
											echo '<a href="#'.$children_it->getId().'">' .
												$children_it->getDisplayName().'</a>';
										echo '</li>';
									}
									
									$children_it->moveNext();
								}
							echo '</ul>';				
						echo '</li>';
						echo '</ul>';				
		 			echo '</div>';
		 		echo '</div>';
			}
 		}
 	}
 	
 	function drawPages( $page_it, $level )
 	{
 		global $project_it, $model_factory;
 		
 		$comment = $model_factory->getObject('Comment');

 		$parent_id = $page_it->getId();
 		$children_it = $page_it->getChildrenIt();
 		
 		while ( $children_it->get('ParentPage') == $parent_id )
 		{
 			if ( $children_it->get('ContentPresents') == 'Y' && $children_it->get('IsArchived') != 'Y' )
 			{
		 		echo '<div class="post">';
					echo '<a name="'.$children_it->getId().'" />';
			 		echo '<h2 class="title">';
						echo '<a href="product/'.$children_it->getSearchName().'">'.$children_it->getDisplayName().'</a>';
			 		echo '</h2>';
					echo '<div id="entry">';
						$more_text = false;
					
						$parser = new SiteWikiParser($children_it, $project_it);
						echo $parser->parse_substr( null, 840, $more_text );
						
						if ( $more_text )
						{
							echo '<div id="more" style="padding-top:6px;"><a href="product/'.$children_it->getSearchName().'">'.
								translate('читать дальше').'</a></div>';
						}

						echo '<br/>';
			 		echo '</div>';

		 		echo '</div>';
 			}
	 		
 			$children_it->moveNext();
 		}
 	}
 	
 	function getTitle()
 	{
 		global $_REQUEST, $model_factory;
 		
		$page = $model_factory->getObject('ProjectPage');
		$page_it = $page->getExact($_REQUEST['id']); 

		if ( $page_it->count() > 0 )
		{
			return $page_it->getDisplayName();
		}
		else
		{
			return parent::getTitle();
		}
 	}

 	function getKeywords()
 	{
 		return array (
 			translate('описание'),	
 			translate('возможности'),	
 			translate('назначение'),	
 			translate('использование')	
 		);
 	}
 }

 ////////////////////////////////////////////////////////////////////////////////
 class SupportTable extends SiteTableBase
 {
 	function draw()
 	{
 		global $project_it, $model_factory, $_REQUEST, $user_it;
 		
 		$uid = new ObjectUID;
 		
 		echo '<div id="content" style="width:90%;">';

			switch ($_REQUEST['object'])
			{
				case 'question':
					$question = $model_factory->getObject('pm_Question');
					$question_it = $question->getExact($_REQUEST['id']);
					
					if ( $question_it->count() > 0 )
					{
						$author_it = $question_it->getRef('Author');
						
				 		echo '<div class="post">';
					 		echo '<h2 class="title">';
								echo $question_it->getWordsOnly('Caption', 10);
					 		echo '</h2>';
				 		echo '</div>';
					 		
				 		echo '<div class="post">';
					 		echo '<div id="entry">';
					 			echo $question_it->getDateFormat('RecordCreated').' '.
					 				'<a href="'.SitePageUrl::parse($author_it).'">'.$author_it->getDisplayName().'</a>';
					 				
					 			echo '<div style="clear:both;"></div>';
					 			echo '<br/>';

					 			echo $question_it->getHtml('Content');
					 			
					 			echo '<div style="clear:both;"></div>';
					 			echo '<br/>';
					 			
					 			$this->drawComments($question_it);
					 		echo '</div>';
				 		echo '</div>';
				 		
			 			$this->initComments($question_it);
					}
					break;
					
				case 'issue':
					$request = $model_factory->getObject('pm_ChangeRequest');
					$request_it = $request->getExact($_REQUEST['id']);
					
					if ( $request_it->count() > 0 )
					{
						$author_it = $request_it->getRef('Author');
						
				 		echo '<div class="post">';
					 		echo '<h2 class="title">';
								echo $request_it->getWordsOnly('Caption', 10);
					 		echo '</h2>';
					 		echo '<div id="entry">';
					 			echo translate('Состояние').': '.$request_it->getStateName();
					 		echo '</div>';
				 		echo '</div>';
					 		
				 		echo '<div class="post">';
					 		echo '<div id="entry">';
					 			echo $request_it->getDateFormat('RecordCreated').' '.
					 				'<a href="'.SitePageUrl::parse($author_it).'">'.$author_it->getDisplayName().'</a>';
					 				
					 			echo '<div style="clear:both;"></div>';
					 			echo '<br/>';

					 			echo $request_it->getHtml('Description');
					 			
					 			echo '<div style="clear:both;"></div>';
					 			echo '<br/>';
					 			
					 			$this->drawComments($request_it);
					 		echo '</div>';
				 		echo '</div>';

			 			$this->initComments($request_it);
					}
					break;

				default:
		 			$root_it = $this->getPageWikiIt('support');
					if ( $root_it->count() > 0 && $root_it->get('ContentPresents') == 'Y' )
					{
				 		echo '<div class="post">';
			 				echo '<div id="entry">';
								$parser = new SiteWikiParser($root_it, $project_it);
								echo $parser->parse();
					 		echo '</div>';
			 			echo '</div>';
					}

					$comment = $model_factory->getObject('Comment');

			 		echo '<div class="post">';
				 		echo '<h2 class="title">';
							echo text('procloud941');
				 		echo '</h2>';
				 		echo '<div id="entry">';
				 			$url = "javascript: $('#felk').triggerHandler('click');";
				 			echo str_replace('%1', '<a href="'.$url.'">'.translate('форму обратной связи').'</a>', text('procloud445'));
				 		echo '</div>';
			 		echo '</div>';
		
			 		echo '<div class="post">';
				 		echo '<h2 class="title">';
							echo translate('Участие в проекте');
				 		echo '</h2>';
		
				 		echo '<div id="entry">';
			 				$url = 'http://projectscloud.ru/main/'.$project_it->get('CodeName');

				 			if ( !is_object($user_it) )
				 			{
				 				$url = "javascript: getLoginForm('".$url."')";
				 			}
				 			
				 			echo str_replace('%1', $url, text('procloud471') );
				 		echo '</div>';
			 		echo '</div>';
			}

 		echo '</div>';
 	}
 	
 	function getKeywords()
 	{
 		return array (
 			translate('поддержка'),	
 			translate('сопровождение'),	
 			translate('вопросы'),	
 			translate('faq'),	
 			translate('участие'),	
 			translate('комментарии'),	
 			translate('пользователей'),	
 		);
 	}
 }
 
 ////////////////////////////////////////////////////////////////////////////////
 class Page404 extends SiteTableBase
 {
 	function validate()
 	{
 		return true;
 	}
 	
 	function draw()
 	{
 		echo '<div id="content" style="width:90%;">';
	 		echo '<div class="post">';
		 		echo '<div id="entry" style="height:300px;">';
					echo text('procloud568');
		 		echo '</div>';
 			echo '</div>';
 		echo '</div>';
 	}
 }
  
 ////////////////////////////////////////////////////////////////////////////////
 class WikiExportTable
 {
 	function WikiExportTable()
 	{
 		global $_REQUEST, $project_it, $model_factory;
 		
 		if ( $_REQUEST['id'] != '' )
 		{
			$wiki = $model_factory->getObject('WikiPage');
			$wiki_it = $wiki->getExact($_REQUEST['id']);

			if ( $wiki_it->count() < 1 )
			{
				die();
			}
			
			if ( $wiki_it->get('ReferenceName') != "4" )
			{
				$parent_it = $wiki_it->getRef('ParentPage');
				if ( $parent_it->count() < 1 )
				{
					exit(header('Location: /404'));
				}

				$tag_it = $parent_it->getTagsIt();

				$public = false;
				while ( !$tag_it->end() )
				{
					if ( $tag_it->getDisplayName() == 'public' || $tag_it->getDisplayName() == 'sitepage' )
					{
						$public = true;
						break;
					}
					$tag_it->moveNext();
				}

				if ( !$public )
				{
					exit(header('Location: /404'));
				}
			}


			switch ( $_REQUEST['kind'] )
			{
				case 'rtf':
			 		$converter = new WikiConverterRtf();
			 		//$converter->setParser( new SiteWikiParser() );
			 		$converter->setObjectIt( $wiki_it );
			 		
			 		$converter->parse();
					break;

				case 'pdf':
					if ( function_exists('mb_convert_encoding') )
					{
					 	$converter = new WikiConverterMPdf();
					}
					else
					{
					 	$converter = new WikiConverterPdf();
					}

			 		//$converter->setParser( new SiteWikiParser() );
			 		$converter->setObjectIt( $wiki_it );
			 		
			 		$converter->parse();
					break;
			}
 		}

		die();
 	}
 }
 
 ////////////////////////////////////////////////////////////////////////////////
 class StyleTable
 {
 	function StyleTable()
 	{
 		global $project_it, $model_factory;
 		
		$artefact = $model_factory->getObject('pm_Artefact');
		
		if ( $_REQUEST['file'] == 'custom.css' )
		{
			$artefact_it = $artefact->getByRefArray(
				array ( 'Caption' => 'style.css' )
				);
				
			$mime = 'text/css';
		}
		else
		{
			$artefact_it = $artefact->getByRefArray(
				array ( 'Caption' => $_REQUEST['file'] )
				);
		}
		
		if ( $artefact_it->count() > 0 )
		{
			$expires = 60 * 60 * 24 * 1;
			
		 	header("Pragma: public");
		 	header("Cache-Control: maxage=". $expires);
		 	header("Expires: " . gmdate('D, d M Y H:i:s', time()+$expires) . " GMT");
			header("Last-Modified: " . $artefact_it->getDateFormatUser("RecordModified", "%a, %d %b %Y %H:%I:%S"). " GMT");
			
			if ( $mime != '' )
			{
				header("Content-type: ".$mime);
			}
			
			$artefact_it->getFile('Content');
		}
		
		die();
 	}
 }
 
 ////////////////////////////////////////////////////////////////////////////////
 class SearchTable
 {
 	function SearchTable()
 	{
 		global $project_it, $model_factory;
 		
 		$site_url = CoController::getProductUrl($project_it->get('CodeName'));
 		$_REQUEST['file'] = strtolower(trim($_REQUEST['file']));
 		
		if ( $_REQUEST['file'] == 'robots.txt' )
		{
			echo 'User-Agent: *'.chr(10);
			echo 'Disallow: /news?'.chr(10);
			echo 'Disallow: /news/tag/*'.chr(10);
			echo 'Disallow: /download/*'.chr(10);
			echo 'Disallow: /export*'.chr(10);
			echo 'Disallow: /support/*'.chr(10);
			echo 'Disallow: /pm/*'.chr(10);
			echo 'Disallow: /co/*'.chr(10);
			
			if ( $project_it->get('CodeName') == 'devprom' )
			{
				echo 'Host: devprom.ru'.chr(10);
			}
			else
			{
				echo 'Host: projectscloud.ru'.chr(10);
			}
			
			echo 'Sitemap: '.$site_url.'sitemap.xml';
		}
		else if ( $_REQUEST['file'] == 'sitemap.xml' )
		{
			$urls = array();
			
			$post = $model_factory->getObject('BlogPost');
			$post_it = $post->getByRefArray( array( 'Blog' => $project_it->get('Blog') ) );
			
			while ( !$post_it->end() )
			{
				array_push($urls, SitePageUrl::parse($post_it));
				$post_it->moveNext();
			}

			$base_it = $project_it->getRef('MainWikiPage');
			$product_it = $base_it->getChildrenIt();

			while ( !$product_it->end() )
			{
				if ( $product_it->get('ContentPresents') == 'Y' )
				{
					$public = false;
					$tag_it = $product_it->getTagsIt();
					
					while ( !$tag_it->end() )
					{
						if ( $tag_it->getDisplayName() == 'public' || $tag_it->getDisplayName() == 'sitepage' )
						{
							$public = true;
							break;
						}
						$tag_it->moveNext();
					}
					
					if ( $public )
					{
						array_push($urls, $site_url.'product/'.$product_it->getSearchName() );
					}
				}

				$product_it->moveNext();
			}
			
			$doc = $model_factory->getObject('HelpPage');
			$doc_it = $doc->getAll(); 
			
			while ( !$doc_it->end() )
			{
				if ( !$doc_it->IsArchived() )
				{
					array_push( $urls, $site_url.'docs/'.$doc_it->getSearchName() );
				}
				$doc_it->moveNext();
			}
			
			$menu_items = SiteTableBase::getMenuItems();
			foreach ( array_keys($menu_items) as $key )
			{
				array_push($urls, $site_url.$key);
			}

			header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Pragma: no-cache"); // HTTP/1.0
			header('Content-type: text/xml; charset=utf-8');

			echo '<?xml version="1.0" encoding="UTF-8"?>'.chr(10);
			echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.chr(10);
			
			foreach ( $urls as $location )
			{
				echo '<url>'.chr(10);
					echo '<loc>'.$post_it->wintoutf8($location).'</loc>'.chr(10);
					echo '<lastmod>'.date('Y-m-d').'</lastmod>'.chr(10);
					echo '<changefreq>daily</changefreq>'.chr(10);
				echo '</url>'.chr(10);
			}
			echo '</urlset>';
		}
		
		die();
 	}
 }
  
 ////////////////////////////////////////////////////////////////////////////////
 class RssTable
 {
 	function RssTable()
 	{
 		global $project_it, $model_factory;
 		
 		$site_url = CoController::getProductUrl($project_it->get('CodeName'));

		header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		header('Content-type: text/xml; charset=utf-8');

		$xml = '<?xml version="1.0" encoding="utf-8"?>'.Chr(10);
		$xml .= '<feed xmlns="http://www.w3.org/2005/Atom">'.Chr(10);
		$xml .= '<link rel="alternate" type="text/html" hreflang="en" href="'.$site_url.'news"/>'.Chr(10);
		$xml .= '<link rel="self" type="application/atom+xml" href="'.$site_url.'rss"/>'.Chr(10);

		$post = $model_factory->getObject('BlogPost');
		$post->setLimit(10);

		if ( $_REQUEST['tag'] != '' ) {
			$tag = $model_factory->getObject('BlogPostTag');
			$post_it = $tag->getPostsByTag( $_REQUEST['tag'] );
			$post_it = $post->getByRef('BlogPostId', array_slice($post_it->idsToArray(), 0, 5));
		}
		else {
			$post_it = $post->getByRefArray(
				array( 'Blog' => $project_it->get('Blog') ), 10);
		}


		$common = array (
			'title' => $project_it->wintoutf8(translate('Новости проекта').': '.$project_it->getDisplayName()),
			'subtitle' => '',
			'updated' => $post_it->getDateFormatUser('RecordCreated', '%Y-%m-%dT%H:%I:%SZ'),
			'id' => 'news: '.$project_it->get('CodeName')
			);
		
		$xml .= $this->convert($common);
		
		$xml .= '<generator uri="http://projectscloud.ru" version="1.0">'.
			$project_it->wintoutf8(translate('Облако проектов')).'</generator>'.Chr(10);
			
		$xml .= '<author><name>'.$project_it->wintoutf8($project_it->getDisplayName()).'</name></author>'.Chr(10);
		$xml .= '<rights>Copyright (c) '.date('Y', time()).' '.$project_it->wintoutf8($project_it->getDisplayName()).'</rights>'.Chr(10);
		
		while ( !$post_it->end() )
		{
			$xml .= '<entry>'.Chr(10);

			$parser = new SiteBlogParser($post_it, $project_it);
			$content = $project_it->wintoutf8($parser->parse());
			
			$entry = array (
				'title' => '<![CDATA['.$project_it->wintoutf8($post_it->getDisplayName()).']]>',
				'id' => 'news.post: '.$post_it->getId(),
				'updated' => $post_it->getDateFormatUser('RecordCreated', '%Y-%m-%dT%H:%I:%SZ'),
				'content' => '<![CDATA['.$content.']]>'
				);
			
			$xml .= $this->convert($entry);
			
			$xml .= '<author><name>DEVPROM team</name></author>'.Chr(10);
			$xml .= '<rights>Copyright (c) '.date('Y', time()).' projectscloud.ru</rights>'.Chr(10);
			$xml .= '<link rel="alternate" type="text/html" href="'.$site_url.'news/'.$project_it->wintoutf8($post_it->getSearchName()).'"/>'.Chr(10);
			$xml .= '</entry>'.Chr(10);
			
			$post_it->moveNext();
		}
		
		$xml .= '</feed>';
		
		echo $xml;
		
		die();
 	}

 	function convert ( $attributes )
 	{
		$tags = array_keys($attributes);
		
		for ( $i = 0; $i < count($tags); $i++ )
		{
			if ( $tags[$i] == 'content' )
			{
				$result .= '<'.$tags[$i].' type="html">'.$attributes[$tags[$i]].'</'.$tags[$i].'>'.Chr(10);
			}
			else
			{
				$result .= '<'.$tags[$i].'>'.$attributes[$tags[$i]].'</'.$tags[$i].'>'.Chr(10);
			}
		}
		
		return $result;
 	}
 }
   
 ////////////////////////////////////////////////////////////////////////////////
 class SiteProjectPage extends Page
 {
 	function getTable()
 	{
 		global $project_it, $_REQUEST;

 		if ( $_REQUEST['mode'] == 'main' )
 		{
			return new MainPageTable;
 		}
 		else if ( $_REQUEST['mode'] == '404' )
 		{
 			return new Page404;
 		}
 		else if ( $_REQUEST['mode'] == 'index' )
 		{
			return new Page404;
 		}
 		else if ( $_REQUEST['mode'] == 'requests' )
 		{
			return new Page404;
 		}
 		else if ( $_REQUEST['mode'] == 'resolved' )
 		{
			return new Page404;
 		}
 		else if ( $_REQUEST['mode'] == 'download' )
 		{
 			return new ArtefactsTable;
 		}
 		else if ( $_REQUEST['mode'] == 'docs' )
 		{
 			return new DocsTable;
 		}
 		else if ( $_REQUEST['mode'] == 'support' )
 		{
 			return new SupportTable;
 		}
 		else if ( $_REQUEST['mode'] == 'team' && $project_it->IsPublicParticipants() )
 		{
 			return new Page404;
 		}
 		else if ( $_REQUEST['mode'] == 'export' )
 		{
 			return new WikiExportTable;
 		}
 		else if ( $_REQUEST['mode'] == 'questions' )
 		{
 			return new Page404;
 		}
 		else if ( $_REQUEST['mode'] == 'changes' )
 		{
 			return new ChangesList;
 		}
 		else if ( $_REQUEST['mode'] == 'rating' )
 		{
 			return new ProjectRatingTable($project_it);
 		}
 		else if ( $_REQUEST['mode'] == 'style' )
 		{
 			return new StyleTable;
 		}
 		else if ( $_REQUEST['mode'] == 'search' )
 		{
 			return new SearchTable;
 		}
 		else if ( $_REQUEST['mode'] == 'rss' )
 		{
 			return new RssTable;
 		}
 		else if ( $_REQUEST['mode'] == 'login' )
 		{
 			return new CoLoginController;
 		}
 		else if ( $_REQUEST['mode'] == 'news' )
 		{
 			if ( $_REQUEST['id'] != '' )
 			{
	 			return new NewsPageTablePage;
 			}
 			else if ( $_REQUEST['tag'] != '' )
 			{
	 			return new ProductTagsPage;
 			}
 			else
 			{
	 			return new NewsPageTable;
 			}
		}
 		else
 		{
			return new CustomPageTable;
 		}
 	}

 	function draw() 
 	{
 		global $model_factory, $project_it, $_REQUEST, $user_it;
 		
		$menu_items = SiteTableBase::getMenuItems();
		
		if ( $project_it->get('CodeName') == 'devprom' )
		{
			$url = '/';
		}
		else
		{
			$url = '/site/'.$project_it->get('CodeName').'/';
		}
		
		if ( $project_it->get('Tools') != 'custom' )
		{
			$style_url = '/css/'.strtolower($project_it->get('Tools')).'/style.css'; 
		}
		else
		{
			$style_url = $url.'style/custom.css'; 
		}

		$tag = $model_factory->getObject('pm_ProjectTag');
		$tag_it = $tag->getByRef('Project', $project_it->getId());
	
		$words = array_merge($this->table->getKeywords(), $tag_it->fieldToArray('Caption'));
		$keywords = join($words, ', ');

		if ( $this->table->getTitle() == '' )
		{
			$title = $project_it->getDisplayName().' - '.$menu_items[$_REQUEST['mode']];
		}
		else
		{
			$title = $this->table->getTitle();
		}

		header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		header('Content-type: text/html; charset='.APP_ENCODING);

 		echo '<html>';
 		echo '<head>';
 			echo '<meta name="keywords" content="'.$keywords.'" />';
			echo '<meta name="description" content="'.$project_it->get('Description').'" />';
			echo '<meta name="verify-v1" content="5MUI1iVXFzgP0cnAnP5q/9RyJGTSJ9fNCA6z79mu7BQ=" />';
			echo "<meta name='yandex-verification' content='7f3c1f3c6f915686' />";
			echo "<meta name='yandex-verification' content='79d2efa72f1f9986' />";
			echo '<meta name="google-site-verification" content="Eu3jj0zEb0hZP8QV4JdqdpuzI06-cIl6NqE494BCfGE" />';
 			echo '<title>'.$title.'</title>';
 			echo '<link rel="stylesheet" type="text/css" href="'.$style_url.'"/>';
 			echo '<link rel="stylesheet" type="text/css" href="/stylex">';
			echo '<link type="application/rss+xml" rel="alternate" href="'.$url.'rss"/>';
 			echo '<script type="text/javascript" src="/jscripts"></script>';
			echo '<!-- toodoo-key: ApB2gSpRCa8qdrrHG7Gag -->';
 		echo '</head>';
		?>
		<style>
			.commentsholder { padding-top:12px; }

			.combody
			{
				clear:both;
			}
			
			.combody img
			{
				margin-left:-4px;
			}
			
			.combody .info
			{
				clear:both;
				padding-top:12px;
			}
			
			.combody .info .date
			{
				float:left;
			}
			
			.combody .info .author
			{
				float:left;
				padding-left:8px;
			}
			
			.combody .content
			{
				clear:both;
				padding:6px 0 6px 0;
			}			
			
			.blackbutton
			{
				float:left;
				margin-right:12px;
			}
		</style>
		<?
 		echo '<body>';
		echo '<div id="loginbg"></div><div id="loginform"></div>';
		echo '<div id="wrap">';
		echo '<div id="header">';
			echo '<div id="logo">';
				echo '<h1>';
					echo '<a href="">'.$project_it->getDisplayName().'</a>';
				echo '</h1>';
				echo '<p>';
					echo $project_it->getHtml('Description');
				echo '</p>';
			echo '</div>';
	
			echo '<div id="search">';
			echo '</div>';

			echo '<div id="menu">';
				echo '<ul>';
					$keys = array_keys ( $menu_items );
					
					foreach( $keys as $key )
					{
						if ( $_REQUEST['mode'] == $key )
						{
							$class = 'active';
						}
						else
						{
							$class = '';
						}
						echo '<li class="'.$class.'">';
							echo '<a href="'.$url.$key.'">'.$menu_items[$key].'</a>';
						echo '</li>';
					}
				echo '</ul>';
			echo '</div>';
		echo '</div>';

		echo '<div id="splash">';
		echo '</div>';

		echo '<div id="page">';
			echo '<div id="page-bg">';
 				$this->table->draw();
 				
 				echo '<div style="clear: both;">&nbsp;</div>';
	 		echo '</div>';
 		echo '</div>';
 		
		echo '<div id="footer" style="margin-bottom:40px;">';
			echo '<p>';
			echo '<div style="float:left;padding-left:20px;">';
				$keys = array_keys ( $menu_items );
				
				$items = 0;
				
				foreach( $keys as $key )
				{
					if ( $items >= 2 )
					{
						//echo '</div>';
						$items = 0;
					}
					
					if ( $items == 0 )
					{
						//echo '<div style="float:left;padding-left:20px;">';
					}
					
					if ( $_REQUEST['mode'] == $key )
					{
						$class = 'current_page_item';
					}
					else
					{
						$class = '';
					}
					//echo '<div><a href="'.$url.$key.'">'.$menu_items[$key].'</a></div>';
					
					$items++;	
				}
			echo '</div>';

			echo '<div style="float:left;padding-left:20px;text-align:left;">';
				if ( is_object($user_it) )
				{
					echo '<div>'.translate('Пользователь').': '.$user_it->getDisplayName().' (<a href="javascript: logoff();">'.translate('выход').'</a>)</div>';

					if ( $project_it->HasUserAccess($user_it) )
					{
						echo '<div>'.str_replace('%1', 'http://projectscloud.ru/pm/'.$project_it->get('CodeName').'/', text('procloud528')).'</div>';
					}
				}
			echo '</div>';
			echo '<div style="float:right;padding-right:20px;text-align:right;">';
				echo '<div>'.str_replace('%1', 'http://projectscloud.ru/main/'.$project_it->get('CodeName'), text('procloud446')).'</div>';
			echo '</div>';
			echo '</p>';
 		echo '</div>';
 		echo '</div>';

 		echo '<script src="'.(defined('FEEDBACK_URL') ? FEEDBACK_URL : 'http://projectscloud.ru').
			'/feedback/'.$project_it->get('CodeName').'" type="text/javascript"></script>';
		echo '<script type="text/javascript">feedbackOpts.formBackground="";feedbackOpts.tagBackground="";feedbackOpts.formColor="";</script>';

		?>
 		<script type="text/javascript">
 			$(document).ready(function() { 
 				$("img.wiki_page_image").each( function() {
					if ( $.browser.msie ) {
 						this.setAttribute('href', $(this).attr('src') + '&.png'); 
					} else {
 						this.href = $(this).attr('src') + '&.png'; 
					}
 				});
 				$("img.wiki_page_image").fancybox({  
 					hideOnContentClick: true
 				});
 			});
 		</script> 

		<script type="text/javascript">
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
		try {
		var pageTracker = _gat._getTracker("UA-10541243-1");
		pageTracker._trackPageview();
		} catch(err) {}</script>

		<?
		
 		echo '</body>';
 		
 		echo '</html>';
 	}
 }

?>