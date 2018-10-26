<?php

include "CommentBaseIterator.php";
include "predicates/CommentEntityPredicate.php";
include "predicates/CommentContextPredicate.php";
include "predicates/CommentObjectStatePredicate.php";
include "predicates/CommentStartFilter.php";
include "predicates/CommentFinishFilter.php";
include "predicates/CommentObjectFilter.php";
include "predicates/CommentRootFilter.php";

class CommentBase extends Metaobject
{
 	function __construct( $registry = null ) {
		parent::__construct('Comment', $registry);
	}
	
	function createIterator() 
	{
		return new CommentBaseIterator( $this );
	}
	
	function getCount2( $object_id, $object_class ) 
	{
		return $this->getByRefArrayCount(
			array( 'ObjectId' => $object_id, 
				   'LCASE(ObjectClass)' => strtolower($object_class) )
			);
	}
	
	function getCountForIt( $object_it )
	{
		return $this->getCount2( $object_it->getId(), get_class($object_it->object) );
	}
	
	function getGroupedCount( $object_it ) 
	{
		$sql = " SELECT c.ObjectId, c.ObjectClass, COUNT(1) CommentsCount " .
			   "   FROM Comment c" .
			   "  WHERE c.ObjectId IN (".join(',', $object_it->idsToArray()).") ".
			   "    AND c.ObjectClass = '".strtolower(get_class($object_it->object))."'" .
			   "  GROUP BY c.ObjectId, c.ObjectClass ";
		
		return $this->createSQLIterator( $sql );
	}

	function getAllForObject( $object_it ) 
	{
		return $this->getByRef2('ObjectId', $object_it->getId(), 
			'LCASE(ObjectClass)', strtolower(get_class($object_it->object)) );
	}
	
	function getAllRootsForObject( $object_it, $query_parms = array() )
	{
		return $this->getRegistry()->Query(
			array_merge(
                $query_parms,
                array(
                    new FilterAttributePredicate('ObjectId', $object_it->getId()),
                    new FilterAttributePredicate('ObjectClass', strtolower(get_class($object_it->object))),
                    new CommentRootFilter(),
                    new SortKeyClause()
                )
            )
		);
	}
	
	function getNew( $days, $limit = 0 ) 
	{
		$sql = 'SELECT t.ObjectId, t.ObjectClass, MAX(t.RecordModified) as RecordModified,' .
			   '       COUNT(1) as CommentsCount, MAX(t.Caption) as Caption, '.
			   '	   TO_DAYS(NOW()) - TO_DAYS(MAX(t.RecordModified)) CommentAge ' .
			   '  FROM Comment t '.
			   " WHERE TO_DAYS(NOW()) - TO_DAYS(t.RecordModified) < '".$days."'".
				$this->getVpdPredicate('t').$this->getFilterPredicate().
			   ' GROUP BY t.ObjectId, t.ObjectClass '.
			   ' ORDER BY t.RecordModified DESC '.($limit > 0 ? 'LIMIT '.$limit : '');

		return $this->createSQLIterator($sql);
	}

	function getLastComment( $object_id, $object_class ) 
	{
 		$this->defaultsort = 'RecordCreated DESC';
 		return $this->getByRef2('ObjectId', $object_id, 
			'LCASE(ObjectClass)', strtolower($object_class));
	}

	function getLastCommentIt( $object_it ) 
	{
		return $this->getLastComment( $object_it->getId(), get_class($object_it->object) );
	}

	function getAllGrouped( $limit = 0 ) 
	{
		$sql = "SELECT t.ObjectId, t.ObjectClass, MAX(t.RecordModified) as RecordModified," .
			   "       (SELECT COUNT(1) FROM Comment c2 ".
			   "	     WHERE c2.ObjectId = t.ObjectId AND c2.ObjectClass = t.ObjectClass ) as CommentsCount, " .
			   "	   DATE(MAX(t.RecordModified)) GroupDate, t.VPD " .
			   "  FROM (" .
			   "		SELECT c.ObjectId, c.ObjectClass, c.RecordModified, c.AuthorId, c.VPD " .
			   "		  FROM Comment c " .
			   "		 UNION " .
			   "		SELECT q.pm_QuestionId, 'pm_Question', q.RecordModified, q.Author, q.VPD " .
			   "  	      FROM pm_Question q ".
			   " 		 WHERE NOT EXISTS (SELECT 1 FROM Comment c WHERE c.ObjectId = q.pm_QuestionId AND c.ObjectClass = 'question') " .
			   " 	   ) t ".
			   " WHERE 1 = 1 ".
			   $this->getVpdPredicate('t').$this->getFilterPredicate().$this->getAccessFilter().
			   " GROUP BY t.ObjectId, t.ObjectClass, TO_DAYS(t.RecordModified) ".
			   " ORDER BY RecordModified DESC ".
			   ($limit > 0 ? " LIMIT ".$limit : "");

		return $this->createSQLIterator($sql);
	}

	function getByEntities( $entity_array, $limit = 0 ) 
	{
		$sql = 'SELECT t.ObjectId, t.ObjectClass, MAX(t.RecordModified) as RecordModified,' .
			   '       (SELECT COUNT(1) FROM Comment c2 ' .
			   '	     WHERE c2.ObjectId = t.ObjectId' .
			   '           AND c2.ObjectClass = t.ObjectClass ) as CommentsCount, t.VPD ' .
			   '  FROM Comment t '.
			   " WHERE t.ObjectClass IN ('".join("','", $entity_array)."') ".
			   $this->getVpdPredicate('t').
			   ' GROUP BY t.ObjectId, t.ObjectClass '.
			   ' ORDER BY RecordModified DESC '.
			   ($limit > 0 ? " LIMIT ".$limit : "");

		return $this->createSQLIterator($sql);
	}

	function getUserComments( $user_id, $limit = 5 ) 
	{
		$sql = " SELECT t.* " .
			   "   FROM Comment t " .
			   "  WHERE t.AuthorId = ".$user_id.
			   "    AND t.VPD = '' " .
			   "  ORDER BY t.RecordCreated DESC " .
			   "  LIMIT ".$limit;

 		return $this->createSQLIterator($sql);
	}

	function getPage()
	{
	    return getSession()->getApplicationUrl($this).'project/log/discussions?';
	}
	
	function getPageNameEditMode( $comment_id )
	{
		$comment_it = $this->getExact( $comment_id );
		$class = getFactory()->getObject($comment_it->get('ObjectClass'));
		
		if( is_object($class) ) return $class->getPageNameEditMode($comment_it->get('ObjectId')); 
	}
	
 	function getAccessFilter()
 	{
 		global $model_factory;
 		
		$rights = $model_factory->getObject('pm_AccessRight');
		
		$names_it = $rights->getEntitiesForParticipant(getSession()->getParticipantIt()->getId());
			
		$noaccess = array();
		
		while ( !$names_it->end() )
		{
			$object = $model_factory->getObject($names_it->get('ReferenceName'));
			
			if ( !getFactory()->getAccessPolicy()->can_read($object) )
			{
				array_push( $noaccess, strtolower(get_class($object)) );
			}
			
			$names_it->moveNext();
		}
		
		if ( count($noaccess) > 0 )
		{
			return " AND ObjectClass NOT IN ('".join($noaccess, "','")."')";
		}
		
		return "";
 	}
 	
 	function add_parms( $parms )
 	{
		if ( $parms['ObjectId'] < 1 ) throw new Exception('Object identifier is required');

		if ( $parms['ObjectClass'] == '' ) {
			$parms['ObjectClass'] = $this->getDefaultAttributeValue('ObjectClass');
		}

 		$class_name = getFactory()->getClass($parms['ObjectClass']);
 		if ( !class_exists($class_name) ) throw new Exception('Object class is required');

 		return parent::add_parms( $parms );
 	}
}
