<?php

include_once SERVER_ROOT_PATH."pm/views/communications/ProjectLogTable.php";

include "WikiHistorySettingBuilder.php";
include "WikiHistoryList.php";

class WikiHistoryTable extends ProjectLogTable
{
	private $page_it;
	
	public function __construct()
	{
		parent::__construct(getFactory()->getObject('ChangeLog'));

 		getSession()->addBuilder( new WikiHistorySettingBuilder($this->getWikiPageIt()) );
	}
	
	function getWikiPageIt()
	{
		if ( !is_object($this->page_it) )
		{
			$this->page_it = parent::buildObjectIt();
		}
		
		return $this->page_it;
	}
	
	function buildObjectIt()
	{
		global $model_factory;
		
		$object_it = parent::buildObjectIt();
		
		$this->page_it = $object_it->copy(); 
		
		if ( $object_it->get('ParentPage') == '' )
		{
			$object = $model_factory->getObject(get_class($object_it->object));
			
			$object->addFilter( new WikiRootTransitiveFilter($object_it->getId()) );
			
			$object_it = $object->getAll();
		}
		
		return $object_it;
	}
	
	function getList()
	{
		return new WikiHistoryList( $this->getObject() );
	}
	
	function getFilters()
	{
		$filters = parent::getFilters();
		
		foreach( $filters as $key => $filter )
		{
			if ( in_array($filter->getValueParm(), array('requirement','object')) )
			{
				unset($filters[$key]);
			}
		}
		
		return array_merge( 
				array (
						new WikiFilterHistoryFormattingWebMethod()
				),
				$filters
		);
	}
	
	function getFilterPredicates()
	{
		return array_filter( parent::getFilterPredicates(), function($predicate) {
				return !$predicate instanceof ChangeLogVisibilityFilter; 
		});
	}
	
	function getActions()
	{
		return array();
	}
	
	function drawScripts()
	{
		parent::drawScripts();
		
		?>
 		<script type="text/javascript">
			$(document).ready(function() 
			{ 
				$('.table td#content').each(function() {
					markupDiff($(this));
				});
			});
		</script>
		<?php
	}
	
	function getRenderParms( $parms )
	{
		$page_it = $this->getWikiPageIt();

		return array_merge(
				parent::getRenderParms( $parms ),
				array (
						'navigation_title' => $page_it->getDisplayName(),
						'navigation_url' => $page_it->getViewUrl(),
						'title' => translate('История изменений')
				)
		);
	}
} 