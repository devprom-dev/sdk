<?php

if ( !class_exists('Tag', false) ) include "Tag.php";
include "WikiTagIterator.php";

include "predicates/WikiTagReferenceFilter.php";
include "persisters/WikiTagCaptionPersister.php";

class WikiTag extends Tag
{
 	function __construct() 
 	{
 		parent::Metaobject('WikiTag');

		$this->addAttribute('Caption', 'TEXT', translate('Название'), false);
		
		$this->addAttribute('ItemCount', 'INTEGER', translate('Количество'), false);

		$this->addPersister( new WikiTagCaptionPersister() );
 	}
 	
	function createIterator() 
	{
		return new WikiTagIterator($this);
	}
 	
	function getGroupKey()
	{
		return 'WikiPageId';
	}

 	function getPageNameObject( $object_id = '' )
 	{
 		return '?state=all&tag='.$object_id;
 	}
 	
 	function getTagsByWiki( $wiki_id )
 	{
 		global $model_factory;
 		
		if ( is_numeric($wiki_id) )
		{
			$wiki_id = array($wiki_id);
		}
		
		$sql = "SELECT t.TagId, t.Caption, p.WikiPageId " .
				" FROM WikiTag wt, Tag t, WikiPage p " .
				"WHERE p.WikiPageId IN (".join(',', $wiki_id).") " .
				"  AND wt.Wiki = p.WikiPageId".
				"  AND t.TagId = wt.Tag" .
				" ORDER BY p.WikiPageId, t.Caption ";

		$tag = $model_factory->getObject('Tag');
		return $tag->createSQLIterator($sql);
 	}
 	
 	function getByObject( $object_it )
 	{
 		if ( is_numeric($object_it) )
 		{
 			$it = $this->getTagsByWiki( $object_it );
 			return $it;
 		}
 		else
 		{
 			$ids = $object_it->idsToArray();
 			$it = $this->getTagsByWiki( $ids );
 			return $it;
 		}
 	}
 	
 	function getByAK( $object_id, $tag_id )
 	{
 		return $this->getByRefArray(
			array('Wiki' => $object_id, 'Tag' => $tag_id) );
 	}
 	
 	function bindToObject( $object_id, $tag_id )
 	{
 		$this->add_parms(array('Wiki' => $object_id,
				'Tag' => $tag_id) );
 	}
 	
 	function removeTags( $request_id )
 	{	
 		$tag_it = $this->getTagsByWiki( $request_id );
 		while ( !$tag_it->end() )
 		{	
 			$tag_it = $this->getByAK( $request_id, $tag_it->getId() );
 			$tag_it->delete();
 			
 			$tag_it->moveNext();
 		}
 	}
}