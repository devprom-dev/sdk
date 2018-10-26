<?php

include_once SERVER_ROOT_PATH."pm/classes/wiki/WikiPageModelExtendedBuilder.php";

class DocsDEVPROMTable extends BaseDEVPROMTable
{
 	function __construct()
 	{
 		global $model_factory, $_REQUEST;
 		
 		getSession()->addBuilder( new WikiPageModelExtendedBuilder() );
 		
		$doc = $model_factory->getObject('HelpPage');
		$doc->addFilter( new WikiNotArchivedPredicate() );
		
 		if ( $_REQUEST['id'] != '' )
 		{
			$current_it = $doc->getExact( $_REQUEST['id'] );
			
			if ( $current_it->getId() < 1 )
			{
				$obsolete = $this->getObsolete();
				
				$redirect = $obsolete[IteratorBase::utf8towin($_REQUEST['id'])];
				
				if ( $redirect != '' ) 
				{
					header($_SERVER["SERVER_PROTOCOL"]." 301 Moved Permanently");
					
					exit(header('Location: /docs/'.IteratorBase::wintoutf8($redirect)));
				}

				throw new Exception('404');
			}
			
			if ( $current_it->IsArchived() ) throw new Exception('404');
			
			$this->setObjectIt( $current_it );
 		}
 		else
 		{
			$doc->addFilter( new WikiRootFilter() );
			$doc->addSort( new SortOrderedClause() );
			
			$doc_it = $doc->getFirst();

			$doc->resetFilters();

			$this->setObjectIt( $doc_it );
 		}
 	}
 	
 	function getObsolete()
 	{
 		return array (
 				'Общие-настройки-системы' => 'Настройки-системы',
 				'Анализ-загрузки-человеческих-ресурсов' => 'Анализ-загрузки-сотрудников-задачами',
 				'Программные-интерфейсы' => 'Программные-интерфейсы-API-',
 				'Документация' => 'Руководство-пользователя',
 				'Общее-описание' => 'Руководство-пользователя',
 				'C-пример-организации-обратной-связи' => 'Пример-использования-Devprom-API',
 				'FAQ' => 'Часто-задаваемые-вопросы',
 				'Проектирование' => 'Возможности-проектирования',
 				'Коммуникации' => 'Коммуникации-в-командах-и-проектах',
 				'Документация-пользователя' => 'Руководство-пользователя',
 				'Анализ-загрузки-сотрудников-задачами' => 'Руководство-пользователя', 
 				'Введение' => 'Руководство-пользователя',
 				'Декомпозиция-на-задачи' => 'Руководство-пользователя',
 				'Руководство-пользователя' => 'Руководство-пользователя',
 				'Документация-Devprom-ALM' => 'Руководство-пользователя',
 				'Дополнительные-возможности-Devprom-ALM' => 'Руководство-пользователя',
 				'Дополнительные-шаблоны-проектов' => 'Руководство-пользователя',
 				'Избранное' => 'Руководство-пользователя',
 				'Интеграция-в-инфраструктуру-компании' => 'Руководство-пользователя',
 				'Кросспроектные-отчеты' => 'Руководство-пользователя',
 				'Настройка-портала-поддержки-ServiceDesk' => 'Руководство-пользователя',
 				'Настройки' => 'Руководство-пользователя',
 				'Организация-обратной-связи-с-пользователями' => 'Руководство-пользователя',
 				'Организация-службы-поддержки' => 'Руководство-пользователя',
 				'Отчеты-по-задачам' => 'Руководство-пользователя',
 				'Отчет-по-затраченному-времени' => 'Руководство-пользователя',
 				'Поддержка-по-электронной-почте' => 'Руководство-пользователя',
 				'Пользовательские-настройки' => 'Руководство-пользователя',
 				'Программы-и-подпроекты' => 'Руководство-пользователя',
 				'Проекты' => 'Руководство-пользователя',
 				'Связанные-проекты' => 'Руководство-пользователя',
 				'Стили-заголовков' => 'Руководство-пользователя',
 				'Термины' => 'Руководство-пользователя',
 				'Установка-на-Linux' => 'Руководство-пользователя',
 				'Шаблоны-уведомлений' => 'Руководство-пользователя'
 		);
 	}
 	
 	function draw()
 	{
 		global $project_it, $model_factory, $methodology_it, $_REQUEST;
 		
		$doc = $model_factory->getObject('HelpPage');
		
		$doc->addSort( new SortOrderedClause() );
		$doc->addFilter( new WikiNotArchivedPredicate() );
		$doc->addFilter( new WikiRootFilter() );
		
		$current_it = $this->getObjectIt();
		
		$parent_it = $current_it->getRootIt();
 		
		?>
		<div class="wrapper1000">
			<ul class="menu2 paddingLeft20">
				<?
				$doc_it = $doc->getAll();
				$doc->resetFilters();
				
				while ( !$doc_it->end() )
				{
					$class = $parent_it->getId() == $doc_it->getId() ? 'current' : '';
					echo '<li class="'.$class.'"><a href="/docs/'.$doc_it->getSearchName().'"><span>'.$doc_it->getDisplayName().'</span></a></li>';
					
					$doc_it->moveNext();
				}
				?>
			</ul>
			<div class="clearFix">
			</div>
			<?
				$this->drawDetails( $current_it );
			?>
		</div>
		<? 		
 	}
 	
 	function drawDetails( $wiki_it )
 	{
 		global $model_factory, $project_it;
 		
 		$root_it = $wiki_it->getRootIt();
 		
		?>
		<div class="leftRound">
			<div class="bgTop">
			</div> <!-- end bgTop -->
			<div class="bgCenter">
				<div class="downloadDoc">
					<a href="<? echo $this->getPdfExportUrl($root_it) ?>"><img src="/style/images/pdf.png" alt="" /></a>
				</div> <!-- end downloadDoc -->
				<h3 class="orang"><? echo $root_it->getDisplayName() ?></h3>
				<div class="clearFix">
				</div>
				<?
				$this->drawSection( $wiki_it );
				?>				
			</div> <!-- end bgCenter -->
			<div class="bgBottom">
			</div> <!-- end bgBottom -->
		</div>
		<div class="maintenance">
			<h3><b>Содержание</b></h3>
			<?
			$this->drawIndex( $root_it );
			?>
		</div>
		<div class="clearFix">
		</div>
		<?
 	}
 	
 	function drawSection( $wiki_it )
 	{
 		global $project_it, $model_factory;
 		
 		if ( $wiki_it->get('ParentPage') != '' ) {
		?>
		<h4 class="titleWithBorder"><? echo $wiki_it->getDisplayName() ?></h4>
		<?php } ?>
		<p>
		<div class="wiki">
		<?
		$parser = new SiteWikiParser($wiki_it, $project_it);
		echo $parser->parse();
		?>
		</div>
		</p>
		<?
		if ( $wiki_it->get('TotalCount') > 0 )
		{
			$children_it = $wiki_it->getChildrenIt();
			
			while ( !$children_it->end() && $children_it->get('ParentPage') == $wiki_it->getId() )
			{
				echo '<p><a href="/docs/'.$children_it->getSearchName().'">'.$children_it->getDisplayName().'</a></p>';
				$children_it->moveNext();
			}
		}
		else
		{
			$parents = $wiki_it->getTransitiveRootArray();
			
			$last_item_id = $wiki_it->getId();
			
			foreach( $parents as $parent_id )
			{
				$object = $model_factory->getObject(get_class($wiki_it->object));
				
				$object->addFilter( new FilterAttributePredicate('ParentPage', $parent_id) );
				
				$object->addSort( new SortOrderedClause() );
				
				$children_it = $object->getAll();
				
				$children_it->moveToId( $last_item_id );
	
				$children_it->moveNext();
				
				if ( !$children_it->end() )
				{
					echo '<p>Далее:</p>';
					
					while ( ! $children_it->end() )
					{
						echo '<p><a href="/docs/'.$children_it->getSearchName().'">'.$children_it->getDisplayName().'</a></p>';
						
						$children_it->moveNext();
					}
					
					return;
				}
				
				
				$last_item_id = $parent_id;
			}
		}			
 	}
 	
 	function drawIndex( $wiki_it, $level = 0 )
 	{
 		$parent_id = $wiki_it->getId();
		$children_it = $wiki_it->getChildrenIt();
		
		if ( $children_it->get('ParentPage') != $parent_id )
		{
			return;
		}
		
		switch ( $level )
		{
			case 0:
				$class = 'thirstLevel';
				break;
				
			case 1:
				$class = 'secondLevel';
				break;
				
			default:
				$class = 'therdLevel';
				break;
		}

 		?>
		<ul class="<? echo $class ?>">
 		<?

		while ( !$children_it->end() && $children_it->get('ParentPage') == $parent_id )
		{
			$title = $children_it->getDisplayName();
			if ( $level == 0 )
			{ 
				$title = '<b>'.$title.'</b>';
			}
			?>
			<li><span class="topPl"></span><span class="bottomPl"><a href="/docs/<?=$children_it->getSearchName()?>"><? echo $title ?></a></span></li>
			<?
			$id = $children_it->getId();
			$this->drawIndex( $children_it, $level + 1 );

			$children_it->moveToId( $id );
			$children_it->moveNext();
		}
		?>
		</ul>
		<?
 	}

	function getKeywords()
 	{
		return array (
			translate('канбан'),
			translate('доска'),
			translate('скрам'),
			translate('итерация'),
			translate('приоритезация'),
			translate('бэклог'),
			translate('alm'),
			translate('тестовый')
		);
 	}
}
