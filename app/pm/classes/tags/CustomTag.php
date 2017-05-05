<?php

if ( !class_exists('Tag', false) ) include "Tag.php";
include "CustomTagIterator.php";

include "predicates/CustomTagFilter.php";
include "persisters/CustomTagDetailsPersister.php";

class CustomTag extends Tag
{
 	var $object;
 	
 	function __construct() 
 	{
 		$this->object = $this->getObject();
 		parent::Metaobject('pm_CustomTag');
 		$this->addPersister( new CustomTagDetailsPersister() );
        $this->addPersister( new TagParentPersister() );
        $this->setSortDefault(
            array(
                new TagCaptionSortClause()
            )
        );
 	}

	function createIterator() 
	{
		return new CustomTagIterator($this);
	}
	
	function getObject()
	{
		return null;
	}
	
	function getObjectClass()
	{
	    return strtolower(get_class($this->object));
	}
	
	function setObject( $object )
	{
		$this->object = $object;
	}
	
	function getGroupKey()
	{
		return 'ObjectId';
	}
	
 	function getPageNameObject( $object_id = '' )
 	{
 		return $this->object->getPage().'&tag='.$object_id;
 	}

 	function getByObject( $object_it )
 	{
 		global $model_factory;

		$tag = $model_factory->getObject('Tag');
		$items = array();
		
 		if ( is_numeric($object_it) )
 		{
 			array_push($items, $object_it);
 		}
 		else
 		{
 			$items = array_merge($items, $object_it->idsToArray());
 		}

		$sql = "SELECT t.TagId, t.Caption, t.Owner, rt.ObjectId, COUNT(1) ItemCount " .
				" FROM Tag t, pm_CustomTag rt " .
				"WHERE rt.Tag = t.TagId " .
				$this->getVpdPredicate('t').
				"  AND rt.ObjectClass = '".strtolower(get_class($this->object))."' ".
				"  AND rt.ObjectId IN (".join($items, ',').") ".
				"GROUP BY rt.ObjectId, t.TagId " .
				"ORDER BY rt.ObjectId, t.Caption " ;

		return $tag->createSQLIterator($sql);
 	}
 	
 	function getByAK( $object_id, $tag_id )
 	{
 		return $this->getByRefArray(
			array( 'ObjectId' => $object_id, 
				   'Tag' => $tag_id,
				   'ObjectClass' => strtolower(get_class($this->object)) ) 
			);
 	}
 	
 	function bindToObject( $object_id, $tag_id )
 	{
 		$this->add_parms(
 			array( 'ObjectId' => $object_id,
				   'Tag' => $tag_id,
				   'ObjectClass' => strtolower(get_class($this->object)) ) 
			);
 	}
 	
 	function removeTags( $object_id )
 	{	
 		$tag_it = $this->getByObject( $object_id );
 		
 		while ( !$tag_it->end() )
 		{	
 			$custom_tag_it = $this->getByAK( $object_id, $tag_it->getId() );
 			$this->delete( $custom_tag_it->getId() );
 			
 			$tag_it->moveNext();
 		}
 	}
}
 