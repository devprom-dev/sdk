<?php
include "CommentIterator.php";
include "persisters/CommentNewPersister.php";
include "predicates/CommentEntityPredicate.php";
include "predicates/CommentContextPredicate.php";
include "predicates/CommentObjectStatePredicate.php";
include "predicates/CommentStartFilter.php";
include "predicates/CommentFinishFilter.php";
include "predicates/CommentObjectFilter.php";
include "predicates/CommentRootFilter.php";
include "mappers/CommentPrivateMapping.php";

class Comment extends Metaobject
{
 	function __construct( $registry = null ) {
		parent::__construct('Comment', $registry);
        $this->setSortDefault( array(
                new SortAttributeClause('ObjectId'),
                new SortRecentClause()
            ));
	}
	
	function createIterator() {
		return new CommentIterator( $this );
	}
	
	function getCount2( $object_id, $object_class ) 
	{
		return $this->getByRefArrayCount(
			array( 'ObjectId' => $object_id, 
				   'LCASE(ObjectClass)' => strtolower($object_class) )
			);
	}
	
	function getCountForIt( $object_it ) {
		return $this->getCount2( $object_it->getId(), get_class($object_it->object) );
	}
	
	function getAllForObject( $object_it, $queryParms = array() )
	{
        $classes = array(
            strtolower(get_class($object_it->object))
        );
        if ( $object_it->object instanceof Request ) {
            $classes[] = 'issue';
        }
        if ( $object_it->object instanceof Issue || $object_it->object instanceof Increment ) {
            $classes[] = 'request';
        }

        return $this->getRegistry()->Query(
            array_merge(
                array(
                    new FilterAttributePredicate('ObjectId', $object_it->getId()),
                    new FilterAttributePredicate('ObjectClass', $classes),
                ),
                $queryParms
            )
        );
	}
	
	function getAllRootsForObject( $object_it, $query_parms = array() )
	{
	    $classes = array(
	        strtolower(get_class($object_it->object))
        );
        if ( $object_it->object instanceof Request ) {
            $classes[] = 'issue';
        }
        if ( $object_it->object instanceof Issue ) {
            $classes[] = 'request';
        }

		return $this->getRegistry()->Query(
			array_merge(
                $query_parms,
                array(
                    new FilterAttributePredicate('ObjectId', $object_it->getId()),
                    new FilterAttributePredicate('ObjectClass', $classes),
                    new CommentRootFilter(),
                    new SortKeyClause()
                )
            )
		);
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

	function getPage() {
	    return getSession()->getApplicationUrl($this).'project/log/discussions?';
	}
	
	function getPageNameEditMode( $comment_id )
	{
		$comment_it = $this->getExact( $comment_id );
		$class = getFactory()->getObject($comment_it->get('ObjectClass'));
		
		if( is_object($class) ) return $class->getPageNameEditMode($comment_it->get('ObjectId')); 
	}

    function DeletesCascade( $object ) {
        return false;
    }

    function IsDeletedCascade( $object ) {
        return false;
    }

    function getMappers()
    {
        return array_merge( parent::getMappers(),
            array(
                new CommentPrivateMapping()
            )
        );
    }

 	function add_parms( $parms )
 	{
		if ( $parms['ObjectId'] < 1 ) throw new Exception('Object identifier is required');

		if ( $parms['ObjectClass'] == '' ) {
			$parms['ObjectClass'] = $this->getDefaultAttributeValue('ObjectClass');
		}

 		$class_name = getFactory()->getClass($parms['ObjectClass']);
 		if ( !class_exists($class_name) ) throw new Exception('Object class is required');

        if ( $parms['EmailMessageId'] == '' ) {
            $parms['EmailMessageId'] = '<'.uniqid(strtolower(get_class($this))) . '@alm>';
        }

 		return parent::add_parms( $parms );
 	}
}
