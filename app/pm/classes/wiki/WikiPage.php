<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/MetaobjectStatable.php";
include_once SERVER_ROOT_PATH.'pm/classes/wiki/WikiType.php';

include "WikiPageIterator.php";
include "WikiPageRegistry.php";
include "WikiPageRegistryContent.php";
include "WikiPageRegistryVersion.php";
include "WikiPageRegistryBaseline.php";
include "WikiPageRegistryComparison.php";
include "predicates/WikiContentFilter.php";
include "predicates/WikiSectionFilter.php";
include "predicates/WikiNonRootFilter.php";
include "predicates/WikiRootFilter.php";
include "predicates/WikiNonRootEmptyFilter.php";
include "predicates/WikiTagFilter.php";
include "predicates/WikiRootTransitiveFilter.php";
include "predicates/WikiAuthorFilter.php";
include "predicates/WikiTypePlusChildren.php";
include "persisters/WikiPageRevisionPersister.php";
include 'persisters/DocumentVersionPersister.php';
include 'persisters/WikiPageTracesRevisionsPersister.php';
include "persisters/WikiPageHistoryPersister.php";
include 'predicates/WikiSameBranchFilter.php';
include 'predicates/WikiDocumentSearchPredicate.php';
include "predicates/WikiDocumentUIDFilter.php";
include "WikiPageDeleteStrategyMove.php";
include_once "sorts/SortParentPathClause.php";
include_once "sorts/SortDocumentClause.php";

class WikiPage extends MetaobjectStatable
{
 	var $show_page_url, $cache;
 	
 	private $delete_strategy = null;
	
 	function __construct( $registry = null )
 	{
		parent::__construct('WikiPage', is_object($registry) ? $registry : new WikiPageRegistry($this));
		
 	 	$this->setSortDefault( array( new SortOrderedClause() ));

		$this->addAttribute('DocumentVersion', 'VARCHAR', translate('Бейзлайн'), false, false, '', 40);

		$this->addPersister( new DocumentVersionPersister() );
		
		foreach ( array('Caption', 'DocumentVersion') as $attribute )
		{
			$this->addAttributeGroup($attribute, 'tooltip');
		}
		
		$system_fields = array (
				'ReferenceName', 
				'IsTemplate', 
				'IsDraft', 
				'ContentEditor', 
				'UserField1', 
				'UserField2',
				'UserField3',
				'ParentPath',
				'SectionNumber'
		);
		
		foreach( $system_fields as $attribute )
		{
			$this->addAttributeGroup($attribute, 'system');
		}
	}
	
	function createIterator() 
	{
		return new WikiPageIterator( $this );
	}
	
	function getReferenceName() 
	{
		return '';
	}
	
	function getDisplayName() 
	{
		return 'Wiki';
	}
	
	function getPageNameViewMode( $objectid ) 
	{
        $url = $this->getPage().'&page='.$objectid;
        if ( $_REQUEST['search'] != '' ) $url .= '&search='.urlencode($_REQUEST['search']);
		return $url;
	}
	
	function getDefaultAttributeValue( $name ) 
	{
		switch( $name )
		{
		    case 'ReferenceName':
		        
		        return $this->getReferenceName();
		        
			case 'Project':

			    return getSession()->getProjectIt()->getId();

			case 'Author':
				
			    if ( !is_a(getSession(), 'PMSession') ) return;
			    
			    return getSession()->getParticipantIt()->getId();
			    
			case 'IsTemplate': return 0;
			    
			default:
			    
			    return parent::getDefaultAttributeValue($name);
	    }
	}
 	
	function IsDeletedCascade( $object )
	{
		return is_a($object, get_class($this));
	}
	
	function add_parms( $parms )
	{
		$parms['ReferenceName'] = $this->getReferenceName();
		
		if ( $parms['ContentEditor'] == '' && $parms['PageType'] != '' )
		{
			$type = getFactory()->getObject('WikiPageType');
			$type_it = $type->getExact( $parms['PageType'] );
			$parms['ContentEditor'] = $type_it->get('WikiEditor');
		} 

		if ( $parms['ContentEditor'] == '')
		{
			$parms['ContentEditor'] = getSession()->getProjectIt()->get('WikiEditorClass'); 
		}

		$id = parent::add_parms( $parms );
		
		if ( $id < 1 ) return $id;
		
		$object_it = $this->getExact($id);
		
		$documentId = $this->updateParentPath($object_it);
		$this->updateSortIndexAndSections($object_it, $documentId);
		$this->updateUID($object_it);

		return $id;
	}
	
	function modify_parms( $id, $parms )
	{
		$registry = new ObjectRegistrySQL($this);
		$registry->setPersisters($this->getPersisters());
		$object_it = $registry->Query(
				array( new FilterInPredicate($id) )
		);

		if ( $parms['ParentPage'] > 0 )
		{
		    $parent_it = $this->getExact($parms['ParentPage']);
		    $roots = $parent_it->getTransitiveRootArray();
		    if ( in_array($id, $roots) ) {
		        throw new Exception('Cyclic reference found in ParentPage attribute of WikiPage entity');
		    }
		}
		
		if ( $object_it->get('PageType') > 0 ) {
			$editor = $object_it->getRef('PageType')->get('WikiEditor');
		}

		if ( $editor == '' && $object_it->get('Project') > 0 ) {
			$editor = $object_it->getRef('Project')->get('WikiEditorClass');
		}
		
		if ( $object_it->get('ContentEditor') == '' ) $parms['ContentEditor'] = $editor; 
		
		$was_content = $object_it->getHtmlDecoded('Content');

		$result = parent::modify_parms( $object_it, $parms );

		$now_it = $this->getExact( $id );
		
		$now_content = $now_it->getHtmlDecoded('Content');

		// make new version of the page
		if ( $was_content != $now_content && $parms['Revert'] == '' ) $now_it->Version( $was_content );
		
		if ( $object_it->get('ParentPage') != $now_it->get('ParentPage') )
		{
			$documentId = $this->updateParentPath($now_it);
		}

		if ( $object_it->get('ParentPage') != $now_it->get('ParentPage') || $object_it->get('OrderNum') != $now_it->get('OrderNum') )
		{
			if ( $documentId == '' ) {
				$documentId = $now_it->get('DocumentId');
			}
			$this->updateSortIndexAndSections($now_it, $documentId);
		}
		
		return $result; 
	}

	function delete( $id, $record_version = ''  )
	{
		$object_it = $this->getExact($id);
		
		$result = parent::delete( $id );
		
		if ( $result < 1 ) return $result;
		
		$this->updateSortIndexAndSections($object_it);
		
		return $result;
	}
	
	function updateParentPath( $object_it )
	{
        $roots = $object_it->getTransitiveRootArray();
        
        $path_value = ','.join(',', array_reverse($roots)).',';
		$document_id = array_pop($roots);

		$sql = "UPDATE WikiPage t SET t.ParentPath = '".$path_value."', DocumentId = ".$document_id." WHERE t.WikiPageId = ".$object_it->getId();

		DAL::Instance()->Query( $sql );
		
		$sql = 
			"UPDATE WikiPage t ".
			"   SET t.ParentPath = REPLACE(t.ParentPath, '".$object_it->get('ParentPath')."', '".$path_value."') ".
			" WHERE t.ParentPath LIKE '%,".$object_it->getId().",%' AND t.WikiPageId <> ".$object_it->getId();

		DAL::Instance()->Query( $sql );

		$sql = 
			"UPDATE WikiPage t SET t.DocumentId = REPLACE(SUBSTRING_INDEX(t.ParentPath, ',', 2),',','') ".
			" WHERE t.ParentPath LIKE '%,".$object_it->getId().",%' ";

		DAL::Instance()->Query( $sql );

		return $document_id;
	}
	
	function updateSortIndexAndSections( $object_it, $documentId )
	{
        $this->updateSiblingsOrderNum( $object_it );
		$this->updateSortIndex( $object_it, $documentId );
		$this->updateSectionNumber( $object_it );
	}
	
	function updateSortIndex( $object_it, $documentId )
	{
		if ( $object_it->getId() == '' ) return;
		if ( $documentId == '' ) return;

		$parent_id = $object_it->get('ParentPage') != '' ? $object_it->get('ParentPage') : $object_it->getId();
		
		$sql = " CREATE TEMPORARY TABLE tmp_WikiPageSort (WikiPageId INTEGER, SortIndex VARCHAR(128) ) ENGINE=MEMORY AS ".
			   " SELECT t.WikiPageId, ".
			   "        (SELECT GROUP_CONCAT(LPAD(u.OrderNum, 10, '0') ORDER BY LENGTH(u.ParentPath)) ".
 		       "    	   FROM WikiPage u WHERE u.DocumentId = t.DocumentId AND t.ParentPath LIKE CONCAT('%,',u.WikiPageId,',%')) SortIndex ".
			   "   FROM WikiPage t ".
			   "  WHERE t.DocumentId = ".$documentId." AND t.ParentPath LIKE '%,".$parent_id.",%' ";
		DAL::Instance()->Query( $sql );
		
		DAL::Instance()->Query( "
			UPDATE WikiPage t SET t.SortIndex = IFNULL((SELECT u.SortIndex FROM tmp_WikiPageSort u WHERE u.WikiPageId = t.WikiPageId),t.SortIndex)
		" );
		DAL::Instance()->Query( "DROP TABLE tmp_WikiPageSort" );

        $className = get_class($object_it->object);

        $sql = "INSERT INTO co_AffectedObjects (RecordCreated, RecordModified, VPD, ObjectId, ObjectClass) ".
            " SELECT NOW(), NOW(), w.VPD, w.WikiPageId, '" . $className . "' ".
            "     FROM WikiPage w WHERE w.DocumentId = ".$documentId." AND w.ParentPath LIKE '%,".$parent_id.",%' AND ParentPage <> ".$parent_id;
        DAL::Instance()->Query( $sql );
	}
	
	function updateSectionNumber( $object_it )
	{
		if ( $object_it->getId() == '' ) return;
		getFactory()->resetCachedIterator($object_it->object);

		if ( $object_it->get('ParentPage') < 1 ) {
			$sql = "UPDATE WikiPage t SET t.SectionNumber = NULL WHERE t.WikiPageId = ".$object_it->getId();
			DAL::Instance()->Query( $sql );
		}

		$registry = new ObjectRegistrySQL($this);
		$registry->setPersisters(array(
			new WikiPageDetailsPersister()
		));

		$object_it = $registry->Query(
			array (
				new WikiRootTransitiveFilter($object_it->get('ParentPage') ? $object_it->get('ParentPage') : $object_it->getId()),
				new SortDocumentClause()
			)
		);
		foreach( $object_it->fieldToArray('ParentPage') as $parentId )
		{
			if ( $parentId < 1 ) continue;
			DAL::Instance()->Query( "SET @r=0 " );
			$sql = "
				UPDATE WikiPage t 
					   INNER JOIN (
					   		SELECT t1.WikiPageId, 
					   			   CONCAT(
					   			   	  (SELECT IF(t2.SectionNumber IS NULL, '', CONCAT(t2.SectionNumber,'.')) FROM WikiPage t2 WHERE t2.WikiPageId = t1.ParentPage),
					   			   	  (@r:= (@r+1))
					   			    ) SectionNumber
					   		  FROM WikiPage t1 WHERE t1.ParentPage = ".$parentId." ORDER BY t1.OrderNum
					   		) t2 ON t2.WikiPageId = t.WikiPageId
				   SET t.SectionNumber = t2.SectionNumber
				 WHERE t.ParentPage = ".$parentId." 
			";
			DAL::Instance()->Query( $sql );
		}
	}

    function updateSiblingsOrderNum($object_it)
    {
		if ( $object_it->get('ParentPage') == '' ) return;

		$registry = new ObjectRegistrySQL($this);
		$registry->setPersisters(array());
        $seq_it = $registry->Query(
			array (
				new WikiSameBranchFilter($object_it),
				new FilterNextSiblingsPredicate($object_it),
				new SortOrderedClause()
			)
		);
        if ( $seq_it->count() < 1 ) return;
		$ids = $seq_it->idsToArray();

        $sql = "SET @r=".$object_it->get('OrderNum');
        DAL::Instance()->Query( $sql );

        $sql = "UPDATE WikiPage w SET w.OrderNum = @r:= (@r+10), w.RecordModified = NOW() WHERE w.WikiPageId IN (".join(",", $ids).") ORDER BY w.OrderNum ASC";
        DAL::Instance()->Query( $sql );

        $sql = "INSERT INTO co_AffectedObjects (RecordCreated, RecordModified, VPD, ObjectId, ObjectClass) ".
            " SELECT NOW(), NOW(), w.VPD, w.WikiPageId, '" . get_class($this) . "' ".
            "     FROM WikiPage w WHERE w.WikiPageId IN (".join(",", $ids).") ";
        DAL::Instance()->Query( $sql );
	}

	function updateUID( $object_it )
	{
		$uid = new ObjectUID();
		$sql = "UPDATE WikiPage w SET w.UID = '".$uid->getObjectUid($object_it)."' WHERE w.UID IS NULL AND w.WikiPageId IN (".join(",", array($object_it->getId())).")";
		DAL::Instance()->Query( $sql );
	}

	function getTagged( $tag_id )
	{
		$wikitag_it = getFactory()->getObject2('WikiTag', $this)->getPages( $tag_id );

		$wiki_array = array();
		
		for($i = 0; $i < $wikitag_it->count(); $i++) 
		{
			array_push($wiki_array, $wikitag_it->get('WikiPageId'));
			$wikitag_it->moveNext();
		}

		return $this->getInArray('WikiPageId', $wiki_array);
	}
	
	function getRootIt()
	{
	    return null;
	}
	
 	function _fillValues( $object_it, $values, $level, $actual, &$items_count )
 	{
 		for($i = 0; $i < $object_it->count(); $i++)
 		{
 			$skip_item = !$object_it->IsSelectable();
			
 			$values = array_merge( $values, 
 				array( ' '.$object_it->getId() => 
 					   array('label' => $object_it->getDisplayName(), 
						     'level' => $level, 
							 'disabled' => $object_it->IsSelectable() ? 'false' : 'true' ) ) );
 			
 			$items_count++;
	 		$int_items_count = 0;
	 		
			$children_it = $object_it->_getChildren();

 			$values = $this->_fillValues( $children_it, $values, 
 				$level + 1, $actual, $int_items_count );
 			
 			if ( $skip_item && $int_items_count == 0 )
 			{
 				unset( $values[' '.$object_it->getId()] );
 				$items_count--;
 			}
 			
 			$object_it->moveNext();
 		}
 		
 		return $values;
 	}
 	
 	function getValues( $root_it = null )
 	{
		// actual items (not in archive)
 		$values = array();
		$items_count = 0;

		if ( !is_object($root_it) )
		{
			$root_it = $this->getRootIt();
		}

 		$values = $this->_fillValues( $root_it, 
 			$values, 0, true, $items_count );
 		
 		return $values;
 	}
 	
 	function setDeleteStrategy( $strategy )
 	{
 		$this->delete_strategy = $strategy;
 	}
 	
 	function deletesCascade( $object )
 	{
 		if ( !is_object($this->delete_strategy) ) return parent::deletesCascade( $object );
 		 
 		$result = $this->delete_strategy->deletesCascade( $object );
 		
 		if ( is_bool($result) ) return $result;
 		
 		return parent::deletesCascade( $object );
 	}
 	
 	function updatesCascade( $attribute, & $self_it, & $reference_it )
 	{
 		if ( !is_object($this->delete_strategy) )
 		{
 			return parent::updatesCascade( $attribute, $self_it, $reference_it );
 		}
 		 
 		$result = $this->delete_strategy->updatesCascade( $attribute, $self_it, $reference_it );
 		
 		if ( is_bool($result) ) return $result;
 		
 		return parent::updatesCascade( $attribute, $self_it, $reference_it );
 	}
}