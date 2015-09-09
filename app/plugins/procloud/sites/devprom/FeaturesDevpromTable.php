<?php

class FeaturesDevpromTable extends BaseDEVPROMTable
{
 	var $page_it;
 	
 	function __construct()
 	{
 		global $model_factory, $_REQUEST;
 		
 		parent::__construct();

 		$root_it = $this->getObjectIt();
 		
 		if ( $_REQUEST['id'] != '' )
 		{
 			$page = $model_factory->getObject('ProjectPage');
 			
 			$page_it = $page->getExact( $_REQUEST['id'] );
 			
 			if ( $page_it->getId() < 1 )
 			{
				$obsolete = $this->getObsolete();
				
				$redirect = $obsolete[urldecode(IteratorBase::utf8towin($_REQUEST['id']))];
				
				if ( $redirect != '' ) 
				{
					header($_SERVER["SERVER_PROTOCOL"]." 301 Moved Permanently");
					
					if ( strpos($redirect, 'http:') !== false )
					{
						exit(header('Location: '.IteratorBase::wintoutf8($redirect)));
					}
					else
					{
						exit(header('Location: /features/'.IteratorBase::wintoutf8($redirect)));
					}
				}
	
				throw new Exception('404');
 			}
 			
 			$parent_it = $page_it->getRef('ParentPage');

 			if ( $parent_it->get('ParentPage') == $root_it->getId() )
 			{
 				$this->page_it = $page_it;
 			}
 		}
 		else
 		{
 			$this->page_it = getFactory()->getObject('ProjectPage')->getExact( 25701 );
 		}

		if ( !is_object($this->page_it) ) throw new Exception('404');
		
		if ( $this->page_it->getId() < 1 ) throw new Exception('404');
 	}
 	
 	function getObsolete()
 	{
 		return array (
 				'Поддержка-программных-продуктов' => 'Решение-по-организации-поддержки-программных-продуктов',
 				'Ведение-Agile-проектов' => 'Решение-по-управлению-Agile-проектами-на-примере-методологии-Scrum',
 				'Управление-тестированием-продуктов' => 'Система-управления-тестированием-Devprom-QA',
 				'Применение-Kanban-для-разработки' => 'Решение-по-применению-Kanban-в-проекте-разработки',
 				'Решение-по-применению-Kanban-в-проекте-разработки' => 'Решение-по-применению-Kanban-в-проекте-разработки',
 				'Управление-требованиями-и-изменениями-для-линейки-продуктов' => 'Решение-по-управлению-требованиями-и-изменениями-для-линейки-продуктов',
 				'Поддержка-различных-процессов' => 'Система-управления-жизненным-циклом-разработки-ПО-Devprom-ALM',
 				'Интеграция-с-существующими-инструментами' => 'Система-управления-жизненным-циклом-разработки-ПО-Devprom-ALM',
 				'Организация-работы-службы-поддержки' => 'Организация-работы-службы-технической-поддержки-с-Devprom-Service-Desk',
 				'Трассировка-межпроектных-задач' => 'Система-управления-жизненным-циклом-разработки-ПО-Devprom-ALM',
 				'Управление-портфелями-проектов' => 'Координация-всех-активностей-по-компании',
 				'Контроль-загрузки-сотрудников-компании' => 'Планирование-балансировка-и-контроль-загрузки-ресурсов',
 				'Сводная-отчетность-по-проектам' => 'Планирование-балансировка-и-контроль-загрузки-ресурсов',
 				'Система-управления-жизненным-циклом-разработки-ПО-Devprom-ALM' => 'http://devprom.ru',
 				'Решение-по-управлению-тестированием-продуктов' => 'Система-управления-тестированием-Devprom-QA',
 				'Решение-по-применению-Kanban-в-проекте-разработки' => 'http://club.devprom.ru/agile/RAZRABOTKA-PO-kanban/'
 		);
 	}
 	
 	function getTitle()
	{
		if ( is_object($this->page_it) )
		{
			return $this->page_it->getDisplayName();
		}
		else
		{
			return parent::getTitle();
		}
	}
 	
	function draw()
 	{
 		$root_it = $this->getObjectIt();
 		if ( $_REQUEST['id'] == '' )
 		{
 			$this->drawMain( $root_it, $this->page_it );
 		}
 		else
 		{
 			$this->drawDetails( $root_it, $this->page_it );
 		}
 	}
 	
 	function drawMain($root_it, $page_it)
 	{
 		global $project_it;
 		
		?>
		<div class="wrapper1000">
			<ul class="menu2">
			<? 		
			$parent_id = $root_it->getId();
	 		$children_it = $root_it->getChildrenIt();
	 		
	 		while ( !$children_it->end() && $children_it->get('ParentPage') == $parent_id )
	 		{
	 			$section_id = $children_it->getId();
	 			$items_it = $children_it->getChildrenIt();
	 			$url = $items_it->getSearchName();
	 			$children_it->moveToId($section_id);
	 			 
		 		?>
				<li>
					<a href="/features/<? echo $url ?>">
						<span><? echo $children_it->getDisplayName() ?></span>
					</a>
				</li>
		 		<?
	 			$children_it->moveNext();
	 		}
	 		?>
			</ul>
			<div class="clearFix">
			</div>
		</div> <!-- end menu2 -->
		
		<div class="whiteRounded" style="margin-bottom:0;">
			<div class="bgTop">
			</div> <!-- end bgTop -->
			<div class="bgCenter">
				<div class="text wiki">
					<h2>Devprom ALM - платформа для поддержки процессов разработки ПО</h2>
					<p><ul>
					<li>Основа эффективных процессов командной работы над проектами в вашей компании</li>
					<li>Поддержка полного жизненного цикла разработки программного обеспечения (Application Lifecycle Management)</li>
					<li>Полноценная трассировка всех проектных артефактов, от исходных требований до кода, тестов и работающего продукта</li>
					<li>Гибкая настройка под любой процесс, используемый в вашей компании (Agile, Waterfall, гибридный)</li>
					<li>Быстрая установка и внедрение, высокая скорость обучения сотрудников, низкая стоимость обслуживания</li>
					</ul></p>
					<br/>
				</div>		
			</div>	
		</div> <!-- end whiteRounded -->
		
	<div class="urls">
          <div class="wrapper1000">
            <div class="register-form">
                <form id="try-form" class="form-inline" role="form">
                    <div class="form-group">
                        <input type="text" class="form-control" id="try-form-instance" placeholder="Название компании">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="try-form-username" placeholder="Ваше имя">
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" id="try-form-email" placeholder="Email">
                    </div>
                    <div class="form-message" style="color:white;display:none;">
                        Ваш экземпляр Devprom ALM уже почти готов, пожалуйста, подождите немного.
                    </div>
                    <button type="button" class="btn btn-success" onclick="javascript: createinstance();">Начать работу</button>
                </form>
                <div class="alert alert-danger" id="try-form-result" style="display:none;"></div>
            </div>
          </div>               
	</div>
		
		<div class="whiteRounded" style="margin-top:0;">
			<div class="bgCenter">
				<div class="text wiki">
					<?
			 		$parser = new DEVPROMWikiParser( $page_it, $project_it );
					echo $parser->parse();
					?>
					<div class="comments" style="background-image:none;">
						<div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like ref="top_left" show_faces="false" layout="standard" width="270px"></fb:like></div>
					</div>
				</div> <!-- end text -->
				<div class="clearFix">
				</div>
			</div> <!-- end bgCenter -->
			<div class="bgBottom">
			</div> <!-- end bgBottom -->
		</div> <!-- end whiteRounded -->
		
		<div style="text-align:center;background:white;margin-top:25px;padding-top:25px;padding-bottom:25px;">
		<img style="vertical-align:middle;padding-right: 20px;padding-left: 20px;" src="/style/images/sprite_logo1.png">
		</div>

	<div class="support">
		<div class="wrapper1000">
			<div class="lines">
				<div class="consultation">
                        <h2>Нужна консультация?</h2>
                        <br>
                        <a class="skype" href="skype:dmitry.lobasev?chat">devprom</a> <a class="tel" href="/contacts">+7 (499) 638-64-11</a>
                    </div>

                    <div class="join">
                        <h2>Присоединяйтесь!</h2>
						<br>
                        <p><a class="facebook" href="http://facebook.com/Devprom">Следите за новостями</a> <a class="rss" href="/rss">Подпишитесь на RSS</a></p>
                    </div>

				<div class="clearFix">
				</div>
			</div>
		</div>
	</div>		
		<? 		
 	}
 	
 	function drawDetails( $root_it, $page_it )
 	{
 		global $project_it;

		?>
		<div class="wrapper1000">
			<ul class="menu2">
			<? 		
			$parent_id = $root_it->getId();
	 		$children_it = $root_it->getChildrenIt();
	 		
	 		while ( !$children_it->end() && $children_it->get('ParentPage') == $parent_id )
	 		{
	 			$class = $children_it->getId() == $page_it->get('ParentPage') ? 'current' : '';
	 			
	 			$section_id = $children_it->getId();
	 			$items_it = $children_it->getChildrenIt();
	 			$url = $items_it->getSearchName();
	 			$children_it->moveToId($section_id);
	 			 
		 		?>
				<li class="<? echo $class ?>">
					<a href="/features/<? echo $url ?>">
						<span><? echo $children_it->getDisplayName() ?></span>
					</a>
				</li>
		 		<?
	 			$children_it->moveNext();
	 		}
	 		?>
			</ul>
			<div class="clearFix">
			</div>
		</div> <!-- end menu2 -->
		<div class="whiteRounded">
			<div class="bgTop">
			</div> <!-- end bgTop -->
			<div class="bgCenter">
				<div class="leftSide">
					<div class="text wiki">
					<?
			 		$parser = new DEVPROMWikiParser( $page_it, $project_it );
					echo $parser->parse();
					?>
					<div class="comments" style="background-image:none;">
						<div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like ref="top_left" show_faces="false" layout="standard" width="270px"></fb:like></div>
					</div>
					
					<?php

					$map = $this->getPage2TagsMap();
					if ( count($map[$page_it->get('ParentPage')]) > 0 )
					{
					?>
						<br/>
						<h3>Еще интересные статьи на эту тему:</h3>
						<br/>
						<ul>
						<?php 
							$ids = getFactory()->getObject('BlogPostTag')->getPostsByTag( array_pop($map[$page_it->get('ParentPage')]) )->idsToArray();
							$post_it = getFactory()->getObject('BlogPost')->getByRefArray(array( 'BlogPostId' => $ids ), 5);
						
							while(!$post_it->end() )
							{
								echo '<li><a href="/news/'.$post_it->getSearchName().'">'.$post_it->getDisplayName().'</a></li>';
								$post_it->moveNext();
							} 
						?>
						</ul>
					<?php 
					}
					?>
					
					</div> <!-- end text -->
				</div> <!-- end leftSide -->
				<div class="rightSideBar">
					<ul class="rightMenu">
					<?
					$page_id = $page_it->getId();
					$parent_it = $page_it->getRef('ParentPage');
					
					$index = 1;
					$parent_id = $parent_it->getId();
	 				$children_it = $parent_it->getChildrenIt();

					while ( !$children_it->end() && $children_it->get('ParentPage') == $parent_id  )
					{
	 					$class = $children_it->getId() == $page_it->getId() ? 'current' : '';
						$tag_it = $children_it->getTagsIt();
						?>
						<li class="<?php echo $class; ?>">
							<a class="<? echo $tag_it->getDisplayName() ?>" href="/features/<?php echo $children_it->getSearchName(); ?>" seq="<? echo $index ?>">
								<? echo $children_it->getDisplayName() ?>
							</a>
						</li>
						<?
						$children_it->moveNext();
						$index++;
					}
					?>
					<?php 
					$map = $this->getPage2ClubMap();
					if ( $map[$page_it->get('ParentPage')] != '' )
					{
					?>
						<li>
							<a href="<?=$map[$page_it->get('ParentPage')]?>" seq="<? echo $index ?>">
								Больше сценариев использования читайте в <b>Клубе Agile ALM</b>
							</a>
						</li>
					<?php
					}
					?>
					</ul>
				</div> <!-- end rightSideBar -->
				<div class="clearFix">
				</div>
			</div> <!-- end bgCenter -->
			<div class="bgBottom">
			</div> <!-- end bgBottom -->
		</div> <!-- end whiteRounded -->
		
		<script type="text/javascript">
			$(document).ready(function() {
				$('.rightMenu li a')
					.click( function() {
						$('.rightMenu li').attr('class', '');
						$(this).parent().attr('class', 'current');
					});
				
				var parts = window.location.toString().split('#');
				if ( parts.length > 1 )
				{
					$('.rightMenu li a[seq='+parts[1]+']').trigger('click');
				}
			});
		</script>
 		<?
 	}
 	
  	function getKeywords()
	{
		global $model_factory;
		
		if ( !is_object($this->page_it) ) return parent::getKeywords();
		
		$tag = $model_factory->getObject('WikiTag');
		
		$tag_it = $tag->getByRef('Wiki', $this->page_it->getId());
		
		return $tag_it->fieldToArray('Caption');
	}

	function getDescription()
	{
		$text = new html2text(preg_replace('/h\d/','p',$this->page_it->getHtmlDecoded('Content')));
		return trim(preg_replace('/\[[^\]]+\]/','',
            str_replace($this->getTitle(), '',
                preg_replace('/[\r\n\s]+/', ' ', substr($text->get_text(), 0, 320))
            )
        ));
	}

	function getPage2TagsMap()
	{
		return array (
			25698 => array(1536), // ALM
			10901 => array(60), // Agile
			25702 => array(422), // Requirements
			25703 => array(147), // QC
			69380 => array(184), // Tech Writing
			27398 => array() // SD
		);
	}
	
	function getPage2ClubMap()
	{
		return array (
			25698 => 'http://club.devprom.ru/alm/',
			10901 => 'http://club.devprom.ru/agile/',
			25702 => 'http://club.devprom.ru/requirements/',
			25703 => 'http://club.devprom.ru/qa/',
			69380 => 'http://club.devprom.ru/writing/',
			27398 => 'http://club.devprom.ru/devops/'
		);
	}
}
