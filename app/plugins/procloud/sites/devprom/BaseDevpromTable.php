<?php

class BaseDEVPROMTable extends SiteTableBase
{
 	var $object_it;
 	
 	function __construct()
 	{
 		global $project_it, $_REQUEST, $model_factory;
 		
 		$page_it = $this->getSitePageWikiIt($_REQUEST['mode']);
 		
 		if ( $page_it->count() < 1 ) throw new Exception('404');

 		$this->setObjectIt($page_it);
 	}
 	
	function setObjectIt( $object_it )
	{
		$this->object_it = $object_it;
	}
	
	function getObjectIt()
	{
		return $this->object_it;
	}
 	
	function getPageWikiIt( $name )
	{
		global $model_factory, $project_it;
		
		$sql = "SELECT p.*, (SELECT COUNT(1) FROM WikiPage t WHERE t.ParentPage = p.WikiPageId) TotalCount " .
				" FROM WikiPage p " .
				"WHERE p.ReferenceName = " .getFactory()->getObject('ProjectPage')->getReferenceName().
				"  AND (SELECT COUNT(1) FROM WikiTag wt, Tag t " .
				"		 WHERE wt.Wiki = p.WikiPageId AND t.TagId = wt.Tag " .
				"		   AND t.Caption IN ('menuitem', '".mysql_real_escape_string($name)."') ) = 2 ".
				"  AND p.Project = ".$project_it->getId().
				" ORDER BY p.ParentPage, p.Caption";

 		$page = $model_factory->getObject('ProjectPage');
 		$page_it = $page->createSQLIterator( $sql );
 		
 		return $page_it;
	}
	
	function getSitePageWikiIt( $name )
	{
		global $model_factory, $project_it;
		
		$sql = "SELECT p.*, (SELECT COUNT(1) FROM WikiPage t WHERE t.ParentPage = p.WikiPageId) TotalCount " .
				" FROM WikiPage p " .
				"WHERE p.ReferenceName = " .getFactory()->getObject('ProjectPage')->getReferenceName().
				"  AND (SELECT COUNT(1) FROM WikiTag wt, Tag t " .
				"		 WHERE wt.Wiki IN (p.WikiPageId, p.ParentPage) AND t.TagId = wt.Tag " .
				"		   AND t.Caption IN ('menuitem', 'devprom.ru') ) > 0 ".
				"  AND (SELECT COUNT(1) FROM WikiTag wt, Tag t " .
				"		 WHERE wt.Wiki = p.WikiPageId AND t.TagId = wt.Tag " .
				"		   AND t.Caption IN ('".mysql_real_escape_string($name)."') ) > 0 ".
				"  AND p.Project = ".$project_it->getId().
				" ORDER BY p.ParentPage, p.Caption";

 		$page = $model_factory->getObject('ProjectPage');
 		$page_it = $page->createSQLIterator( $sql );
 		
 		return $page_it;
	}

	function getTitle()
	{
		$page_it = $this->getObjectIt();
 		return $page_it->getDisplayName();
	}
	
 	function drawScript()
 	{
 		echo $this->script;
 	}
	
 	function draw()
	{
 		global $project_it;
 		
 		$this->drawScript();
 		
 		echo '<div class="wiki">';
			$parser = new DEVPROMWikiParser($this->getObjectIt(), $project_it);
			echo $parser->parse();
		echo '</div>';
	}
	
	function validate()
	{
	}
	
	function getKeywords()
	{
		global $model_factory;
		
		if ( !is_object($this->object_it) ) return;
		
		$tag = $model_factory->getObject('WikiTag');
		
		$tag_it = $tag->getByRef('Wiki', $this->object_it->getId());
		
		return $tag_it->fieldToArray('Caption');
	}
}