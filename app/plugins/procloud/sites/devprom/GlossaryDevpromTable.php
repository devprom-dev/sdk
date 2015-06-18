<?php

class GlossaryDEVPROMTable extends BaseDEVPROMTable
{
	public function __construct()
	{
		global $model_factory;
		
		parent::__construct();
		
	 	if ( $_REQUEST['id'] == '' ) return;
 			
	 	$page = $model_factory->getObject('ProjectPage');
	 	
	 	$page->addFilter( new WikiRootTransitiveFilter($this->getObjectIt()->getId()) );
	 	 
		$page_it = $page->getExact($_REQUEST['id']);
 			
		if ( $page_it->getId() < 1 )
		{
			$obsolete = $this->getObsolete();
				
			$redirect = $obsolete[urldecode(IteratorBase::utf8towin($_REQUEST['id']))];
				
			if ( $redirect != '' ) 
			{
				header($_SERVER["SERVER_PROTOCOL"]." 301 Moved Permanently");
					
				exit(header('Location: /glossary/'.IteratorBase::wintoutf8($redirect)));
			}

			throw new Exception('404');
		}

		$this->setObjectIt( $page_it );
	}
	
 	function getObsolete()
 	{
 		return array (
 				'Итерация' => 'Итерация-разработка-ПО',
 				'Backlog' => 'Бэклог'
 		);
 	}
	
	function draw()
 	{
 	 	global $project_it;

 	 	$root_it = $this->getObjectIt();

	 	if ( $root_it->get('TotalCount') < 1 )
	 	{
	 		$page_it = $root_it->copy();
	 	}
	 	else
	 	{
	 		$page_it = $root_it->getChildrenIt()->copy();
	 	}
 	 	
		?>
		<div class="wrapper1000">
			<br/>
			<div class="clearFix">
			</div>
		</div> <!-- end menu2 -->
		<div class="whiteRounded">
			<div class="bgTop">
			</div> <!-- end bgTop -->
			<div class="bgCenter">
				<div class="leftSide">
					<h1><?=$page_it->getDisplayName()?></h1>
					<div class="text wiki">
					<?
			 		$parser = new SiteWikiParser( $page_it, $project_it );
					echo $parser->parse();
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
							<a class="<? echo $tag_it->getDisplayName() ?>" href="/glossary/<?php echo $children_it->getSearchName(); ?>" seq="<? echo $index ?>">
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
}