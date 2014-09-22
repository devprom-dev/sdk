<?php

if ( !class_exists('Tag', false) ) include "Tag.php";
include "RequestTagIterator.php";

include "predicates/TagRequestFilter.php";
include "persisters/RequestTagCaptionPersister.php";

class RequestTag extends Tag
{
 	function RequestTag() 
 	{
 		parent::Metaobject('pm_RequestTag');

		$this->addAttribute('Caption', 'TEXT', translate('Название'), false);
		
		$this->addAttribute('ItemCount', 'INTEGER', translate('Количество'), false);

		$this->addPersister( new RequestTagCaptionPersister() );
 	}

 	function getPageNameObject( $tag_id ) 
 	{
 		global $model_factory;
 		
 		$report = $model_factory->getObject('PMReport');
 		$report_it = $report->getExact('allissues');
 		
 		return $report_it->getUrl().'&state=all&tag='.$tag_id;
 	}

	function createIterator() 
	{
		return new RequestTagIterator($this);
	}
	
	function getGroupKey()
	{
		return 'Request';
	}
	
 	function getByRequest( $parm ) 
 	{
 		global $model_factory;
 		
 		if ( is_array($parm) )
 		{
 			$request_id = join(',', $parm);
 		}
 		else
 		{
 			$request_id = $parm;
 		}
 		
		$sql = "SELECT t.TagId, t.Caption, t.Owner, " .
				"	   r.Request ".$this->getGroupKey().", COUNT(1) ItemCount " .
				" FROM pm_RequestTag r, Tag t " .
				"WHERE r.Request IN (".$request_id.") ".
				"  AND t.TagId = r.Tag " .
				"  AND r.vpd IN ('".join("','",$this->getVpds())."') ".
				"GROUP BY r.Request, t.TagId " .
				"ORDER BY r.Request, Caption " ;

		$tag_cls = $model_factory->getObject('Tag');
		return $tag_cls->createSQLIterator($sql);
 	}
 	
 	function getByObject( $object_it )
 	{
 		if ( is_numeric($object_it) )
 		{
 			return $this->getByRequest( $object_it );
 		}
 		else
 		{
 			$ids = $object_it->idsToArray();
 			return $this->getByRequest( $ids );
 		}
 	}
 	
 	function getByAK( $object_id, $tag_id )
 	{
 		return $this->getByRefArray(
			array('Request' => $object_id, 'Tag' => $tag_id) );
 	}
 	
 	function bindToObject( $object_id, $tag_id )
 	{
 		$this->add_parms(array('Request' => $object_id,
				'Tag' => $tag_id) );
 	}
 	
 	function removeTags( $request_id )
 	{	
 		$tag_it = $this->getByRequest( $request_id );
 		
 		while ( !$tag_it->end() )
 		{	
 			$request_tag_it = $this->getByAK( $request_id, $tag_it->getId() );
 			$this->delete( $request_tag_it->getId() );
 			
 			$tag_it->moveNext();
 		}
 	}
}