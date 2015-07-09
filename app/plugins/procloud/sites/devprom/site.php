<?php

include( 'DevpromLoginController.php' );
include( 'BaseDevpromTable.php' );
include( 'LicenseDevpromTable.php' );
include( 'Devprom404Table.php' );
include( 'GlossaryDevpromTable.php');
include( 'TrainingDevpromTable.php');
include( 'DocsDevpromTable.php');
include( 'FeaturesDevpromTable.php');
include( 'MainDevpromTable.php');

 ////////////////////////////////////////////////////////////////////////////////
 class DEVPROMWikiParser extends SiteWikiParser
 {
 	function parse( $content = null )
 	{
 		if ( is_null($content) )
 		{
 			$object_it = $this->getObjectIt();
 			return $object_it->getHtmlDecoded('Content');
 		}
 		else
 		{
 			return html_entity_decode( $content );
 		}
 	}
 }

 ////////////////////////////////////////////////////////////////////////////////
 class StyleDEVPROMTable extends StyleTable
 {
	function validate()
	{
	}
 	
	function StyleDEVPROMTable()
 	{
 		global $_REQUEST, $model_factory;

 		if ( $_REQUEST['file'] != 'custom.css' )
 		{
			$expires = 60 * 60 * 24 * 3;
			
		 	header("Pragma: public");
		 	header("Cache-Control: maxage=". $expires);
		 	header("Expires: " . gmdate('D, d M Y H:i:s', time()+$expires) . " GMT");
			header("Content-type: image/png");
				
			$filepath = dirname(__FILE__).'/images/'.basename($_REQUEST['file']);
			if ( !file_exists($filepath) ) die();
		
			$file = fopen( $filepath, "r" );
			echo fread( $file, filesize($filepath));
		
			die();	
 		}
 		else
 		{
			$artefact = $model_factory->getObject('pm_Artefact');

			$artefact_it = $artefact->getByRefArray(
				array ( 'Caption' => 'custom.css' )
				);
				
			$mime = 'text/css';

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
			
			die();
 		}
 		
 		parent::StyleTable();
 	}
 }
 
 ////////////////////////////////////////////////////////////////////////////////
 class FilesDEVPROMTable extends ArtefactsTable
 {
 	private $sections_map = array();
 	
	function validate()
	{
		global $_REQUEST;
		
		if ( array_key_exists('json', $_REQUEST) != '' )
		{
		    $this->exportUpdatesJson();
		}
		
		if ( $_REQUEST['key'] != '' )
		{
			$this->script = '<script type="text/javascript">$().ready(function(){getRestoreForm("'.$_REQUEST['key'].'");});</script>';
		}
		
		$this->sections_map = array (
				'distributives' => 72,
				'updates' => 71,
				'connectors' => 674
		);
	}
	
	function exportUpdatesJson()
	{
	    global $model_factory, $project_it;
	    
		$artefact = $model_factory->getObject('pm_Artefact');

		$artefact->setSortDefault( new SortRevOrderedClause() );
		
		$version_major = 'current';
		
		if ( $_REQUEST['version'] != '' )
		{
			$version_parts = preg_split('/\./', $_REQUEST['version']);
			
			if ( count($version_parts) == 4 )
			{
				$version_major = $version_parts[0].'.'.$version_parts[1];
			}
		}
		
		$version_channels = array (
				'current' => 71, // Обновления
				'3.6' => 658 	 // Обновления для версии
		);
		
		$kind = array_key_exists('msword', $_REQUEST) 
		    ? 674 // Коннекторы и плагины 
		    : ($version_channels[$version_major] > 0 ? $version_channels[$version_major] : $version_channels['current']);
		
		$artefact_it = $artefact->getByRefArray( array ( 
            'Project' => $project_it->getId(), 
            'Kind' => $kind,
            "IFNULL(IsArchived, 'N')" => 'N' 
        ));

        $data = array();
        
        while( !$artefact_it->end() )
        {
            if ( preg_match('/(devprominstaller|devprom\.zip)/i', $artefact_it->getDisplayName(), $matches) )
            {
                $artefact_it->moveNext();
                continue;
            }
            
            if ( array_key_exists('msword', $_REQUEST) && !preg_match('/word/i', $artefact_it->getDisplayName(), $matches) )
            {
                $artefact_it->moveNext();
                continue;
            }

            if ( $this->paymentRequired($_REQUEST['iid'], $artefact_it) )
            {
                $artefact_it->moveNext();
                continue;
            }
            
            $data[] = array (
                'type' => 'planned',
                'created' => $artefact_it->get_native('RecordCreated'),
                'version' => $artefact_it->get('Version'),
                'description' => IteratorBase::wintoutf8($artefact_it->get('Description')),
                'download_url' => 'http://devprom.ru/download/'.$artefact_it->getSearchName().'?version='.$artefact_it->get('Version')
            );
        
            $artefact_it->moveNext();
        }
        
	 	header("Pragma: public");
	 	header("Cache-Control: maxage=". $expires);
	 	header("Expires: " . gmdate('D, d M Y H:i:s', time()+$expires) . " GMT");
		header('Content-type: text/html; charset=utf-8');
 		header('Access-Control-Allow-Origin: *');
 		header('Access-Control-Allow-Methods: *');
 		header('Access-Control-Allow-Headers: *');
		
		echo JsonWrapper::encode($data);
		
		die();
	}
 	
	function draw()
 	{
 		global $project_it, $model_factory, $methodology_it, $_REQUEST;
 		
		$artefact = $model_factory->getObject('pm_Artefact');
		
		$artefact->defaultsort = ' (SELECT k.OrderNum FROM pm_ArtefactType k WHERE k.pm_ArtefactTypeId = t.Kind) ASC, t.OrderNum DESC, t.Kind, t.RecordModified DESC';
		
		$artefact_it = $artefact->getByRefArray(
			array ( 'Project' => $project_it->getId(), "IFNULL(IsArchived, 'N')" => 'N' ) );
			
		$this->drawScript();
		
		$current_kind = array_shift($this->sections_map);
		
		foreach( $this->sections_map as $section => $kind )
		{
			if ( array_key_exists($section, $_REQUEST) )
			{
				$current_kind = $kind;
				break;
			}
		}
		
		?>
		<div class="wrapper1000">
			<!-- Здесь нужен скрипт переключащий вкладки -->
			<div class="tabs mb25">
				<div class="bgTop">
				</div> <!-- end bgTop -->
				<div class="bgCenter">
					<div class="menu">
						<ul>
						<?
						$prev_kind = 0;
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
								$prev_kind = $artefact_it->get('Kind');
								
								echo '<li><a href="javascript:" type="'.$type_it->getId().'">'.$type_it->getDisplayName().'</a></li>';
							}
							$artefact_it->moveNext();
						}
						?>
						</ul>
						<? if ( true ) { ?>
<div class="grayRound">
       <div class="grayRoundTop">
       </div>
       <div class="grayRoundCenter">
        <h2>Нужна консультация?</h2>
        <p><a class="skype" href="skype:dmitry.lobasev?chat">devprom</a></p>
        <a class="tel" href="/contacts">+7 (499) 638-64-11</a>
       </div>
       <div class="grayRoundBottom">
       </div>
      </div>
      
      <div class="grayRound">
       <div class="grayRoundTop">
       </div>
       <div class="grayRoundCenter">
        <h2>Присоединяйтесь!</h2>
        <p>
         <a class="facebook" href="http://facebook.com/Devprom">Следите за новостями</a>
         <a class="rss" href="/rss">Подпишитесь на RSS</a>
        </p>
       </div>
       <div class="grayRoundBottom">
       </div>
      </div>						

						<? } ?>
					</div> <!-- end menu -->

					<div class="contentTabs">
					<?
					$artefact_it->moveFirst();
					while ( !$artefact_it->end() )
					{
						$type_it = $artefact_it->getRef('Kind');
						if ( $type_it->get('IsDisplayedOnSite') == 'N' )
						{
							$artefact_it->moveNext();
							continue;
						}

						$url = SitePageUrl::parse($artefact_it);
						?>
						<div class="roundedGrayBor" type="<? echo $artefact_it->get('Kind') ?>">
							<div class="roundedTopGrayBor">
							</div> <!-- end roundedTopGrayBor -->
							<div class="roundedCenterGrayBor">
								<h3><a class="orang" href="<? echo $url?>"><? echo $artefact_it->getDisplayName() ?></a>
								<?php if ( $artefact_it->getVersion() != '' ) { ?>
								<span class="bgOrang"><b><? echo $artefact_it->getVersion() ?></b></span>
								<?php } ?>
								</h3>
								<p><? echo $artefact_it->getHtml('Description') ?></p>

								<div class="productInfo">
									<a class="downloadButton" href="<? echo $url ?>">Загрузить</a>
									<span><? echo $artefact_it->getDateFormat('RecordModified') ?></span>
									|
									<span><? echo $artefact_it->getFileSizeKb('Content') ?> Кб</span>
								</div> <!-- end productInfo -->
							</div>
							<div class="roundedBottomGrayBor">
							</div> <!-- end roundedBottomGrayBor -->
						</div> <!-- end roundedGrayBor -->
						<?
						$artefact_it->moveNext();
					}
					?>
					</div> <!-- end contentTabs -->
					<div class="clearFix">
					</div>
				</div> <!-- end bgCenter -->
				<div class="bgBottom">
				</div> <!-- end bgBottom -->
			</div> <!-- end tabs -->
		</div> <!-- end wrapper1000 -->

		<script type="text/javascript">
			$(document).ready(function() 
			{
				$('.menu li a')
					.click( function() {
						$('.menu li').attr('class', '');
						$('.roundedGrayBor').hide();
						
						$(this).parent().attr('class', 'current');
						$('.roundedGrayBor[type='+$(this).attr('type')+']').show();
					});

				$('.roundedGrayBor').hide();
				$('.menu li a[type=<?=$current_kind?>]').trigger('click');
			});
		</script>
		<?
 	}

	function getTitle()
	{
 		return "Загрузить";
	}
	
  	function paymentRequired( $iid, $artefact_it )
 	{
 		switch( $artefact_it->getDisplayName() )
 		{
 		    case 'DevpromUpdate34.zip':
 		    case 'DevpromUpdate35.zip':
 		    	$service_it = $this->getServiceIt($iid);
 		    	if ( !is_object($service_it) ) return true;
 		    	if ( $service_it->getId() == '' ) return true;
 		    	if ( $service_it->get('PayedTill') < date('Y-m-d') ) return true;
 		    	return false;
 		    	
 		    default:
 		    	return false;
 		}
 	}
 	
 	function getServiceIt($iid)
 	{
 	    if ( !preg_match('/^[a-z0-9{-]+$/im', $iid, $matches) ) return null;
 		
		if ( $_SERVER['HTTP_REFERER'] != '' )
		{
			$parts = parse_url($_SERVER['HTTP_REFERER']);
			$title = $parts['host'];
		}
		
		$service_it = getFactory()->getObject('ServicePayed')->getByRef('VPD', $iid);
		if ( $service_it->getId() < 1 )
		{
			if ( $title == '' && $_SERVER['REMOTE_HOST'] != '' )
			{
				$title = $_SERVER['REMOTE_HOST'];
			}
			if ( $title == '' )
			{
				$data = mysql_fetch_array(DAL::Instance()->Query("select u.Caption, u.Email, u.Phone, u.Description from cms_User u WHERE u.ICQ LIKE '%".$iid."%'"));
				if ( count($data) > 0 )
				{
					$title = $data['Email'];
					$description = $data['Description'];
				}
			}
		 	if ( $title == '' && $_SERVER['REMOTE_ADDR'] != '' )
			{
				$title = $_SERVER['REMOTE_ADDR'];
			}
			if ( $title == '' )
			{
				$title = $iid;
			}
 			$service_it->object->add_parms(
					array (
							'Caption' => $title,
							'IID' => $iid,
							'Description' => $description
					)
			);
 		}
 		elseif ( $title != '' )
		{
 			$service_it->object->modify_parms( $service_it->getId(),
					array (
							'Caption' => $title
					)
			);
		}
		
		return $service_it;
 	}
 }

 ////////////////////////////////////////////////////////////////////////////////
 class NewsDEVPROMTable extends SiteTableBase
 {
 	private $total;
 	
	function __construct()
	{
		global $model_factory, $project_it;
		
		$post = $model_factory->getObject('BlogPost');
		
		$post->addSort( new SortRevOrderedClause() );

		if ( $_REQUEST['tag'] != '' )
		{
			$tag = $model_factory->getObject('BlogPostTag');
			
			$post_it = $tag->getPostsByTag( $_REQUEST['tag'] );

			$post_it = $post->getByRefArray(array( 'BlogPostId' => $post_it ));
		}
		else
		{
			if ( $_REQUEST['id'] != '' )
			{
				$post_it = $post->getExact($_REQUEST['id']);
			}
			else
			{
				$post_it = $post->getByRefArray(array( 'Blog' => $project_it->get('Blog') ));
			}
		}

		if ( $post_it->count() < 1 )
		{
				header($_SERVER["SERVER_PROTOCOL"]." 301 Moved Permanently");
				exit(header('Location: /news'));
		}
			
		$this->total = $post_it->count();
		
		$offset = ($_REQUEST['page'] != '' ? $_REQUEST['page'] : 0) * 5;
		
		$this->post_it = $post->createCachedIterator( array_slice($post_it->getRowset(), $offset, 5) );
	}
	
	function getObsolete()
	{
		return array (
			''
		);
	}
	
	function validate()
	{
	}
	
	function getPageIt()
	{
		return $this->post_it;
	}
 	
	function getMainTags()
	{
		return array (
			'Agile', 
			'ALM', 
			'Бизнес-анализ',
			'Разработка',
			'Тестирование',
			'Технический писатель',
			'Требования'
		);
	}
	
	function draw()
 	{
 		global $project_it, $model_factory, $_REQUEST;
 		
		$tag = $model_factory->getObject('BlogPostTag');
		$tag_it = $tag->getAllTags();
		$tags = array();
		
		while ( !$tag_it->end() )
		{
			$url = CoController::getProductUrl($project_it->get('CodeName')).
				'news/tag/'.$tag_it->getSearchName();
			
			if ( in_array($tag_it->getDisplayName(), $this->getMainTags()) )
			{
				$index = 20;
			}
			else
			{
				$index = min(14 + $tag_it->get('ItemCount') / 3, 18);
			}
			
			$tags[] = '<a class="font'.$index.'" href="'.$url.'">'.$tag_it->getDisplayName().'</a>';
			$tag_it->moveNext();
		}
		
		?>
		<div class="wrapper1000">
			<div class="blogs">
				<div class="leftSide" style="<?=($_REQUEST['id'] != '' ? 'width:auto;' : '')?>">
				<?php
				if ( $_REQUEST['id'] != '' )
				{ 
					$this->drawSingle(); 
				}
				else
				{
					$this->drawList(); 
				}
				?>
				</div> 

				<?php if ( $_REQUEST['id'] == '' ) { ?>
				<div class="rightSideBar">
					<div class="item">
						<div class="bgTopRSBItem">
						</div>

						<div class="bgCenterRSBItem">
							<div class="methodNews">
								<a class="facebook" href="http://facebook.com/devprom">Присоединяйтесь!</a>
								<a class="rss" href="/rss">Подпишитесь на RSS</a>
							</div>
						</div>

						<div class="bgBottomRSBItem">
						</div>
					</div>

					<div class="item">
						<div class="bgTopRSBItem">
						</div>

						<div class="bgCenterRSBItem">
							<div class="tagsCloud">
								<?php echo join($tags, '&nbsp; '); ?>
							</div> 
						</div>

						<div class="bgBottomRSBItem">
						</div>
					</div>

<!-- 
					<div class="item">
						<div class="bgTopRSBItem">
						</div>

						<div class="bgCenterRSBItem">
							<div class="lastComments">
								<h4>Последние комментарии</h4>
								
								<?php echo is_array($comments) ? join($comments, '') : ''; ?>
							</div> 
						</div>

						<div class="bgBottomRSBItem">
						</div>
					</div>
 -->					
				</div> 
				<?php } ?>

				<div class="clearFix">
				</div>

			</div> 

		</div> <!-- end wrapper1000 -->
		<?
 	}
 	
 	function drawList()
 	{
 		global $project_it, $model_factory;
 		
		$post_it = $this->getPageIt();
	
		while ( !$post_it->end() )
		{						
			$url = SitePageUrl::parse($post_it);
			$title = $post_it->getDisplayName();
			$created = $post_it->getDateTimeFormat('RecordCreated');
			
			$parser = new SiteBlogParser($post_it, $project_it);
			$content = $parser->parse_substr( null, 840, $more_text );
		
			$tag_it = $post_it->getTagsIt();
			$tags = array();
			
			while ( !$tag_it->end() )
			{
				$tags[$tag_it->getPos()] = '<a href="'.
					CoController::getProductUrl($project_it->get('CodeName')).'news/tag/'.$tag_it->getSearchName().'">'.
						$tag_it->getDisplayName().'</a>';
					
				$tag_it->moveNext();	
			}
			
			$comment = $model_factory->getObject2('Comment', $post_it);
		?>
		<div class="item">
			<div class="bgTopBlogItem">
			</div>
			<div class="bgCenterBlogItem">
				<h2><a href="<?php echo $url ?>"><?php echo $title ?></a></h2>
				
				<div class="info">
					<div class="date">
						<?php echo $created ?>
					</div>
	
					<?php if ( count($tags) > 0 ) { ?>
					<div class="sections">
						<?php echo join($tags, ', '); ?>
					</div>
					<?php } ?>
	
					<div class="clearFix">
					</div>
				</div>
	
				<div class="wiki">
				<table cellspacing="0" cellpadding="0" border="0">
					<tr><td><?php echo $content ?></td></tr>
				</table>
				</div>
						
				<a class="orang" href="<?php echo $url ?>">Читать полностью &raquo;</a>
			</div>
			<div class="bgBottomBlogItem">
			</div>
		</div>
	
		<?php
		
		$post_it->moveNext();	
		} 
		
		$this->drawPaging( $this->total ); 
 	}
 	
 	function drawSingle()
 	{
 		global $project_it, $model_factory;
 		
		$post = $model_factory->getObject('BlogPost');
		
		$post_it = $this->getPageIt();
					
		$url = SitePageUrl::parse($post_it);
		$title = $post_it->getDisplayName();
		$created = $post_it->getDateTimeFormat('RecordCreated');
		
		$parser = new SiteBlogParser($post_it, $project_it);
		$content = $parser->parse();
	
		$tag_it = $post_it->getTagsIt();
		$tags = array();
		
		while ( !$tag_it->end() )
		{
			$tags[$tag_it->getPos()] = '<a href="'.
				CoController::getProductUrl($project_it->get('CodeName')).'news/tag/'.$tag_it->getSearchName().'">'.
					$tag_it->getDisplayName().'</a>';
				
			$tag_it->moveNext();	
		}
		
		?>
		<div class="item">
			<div class="bgTopBlogItem" style="height:7px;background:url('/style/images/roundedwhite.png') no-repeat scroll left top transparent">
			</div>
			<div class="bgCenterBlogItem" style="background:url('/style/images/roundedwhite.png') repeat-y scroll center top transparent">
				<h2><a href="<?php echo $url ?>"><?php echo $title ?></a></h2>
				
				<div class="info">
					<div class="date">
						<?php echo $created ?>
					</div>
	
					<?php if ( count($tags) > 0 ) { ?>
					<div class="sections">
						<?php echo join($tags, ', '); ?>
					</div>
					<?php } ?>
	
					<div class="comments" style="background-image:none;">
						<div style="float:left;margin-left:-20px;margin-top:-5px;"><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like ref="top_left" show_faces="false" layout="standard" width="270px"></fb:like></div>
					</div>
	
					<div class="clearFix">
					</div>
				</div>
	
				<div class="wiki">
				<table cellspacing="0" cellpadding="0" border="0">
					<tr><td><?php echo $content ?></td></tr>
					<tr><td>
				<h3>Еще интересные статьи на эту тему:</h3>
				<ul>
				<?php 
					$tag_it->moveFirst();
				
					$ids = getFactory()->getObject('BlogPostTag')->getPostsByTag( $tag_it->getId() )->idsToArray();
					unset($ids[array_search($post_it->getId(),$ids)]);
					
					$post_it = $post->getByRefArray(array( 'BlogPostId' => $ids ), 5);
				
					while(!$post_it->end() )
					{
						echo '<li><a href="/news/'.$post_it->getSearchName().'">'.$post_it->getDisplayName().'</a></li>';
						$post_it->moveNext();
					} 
				?>
				</ul>
					</td></tr>
				</table>
				
				</div>
			</div>
			<div class="bgBottomBlogItem">
			</div>
			<a name="comments"></a>
		</div>
		<?php
		
		//$this->initComments( $post_it );
 	}

	function getTitle()
	{
		global $_REQUEST, $model_factory;

		if ( $_REQUEST['id'] != '' )
		{
			$post = $model_factory->getObject('BlogPost');
			$post_it = $post->getExact($_REQUEST['id']);

			if ( $post_it->getId() > 0 ) return $post_it->getDisplayName();
		}

		$title = "Блог";
		
		if ( $_REQUEST['tag'] != '' )
		{
			$tag = $model_factory->getObject('BlogPostTag');
			
			$tag_it = $tag->getAllTags();
			
			$tag_it->moveTo('Caption', IteratorBase::utf8towin($_REQUEST['tag']));
			
			if ( $tag_it->getId() > 0 )
			{
				$title = 'Новости по теме '.$tag_it->getDisplayName();
			}
		}
		
		if ( $_REQUEST['page'] != '' && is_numeric($_REQUEST['page']) )
		{
			$title .= " (страница ".($_REQUEST['page'] + 1).") ";
		}
		
		return $title;
	}
 	
 	function getDescription()
	{
		if ( $_REQUEST['id'] == '' ) return parent::getDescription();

		$post_it = getFactory()->getObject('BlogPost')->getExact($_REQUEST['id']);
		if ( $post_it->getId() < 1 ) return parent::getDescription();
		
		$parser = new SiteBlogParser($post_it, getSession()->getProjectIt());
		$text = new html2text($parser->parse_substr( null, 840, $more_text ));
		return $text->get_text();
	}
	
 	function drawComments2( $object_it )
 	{
	?>
		<div class="commentsAll">
			<div class="bgTopComAll">
			</div>

			<div class="bgCenterComAll">
				<div class="item">
					<div class="bgTopComment">
						<div class="bgTopCommentRight">
						</div>
					</div>

					<div class="bgCenterComment">
						<div class="bgCenterCommentRight">
							<div class="foto">
								<a href="#"><img src="media/images/avatar.jpg" alt="" /></a>
							</div>
							<a class="toAnswer" href="#">Ответить</a>
							<div class="infoUser">
								<div class="userName">
									<a href="#">dakotaf.livejournal.com</a>
								</div>
								<div class="date">
									14.06.2011 в 15:09
								</div>
							</div>
							<div class="clearFix">
							</div>
							<h4>Вопрос по поводу скорости команды. </h4>
							<p>Описанный вами подход работает при условии постоянного состава команды и времени, которое уделяет каждый сотрудник на проект. А как можно вычислить производительность, если люди добавляются и уходят, меняют кол-во времени, которым они занимаются проектом. В этом случае для подсчетов скорости нужен еще один параметр — количество доступных чел/ч. Таким образом, меняя кол-во доступных чел/ч перед каждой итерацией и объем выполненной работы в идеальных часах, мы сможем уточнять скорость разработки.</p>
						</div>
					</div>

					<div class="bgBottomComment">
						<div class="bgBottomCommentRight">
						</div>
					</div>

					<div class="answerItem">
						<div class="bgTopComment">
							<div class="bgTopCommentRight">
							</div>
						</div>

						<div class="bgCenterComment">
							<div class="bgCenterCommentRight">
								<div class="foto">
									<a href="#"><img src="media/images/avatar.jpg" alt="" /></a>
								</div>
								<a class="toAnswer" href="#">Ответить</a>
								<div class="infoUser">
									<div class="userName">
										<a href="#">dakotaf.livejournal.com</a>
									</div>
									<div class="date">
										14.06.2011 в 15:09
									</div>
								</div>
								<div class="clearFix">
								</div>
								<h4>Вопрос по поводу скорости команды. </h4>
								<p>Описанный вами подход работает при условии постоянного состава команды и времени, которое уделяет каждый сотрудник на проект. А как можно вычислить производительность, если люди добавляются и уходят, меняют кол-во времени, которым они занимаются проектом. В этом случае для подсчетов скорости нужен еще один параметр — количество доступных чел/ч. Таким образом, меняя кол-во доступных чел/ч перед каждой итерацией и объем выполненной работы в идеальных часах, мы сможем уточнять скорость разработки.</p>
							</div>
						</div>

						<div class="bgBottomComment">
							<div class="bgBottomCommentRight">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="bgBottomComAll">
			</div>
		</div>

		<form class="addComment" method="post" action="">
			<table cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td class="forLabel">
						<label><b>Имя</b></label>
					</td>
					<td class="forField">
						<div class="field">
							<input class="text" type="text" name="" value="Дмитрий" />
						</div>
					</td>
				</tr>

				<tr>
					<td class="forLabel">
						<label><b>E-mail</b></label>
					</td>
					<td class="forField">
						<div class="field">
							<input class="text" type="text" name="" value="rulon@gmail.com	" />
						</div>
					</td>
				</tr>

				<tr>
					<td class="forLabel">
						<label><b>Введите текст комментария</b></label>
					</td>
					<td class="forField">
						<div class="field">
							<textarea name="" rows="" cols="">Надо, блин, запретить анонимные комментарии.</textarea>
						</div>
					</td>
				</tr>
				<tr>
					<td class="forLabel">
						&nbsp;
					</td>
					<td class="forField">
						<div class="button">
							Отправить
							<input type="submit" name="" />
						</div>
					</td>
				</tr>
			</table>
		</form>
	<?php  		
 	}
 	
	function drawPaging( $total_items, $limited_on_page = 5 )
	{
		global $_REQUEST;
		
		if ( $_REQUEST['page'] == '' ) $_REQUEST['page'] = 0;
		
		echo '<div class="pager">';
		
		if ( $total_items > $limited_on_page )
		{
			if ( $_REQUEST['page'] != '' && $_REQUEST['page'] > 0 )
			{
				echo '<a class="leftA" href="?page='.($_REQUEST['page'] - 1).'">позже</a>';
			}
	
			if ( $total_items > ($_REQUEST['page'] + 1) * $limited_on_page )
			{
				echo '<a class="rightA" href="?page='.($_REQUEST['page'] + 1).'">раньше</a>';
			}
		}
		
		for( $item = 0; $item < min($total_items / $limited_on_page, 5); $item++ )
		{
			$class = "page";
			
			if ( $item == $_REQUEST['page'] )
			{
				$class .= " current";
			}
			
			echo '<a class="'.$class.'" href="?page='.$item.'">'.($item + 1).'</a>';
		}

		echo '</div>';
	}
 	
	function getKeywords()
 	{
 		return array (
			translate('канбан'),
			translate('скрам'),
			translate('тестирование'),
			translate('требования'),
			translate('трассировка'),
 			translate('agile'),	
 			translate('новости'),	
 			translate('kanban'),	
			translate('scrum'),
 		);
 	}
 }

 ////////////////////////////////////////////////////////////////////////////////
 class PriceDEVPROMTable extends BaseDEVPROMTable
 {
 	function __construct()
 	{
 		parent::__construct();
 		
 	 	if ( $_REQUEST['id'] == '' ) return;
 		
		$page_it = getFactory()->getObject('ProjectPage')->getExact( $_REQUEST['id'] );
 			
		if ( $page_it->getId() < 1 )
		{
			throw new Exception('404');
 		}
 			
 		if ( $page_it->get('ParentPage') != $this->getObjectIt()->getId() )
 		{
 			throw new Exception('404');
 		}
 			
 		$this->setObjectIt($page_it);
 	}
 	
	function draw()
	{
		$parser = new DEVPROMWikiParser($this->getObjectIt(), getSession()->getProjectIt());
		
		echo $parser->parse();
	}
 }
 
 ////////////////////////////////////////////////////////////////////////////////
 class SearchDevpromTable extends SearchTable
 {
	function validate()
	{
	}
 	
	function SearchDevpromTable()
 	{
 		global $_REQUEST, $project_it, $model_factory;
 		
 		$site_url = CoController::getProductUrl($project_it->get('CodeName'));
 		$_REQUEST['file'] = strtolower(trim($_REQUEST['file']));
 		
		if ( $_REQUEST['file'] == 'sitemap.xml' )
		{
			$urls = array();
			
			// product
			$parent_it = BaseDEVPROMTable::getSitePageWikiIt( 'glossary' );
			
			$section_it = $parent_it->object->getRegistry()->Query( array( 
				new WikiRootTransitiveFilter( $parent_it->getId() )
			)); 
			
			while ( !$section_it->end() )
			{
				if ( $section_it->get('ContentPresents') == 'Y' )
				{				
					$urls[] = $site_url.'glossary/'.$section_it->getSearchName();
				}

				$section_it->moveNext();
			}			
			
			// features
			$parent_it = BaseDEVPROMTable::getSitePageWikiIt( 'features' );
			
			$section_it = $parent_it->object->getRegistry()->Query( array( 
				new WikiRootTransitiveFilter( $parent_it->getId() )
			)); 
			
			while ( !$section_it->end() )
			{
				if ( $section_it->get('ContentPresents') == 'Y' )
				{				
					$urls[] = $site_url.'features/'.$section_it->getSearchName();
				}

				$section_it->moveNext();
			}			
			
			// trainings
			$parent_it = BaseDEVPROMTable::getSitePageWikiIt( 'trainings' );
			
			$section_it = $parent_it->object->getRegistry()->Query( array( 
				new WikiRootTransitiveFilter( $parent_it->getId() )
			)); 
			
			while ( !$section_it->end() )
			{
				if ( $section_it->get('ContentPresents') == 'Y' )
				{				
					$urls[] = $site_url.'trainings/'.$section_it->getSearchName();
				}

				$section_it->moveNext();
			}			
			
			//
			$post = $model_factory->getObject('BlogPost');
			$post_it = $post->getByRefArray( array( 'Blog' => $project_it->get('Blog') ) );
			
			while ( !$post_it->end() )
			{
				array_push($urls, SitePageUrl::parse($post_it));
				$post_it->moveNext();
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
	 	else
	 	{
	 		return parent::SearchTable();
	 	}
		
		die();
 	}
 }
 
 ////////////////////////////////////////////////////////////////////////////////
 class SiteDEVPROMPage extends SiteProjectPage
 {
 	function getTable()
 	{
 		global $_REQUEST;
 		
 		try
 		{
	 		switch ( $_REQUEST['mode'] )
	 		{
	 			case 'search':
	 				return new SearchDevpromTable;
	 				
	 			case 'style':
	 				return new StyleDEVPROMTable;
	 				
	 			case 'features':
	 				return new FeaturesDevpromTable;
	 				
	 			case 'trainings':
	 				return new TrainingsDEVPROMTable;
	
	 			case 'main':
	 				return new MainDEVPROMTable;
	
				case 'download':
	 				return new FilesDEVPROMTable;
	
				case 'docs':
	 				return new DocsDEVPROMTable;
	
				case 'news':
	 				return new NewsDEVPROMTable;
	 			
	 			case 'export':
	 				return new WikiExportTable;
	
	 			case 'rss':
		 			return new RssTable;
	
				case 'price':
					return new PriceDEVPROMTable;
	 				
	 			case 'search':
					return new SearchTable;
	
	 			case 'login':
					return new DevpromLoginController;
	
	 			case 'license':
					return new LicenseDEVPROMTable;
					
	 			case '404':
	 				return new Devprom404Table;
	 				
	 			case 'glossary':
	 				return new GlossaryDEVPROMTable;
	 				
				default:
					if ( count(preg_split('/-/', $_REQUEST['mode'])) > 1 )
					{
						return new FeaturesDevpromTable;
					}
					else
					{
						return new BaseDEVPROMTable;
					}
	 		}
 		}
 		catch( Exception $e )
 		{
 			 $_REQUEST['mode'] = '404';
 			 $_REQUEST['id'] = '';
 			 
 			return new Devprom404Table;
 		}
 	}

	function getMenuItems()
	{
 		global $model_factory, $project_it;

		$refname = 'menuitem';
		
		$sql = "SELECT p.*," .
				"	   (SELECT MIN(t.Caption) FROM WikiTag wt, Tag t" .
				"		 WHERE wt.Wiki = p.WikiPageId AND t.TagId = wt.Tag" .
				"		   AND t.Caption <> '".$refname."' ) PageType " .
				" FROM WikiPage p " .
				"WHERE p.ReferenceName = " .getFactory()->getObject('ProjectPage')->getReferenceName().
				"  AND EXISTS (SELECT 1 FROM WikiTag wt, Tag t" .
				"			    WHERE wt.Wiki = p.WikiPageId".
				"  			      AND t.TagId = wt.Tag AND t.Caption = '".$refname."' )".
				"  AND p.Project = ".$project_it->getId().
				" ORDER BY p.OrderNum, p.WikiPageId ASC";

 		$page = $model_factory->getObject('ProjectPage');
 		$page_it = $page->createSQLIterator( $sql );
 		
 		while ( !$page_it->end() )
 		{
 			$page = $page_it->get('PageType');
 			
			$menu_items[$page] = $page_it->getDisplayName();
 			$page_it->moveNext(); 
 		}
 		
 		return $menu_items;
	}
	
	function draw() 
 	{
 		global $model_factory, $project_it, $_REQUEST, $user_it;
 		
 		$this->table->validate();
 		
		$menu_items = $this->getMenuItems();

		$url = '/';
		$style_url = $url.'style/custom.css?v=2.0'; 

		$words = array_diff($this->table->getKeywords(), array('menuitem', 'public', 'menu', 'devprom.ru'));
		
		$keywords = join($words, ', ');

		if ( $this->table->getTitle() == '' )
		{
			$title = $menu_items[$_REQUEST['mode']];
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

 		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
		echo '<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">';
 		echo '<head>';
			echo '<meta property="og:image" content="http://devprom.ru/style/images/logo.png"/>';
			echo '<meta property="og:image:secure_url" content="https://devprom.ru/style/images/logo.png"/>';
 			echo '<meta name="keywords" content="'.$keywords.'" />';
			echo '<meta name="description" content="'.$title.'" />';
			echo '<meta name="verify-v1" content="5MUI1iVXFzgP0cnAnP5q/9RyJGTSJ9fNCA6z79mu7BQ=" />';
			echo "<meta name='yandex-verification' content='7f3c1f3c6f915686' />";
			echo "<meta name='yandex-verification' content='79d2efa72f1f9986' />";
			echo '<meta name="google-site-verification" content="Eu3jj0zEb0hZP8QV4JdqdpuzI06-cIl6NqE494BCfGE" />';
 			echo '<title>'.$title.'</title>';
 			echo '<link rel="stylesheet" type="text/css" href="/stylex">';
 			echo '<link rel="stylesheet" type="text/css" href="'.$style_url.'"/>';
			echo '<link type="application/rss+xml" rel="alternate" href="'.$url.'rss"/>';
 			echo '<script type="text/javascript" src="/jscripts"></script>';
 		echo '</head>';
 		echo '<body>';

		echo '<div id="loginbg"></div><div id="loginform"></div><div class="disable-block"></div>';
 		?>
		<div class="header">
			<div class="top">
				<div class="wrapper1000">
					<a class="logo" href="/"><img src="/style/images/logo.png" alt="" /></a>
					<a class="button" href="#" onclick="startdemo('')"><span style="top:2px;font-size:17px;">Попробовать</span></a>
				</div> <!-- end wrapper1000 -->
			</div> <!-- end top -->
			<div class="topMenu">
				<ul>
					<?
					$keys = array_keys ( $menu_items );

					foreach( $keys as $key )
					{
						if ( $_REQUEST['mode'] == $key )
						{
							$class = 'current';
						}
						else
						{
							$class = '';
						}
						echo '<li class="'.$class.'">';
							echo '<span><a href="'.$url.($key != 'features' ? $key : '').'">'.$menu_items[$key].'</a></span>';
						echo '</li>';
					}
					?>
				</ul>
			</div> <!-- end topMenu -->
		</div> <!-- end header -->

		<div class="content">
			<?
				$this->table->draw();
			?>
			<div class="darkBg">
				<div class="wrapper1000">
				</div> <!-- end wrapper1000 -->
			</div> <!-- end darkBg -->
			<div class="lightDarkBg">
				<div class="wrapper1000">
					<div class="sidebar">
						<div class="title">
							<h3><b>Карта сайта</b></h3>
						</div> <!-- end title -->
						<ul class="mapSite">
						<?
						foreach( $menu_items as $key => $item )
						{
							echo '<li class="'.$class.'">';
								echo '<a href="'.$url.$key.'">'.$item.'</a>';
							echo '</li>';
						}
						?>
						</ul>
					</div> <!-- end sidebar -->
					<div class="rightSide">
						<div class="title">
							<h3><b>Новости</b></h3>
						</div> <!-- end title -->
						<div class="news">
							<?
							$post = $model_factory->getObject('BlogPost');
							$post_it = $post->getByRefArray(
								array( 'Blog' => $project_it->get('Blog') ), 3);
												
							while( !$post_it->end() )
							{
								echo '<div class="item">';
									echo '<h4><a href="'.SitePageUrl::parse($post_it).'">'.
											$post_it->getDisplayName().'</a></h4>';
								echo '</div>';	

								$post_it->moveNext();
							} 		 		
							?>
						</div> <!-- end news -->
					</div> <!-- end rightSide -->
					<div class="clearFix">
					</div>
				</div> <!-- end wrapper1000 -->
			</div> <!-- end lightDarkBg -->
		</div> <!-- end content -->

		<div class="footer">
			<div class="wrapper1000">
				<p class="valid" style="margin-top:6px;">Мы принимаем <img style="margin-bottom:-12px" src="/images/mastercard-curved-32px.png"> <img style="margin-bottom:-12px" src="/images/visa-curved-32px.png"></p>
				<p class="copy">Copyright © Devprom <?php echo date('Y'); ?>. Все права защищены.</p>
				<a class="goTop" href="#">Наверх</a>
				<div class="clearFix">
				</div>
			</div> <!-- end wrapper1000 -->
		</div> <!-- end footer -->

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
		var gaJsHost = (("https:" == document.location.protocol) ? "https://" : "http://");
		document.write(unescape("%3Cscript src='" + gaJsHost + "stats.g.doubleclick.net/dc.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
		try {
		var pageTracker = _gat._getTracker("UA-10541243-1");
		pageTracker._trackPageview();
		} catch(err) {}</script>
 		
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter22082527 = new Ya.Metrika({id:22082527,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/22082527" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter —>

		<script type="text/javascript">
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
		try {
		var pageTracker = _gat._getTracker("UA-10541243-1");
		pageTracker._trackPageview();
		} catch(err) {}</script>

		<!-- Yandex.Metrika counter -->
		<script type="text/javascript">
		(function (d, w, c) {
		    (w[c] = w[c] || []).push(function() {
		        try {
		            w.yaCounter22082527 = new Ya.Metrika({id:22082527,
		                    webvisor:true,
		                    clickmap:true,
		                    trackLinks:true,
		                    accurateTrackBounce:true});
		        } catch(e) { }
		    });

		    var n = d.getElementsByTagName("script")[0],
		        s = d.createElement("script"),
		        f = function () { n.parentNode.insertBefore(s, n); };
		    s.type = "text/javascript";
		    s.async = true;
		    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

		    if (w.opera == "[object Opera]") {
		        d.addEventListener("DOMContentLoaded", f, false);
		    } else { f(); }
		})(document, window, "yandex_metrika_callbacks");
		</script>
		<noscript><div><img src="//mc.yandex.ru/watch/22082527" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
		<!-- /Yandex.Metrika counter -->

<script type="text/javascript">var _kmq = _kmq || [];
var _kmk = _kmk || '129d4c88ff89b2ca2cbd44cb2d0577602e5b7391';
function _kms(u){
  setTimeout(function(){
    var d = document, f = d.getElementsByTagName('script')[0],
    s = d.createElement('script');
    s.type = 'text/javascript'; s.async = true; s.src = u;
    f.parentNode.insertBefore(s, f);
  }, 1);
}
_kms('//i.kissmetrics.com/i.js');
_kms('//doug1izaerwt3.cloudfront.net/' + _kmk + '.1.js');
</script>
		
		<?
		
 		echo '</body>';
 		echo '</html>';
 	}
 }

