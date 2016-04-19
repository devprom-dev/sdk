<?php

class TrainingsDEVPROMTable extends BaseDEVPROMTable
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
 			
 			if ( in_array($root_it->getId(), $page_it->getParentsArray()) )
 			{
 				$this->page_it = $page_it;
 			}
 		}
 		else
 		{
 			$parent_it = $root_it->getChildrenIt();
 			$it = $parent_it->getChildrenIt();

 			$page = $model_factory->getObject('ProjectPage');
 			$this->page_it = $page->getExact( $it->getId() );
 		}

		if ( !is_object($this->page_it) ) exit(header('Location: /404'));
		if ( $this->page_it->getId() < 1 ) exit(header('Location: /404'));
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
 		global $model_factory;
 		
 		$root_it = $this->getObjectIt();
		
		$tag = $model_factory->getObject('WikiTag');
		
		$tag->setAttributeType('Wiki', 'REF_'.get_class($root_it->object).'Id'); 
		
		$tag_it = $tag->getByRefArray( array (
				'Wiki' => $this->page_it->getParentsArray()
		));
		
 		if ( in_array('instruction', $tag_it->fieldToArray('Caption')) )
 		{
 			$tag_it->moveTo('Caption', 'instruction');
 			 
 			$this->drawInstruction( $root_it, $tag_it->getRef('Wiki'), $this->page_it );
 		}
 		else
 		{
 			$this->drawDetails( $root_it, $this->page_it );
 		}
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
					<a href="/trainings/<? echo $url ?>">
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
		<?
		$index = 1;
		$parent_it = $page_it->getRef('ParentPage');
		$parent_id = $parent_it->getId();
		$children_it = $parent_it->getChildrenIt();
		$count = 0;
		while ( !$children_it->end() && $children_it->get('ParentPage') == $parent_id  ) {
			$count++;
			$children_it->moveNext();
		}
		$children_it = $parent_it->getChildrenIt();
		?>
			<div class="bgTop">
			</div> <!-- end bgTop -->
			<div class="bgCenter">
				<div class="<?=($count > 1 ? "leftSide" : "")?>">
					<div class="text wiki">
					<?
			 		$parser = new DEVPROMWikiParser( $page_it, $project_it );
					echo $parser->parse();
					?>
					</div> <!-- end text -->
				</div> <!-- end leftSide -->
				<? if ($count > 1) { ?>
				<div class="rightSideBar">
					<ul class="rightMenu">
					<?
					while ( !$children_it->end() && $children_it->get('ParentPage') == $parent_id  )
					{
	 					$class = $children_it->getId() == $page_it->getId() ? 'current' : '';
						$tag_it = $children_it->getTagsIt();
						?>
						<li class="<?php echo $class; ?>">
							<a class="<? echo $tag_it->getDisplayName() ?>" href="/trainings/<?php echo $children_it->getSearchName(); ?>" seq="<? echo $index ?>">
								<? echo $children_it->getDisplayName() ?>
							</a>
						</li>
						<?
						$children_it->moveNext();
						$index++;
					}
					?>
					</ul>
				</div> <!-- end rightSideBar -->
				<? } ?>
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
 	
 	function drawInstruction( $root_it, $instruction_it, $page_it )
 	{
 		global $project_it;

		?>
		<div class="wrapper1000">
			<ul class="menu2">
			<? 		
			$parent_id = $root_it->getId();
	 		$children_it = $root_it->getChildrenIt();
	 		
	 		$parents = $page_it->getParentsArray();
	 		
	 		while ( !$children_it->end() && $children_it->get('ParentPage') == $parent_id )
	 		{
	 			$class = in_array($children_it->getId(), $parents) ? 'current' : '';
	 			
	 			$section_id = $children_it->getId();
	 			$items_it = $children_it->getChildrenIt();
	 			$url = $items_it->getSearchName();
	 			$children_it->moveToId($section_id);
	 			 
		 		?>
				<li class="<? echo $class ?>">
					<a href="/trainings/<? echo $url ?>">
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

			<div class="leftRound">
			<div class="bgTop">
			</div> <!-- end bgTop -->
			<div class="bgCenter">
				<?
				$this->drawSection( $instruction_it, $page_it );
				?>				
			</div> <!-- end bgCenter -->
			<div class="bgBottom">
			</div> <!-- end bgBottom -->
		</div>
		<div class="maintenance">
			<h3><b>Содержание</b></h3>
			<?
			$this->drawIndex( $instruction_it );
			?>
		</div>
		<div class="clearFix">
		</div>
				<div class="clearFix">
				</div>
		</div> <!-- end menu2 -->
		<?
 	}

 	function drawSection( $instruction_it, $wiki_it )
 	{
 		global $project_it, $model_factory;
 		
 		if ( $wiki_it->get('ParentPage') != '' ) {
		?>
		<h2><? echo $wiki_it->getDisplayName() ?></h2>
		<?php } ?>
		<p>
		<div class="wiki">
		<?
		$parser = new DEVPROMWikiParser($wiki_it, $project_it);
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
				echo '<p><a href="/trainings/'.$children_it->getSearchName().'">'.$children_it->getDisplayName().'</a></p>';
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
						echo '<p><a href="/trainings/'.$children_it->getSearchName().'">'.$children_it->getDisplayName().'</a></p>';
						
						$children_it->moveNext();
					}
					
					return;
				}
				
				if ( $parent_id == $instruction_it->getId() ) break;
				
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
			<li><span class="topPl"></span><span class="bottomPl"><a href="/trainings/<?=$children_it->getSearchName()?>"><? echo $title ?></a></span></li>
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
		global $model_factory;
		
		if ( !is_object($this->page_it) ) return parent::getKeywords();
		
		$tag = $model_factory->getObject('WikiTag');
		
		$tag_it = $tag->getByRef('Wiki', $this->page_it->getId());
		
		return $tag_it->fieldToArray('Caption');
	}
 	
}
