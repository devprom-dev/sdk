<?php

/////////////////////////////////////////////////////////////////////////////////
class CoCataloguePageContent extends CoPageContent
{
	var $profile_it;
	
	function validate()
	{
		global $project_it, $_REQUEST, $model_factory, $user_it;
		
		if ( $_REQUEST['action'] != '' )
		{
			switch ( $_REQUEST['action'] )
			{
				case 'tag':
					$tag = $model_factory->getObject('pm_ProjectTag');
					$this->tag_it = $tag->getExact($_REQUEST['id']);
					
					if ( $this->tag_it->count() < 1 )
					{
						return false;
					}
					
					break;

				case 'search':
					break;
					
				default:
					return false;
			}
		}

		return true;
	}
	
	function getTitle()
	{
		if ( is_object($this->tag_it) )
		{
			return $this->tag_it->getDisplayName().' - '.parent::getTitle();
		}
		else
		{
			return translate('Каталог проектов').' - '.parent::getTitle();
		}
	}
	
	function getKeywords()
	{
		$words = array ( 
			translate('каталог'), 
			translate('проект'), 
			translate('проекты'), 
			translate('продукт'), 
			translate('devprom'), 
			translate('сайт'), 
			translate('тэги'), 
			translate('поиск'), 
			translate('найти'), 
			translate('участник'), 
			translate('облако'), 
			translate('пользователь'), 
			translate('использовать') 
		);
		
		return join($words, ' ');
	}
	
	function getDescription()
	{
		return text('procloud566');
	}

	function draw()
	{
		global $model_factory, $_REQUEST, $project_it;
		
		$page = $this->getPage();
		$project = $model_factory->getObject('pm_Project');
		
		echo '<div style="float:left;width:100%;">';
			echo '<div id="grbutton" style="width:220px;">';
				echo '<div id="lt"></div>';
				echo '<div id="bd"><div style="padding-top:4px;"><a href="/projects">'.translate('Проекты').'</a></div></div>';
				echo '<div id="rt"></div>';
				echo '<div id="an"></div>';
			echo '</div>';
		echo '</div>';

		echo '<div style="clear:both;width:100%;"></div>';
		echo '<br/>';

		echo '<div id="bloglist">';
			
			switch ( $_REQUEST['action'] )
			{
				case 'tag':
					$protag = $model_factory->getObject('pm_ProjectTag');
					$protag_it = $protag->getExact($_REQUEST['id']);

					$project_it = $project->getInArray('pm_ProjectId', 
						$protag_it->fieldToArray('Project'));
						
					break;
					
				case 'search':
					$project_it = $project->search($_REQUEST['id']);
					break;

				default:
					$projects_count = $project->getLatestMostUsedPublicCount();
					$project_it = $project->getLatestMostUsedPublicIt( 20, $_REQUEST['page'] );
					break;
			}
			
			$this->drawProjects( $projects_count );
			
		echo '</div>';

		echo '<div id="user_actions">';
			echo '<div class="action_box">';
				$page->drawGreyBoxBegin();

				echo '<div id="title">';
					echo translate('Поиск');
				echo '</div>';

				echo '<div id="search">';
					echo '<div id="bds">';
						echo '<div id="lt"></div>';
						$value = ($_REQUEST['id'] != '' ? $project->utf8towin($_REQUEST['id']) : translate('поиск проекта'));
						$script = "if ( $('#searchval').val() == '".$value."') $('#searchval').val('');";
						echo '<input id="searchval" value="'.$value.'" onfocus="'.$script.'">';
						echo '<div id="rt"><a href="javascript: searchProject();">&nbsp;&nbsp;</a></div>';
					echo '</div>';
				echo '</div>';

				$page->drawGreyBoxEnd();
			echo '</div>';

			echo '<div class="action_box">';
				$page->drawGreyBoxBegin();

				echo '<div id="title">';
					echo translate('Тэги');
				echo '</div>';

				echo '<div id="body">';
					$tag = $model_factory->getObject('pm_ProjectTag');
					
					$sql = "SELECT t.*, COUNT(1) ItemsCount " .
						   "  FROM pm_ProjectTag t, pm_PublicInfo i" .
						   " WHERE i.Project = t.Project" .
						   "   AND i.IsProjectInfo = 'Y' " .
						   " GROUP BY t.Caption HAVING COUNT(1) > 1 " .
						   " ORDER BY t.Caption";
						   
					$tag_it = $tag->createSQLIterator($sql);
			
					for( $i = 0; $i < $tag_it->count(); $i++ )
					{
						$style = 'style="font-size:'.min(7 + floor($tag_it->get('ItemsCount') / 2), 12).
							'pt;font-weight:'.min(100 * round($tag_it->get('ItemCount') / 2), 600).';"';
							
						echo '<a '.$style.' href="'.ParserPageUrl::parse($tag_it).'">'.
							str_replace(' ', '&nbsp;', $tag_it->getDisplayName()).'</a><sup>'.$tag_it->get('ItemCount').'</sup>';
							
						if ( $i < $tag_it->count() - 1 ) echo ' &nbsp; &nbsp; ';
						$tag_it->moveNext();
					}
				echo '</div>';

				$page->drawGreyBoxEnd();
			echo '</div>';
		echo '</div>';

		
		echo '<div style="clear:both;width:100%;"></div>';
		echo '<br/>';
		
		?>
		<script type="text/javascript">
			$(document).ready(function() {
				  $("#search input").keydown(
				     function(e){
				       var key = e.charCode || e.keyCode || 0;
				       if ( key == 13 ) searchProject();
				     }
				  );
			});
		</script>
		<?
	}

	function drawProjects( $total = 0 )
	{	
		global $_REQUEST, $model_factory, $project_it, $user_it;

		$model_factory->enableVpd(false);
		$page = $this->getPage();

		// collect projects tags
		$protag = $model_factory->getObject('pm_ProjectTag');
		
		$protag_it = $protag->getByRefArray(
			array( 'Project' => $project_it->idsToArray() ) 
			);

		$tags = array();
		while ( !$protag_it->end() )
		{
			if ( !is_array($tags[$protag_it->get('Project')]) )
			{
				$tags[$protag_it->get('Project')] = array();
			}
			
			array_push( $tags[$protag_it->get('Project')], 
				'<a class="tag" href="'.ParserPageUrl::parse($protag_it).'">'.$protag_it->getDisplayName().'</a>' );
				
			$protag_it->moveNext();
		}
		
		// collect projects participants
		$part = $model_factory->getObject('pm_Participant');
		
		$sql = " SELECT COUNT(1) cnt, p.Project" .
			   "   FROM pm_Participant p" .
			   "  WHERE p.IsActive = 'Y' " .
			   "    AND p.Project IN (".join($project_it->idsToArray(), ',').")".
			   "  GROUP BY p.Project ";

		$part_it = $part->createSQLIterator($sql);
		
		$participants = array();
		while ( !$part_it->end() )
		{
			$participants[$part_it->get('Project')] = $part_it->get('cnt');
			$part_it->moveNext();
		}

		// collect projects subscriptions
		$part = $model_factory->getObject('co_ProjectSubscription');
		
		$sql = " SELECT COUNT(1) cnt, p.Project" .
			   "   FROM co_ProjectSubscription p" .
			   "  WHERE p.Project IN (".join($project_it->idsToArray(), ',').")".
			   "  GROUP BY p.Project ";

		$part_it = $part->createSQLIterator($sql);
		
		$users = array();
		while ( !$part_it->end() )
		{
			$users[$part_it->get('Project')] = $part_it->get('cnt');
			$part_it->moveNext();
		}

		// iterate through projects
		$project = $model_factory->getObject('pm_Project');
		
		$sql = " SELECT p.* " .
			   "   FROM pm_Project p, pm_PublicInfo i" .
			   "  WHERE p.pm_ProjectId IN (".join($project_it->idsToArray(), ',').")" .
			   "    AND p.pm_ProjectId = i.Project" .
			   "    AND i.IsProjectInfo = 'Y' ".
			   "  ORDER BY (SELECT COUNT(1) FROM co_ProjectSubscription cos" .
			   "		      WHERE cos.Project = p.pm_ProjectId) DESC ";

		$project_it = $project->createSQLIterator($sql);

		$page->drawWhiteBoxBegin();
		$projects_found = false;

		while ( !$project_it->end() )
		{
			echo '<h2><a href="'.ParserPageUrl::parse($project_it).'">'.
				$project_it->getWordsOnly('Caption', 5).'</a></h2>';
			
			echo '<div>';
				echo $project_it->getWordsOnly('Description', 30);
			echo '</div>';	
			
			echo '<div style="padding-top:3px;">';
				echo join($tags[$project_it->getId()], ', ');
			echo '</div>';	

			echo '<div style="padding-top:3px;clear:both;">';
				echo translate('Участников').': '.$participants[$project_it->getId()].
					', '.translate('Пользователей').': '.( $users[$project_it->getId()] == '' ? 0 : $users[$project_it->getId()]);
			echo '</div>';	

			echo '<br/>';	
			echo '<br/>';	
			
			$projects_found = true;
			$project_it->moveNext();
		}
		
		if ( !$projects_found )
		{
			echo text('procloud513');
		}
		else
		{
			$this->drawPaging( $total, 20 );
		}
					
		$page->drawWhiteBoxEnd();

		$model_factory->enableVpd(true);
	}
}

?>
