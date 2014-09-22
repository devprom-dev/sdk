<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/MetaobjectStatable.php";
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
include "predicates/WikiNotArchivedPredicate.php";
include "predicates/WikiTagFilter.php";
include "predicates/WikiRootTransitiveFilter.php";
include "persisters/WikiPageRevisionPersister.php";
include 'persisters/DocumentVersionPersister.php';
include "WikiPageDeleteStrategyMove.php";
include_once "sorts/SortParentPathClause.php";
include_once "sorts/SortDocumentClause.php";

class WikiPage extends MetaobjectStatable
{
 	var $show_page_url, $cache;
 	
 	private $delete_strategy = null;
	
 	function __construct() 
 	{
		parent::__construct('WikiPage', new WikiPageRegistry($this));
		
 	 	$this->setSortDefault( array( new SortAttributeClause('Caption') ));

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
		return $this->getPage().'&page='.$objectid;
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
 	
	function add_parms( $parms )
	{
		global $_REQUEST, $model_factory;
		
		$parms['ReferenceName'] = $this->getReferenceName();
		
		if ( $parms['ContentEditor'] == '' && $parms['PageType'] != '' )
		{
			$type = $model_factory->getObject('WikiPageType');
			$type_it = $type->getExact( $parms['PageType'] );
			$parms['ContentEditor'] = $type_it->get('WikiEditor');
		} 

		if ( $parms['ContentEditor'] == '')
		{
			$parms['ContentEditor'] = getSession()->getProjectIt()->get('WikiEditorClass'); 
		}

		return parent::add_parms( $parms );
	}
	
	function modify_parms( $id, $parms )
	{
		$object_it = $this->getExact( $id );
		
		if ( $parms['ParentPage'] > 0 )
		{
		    $parent_it = $this->getExact($parms['ParentPage']);
		    
		    $roots = $parent_it->getTransitiveRootArray();
		    
		    if ( in_array($id, $roots) )
		    {
		        throw new Exception('Cyclic reference found in ParentPage attribute of WikiPage entity');
		    }
		}
		
		if ( $object_it->get('PageType') > 0 )
		{
			$editor = $object_it->getRef('PageType')->get('WikiEditor');
		}

		if ( $editor == '' && $object_it->get('Project') > 0 )
		{
			$editor = $object_it->getRef('Project')->get('WikiEditorClass');
		}
		
		if ( $object_it->get('ContentEditor') == '' ) $parms['ContentEditor'] = $editor; 
		
		$was_content = $object_it->getHtmlDecoded('Content');
		
		$result = parent::modify_parms( $id, $parms );

		$object_it = $this->getExact( $id );
		
		$now_content = $object_it->getHtmlDecoded('Content');

		// make new version of the page
		
		if ( $was_content != $now_content && $parms['Revert'] == '' ) $object_it->Version( $was_content );
		
		return $result; 
	}

	function createLike( $object_id, $use_notification = true )
	{
		$new_object_id = parent::createLike( $object_id, $use_notification );
		
		$object_it = $this->getExact( $object_id );
		$children_it = $object_it->_getChildren();
		
		for( $i = 0; $i < $children_it->count(); $i++ )
		{
			$new_children_id = $this->createLike($children_it->getId(), $use_notification);
			
			$this->modify_parms($new_children_id,
				array( 'ParentPage' => $new_object_id ), $use_notification);
				
			$children_it->moveNext();
		}
		
		$file = getFactory()->getObject('WikiPageFile');
		$file_it = $file->getByRef('WikiPage', $object_id);
		
		for( $i = 0; $i < $file_it->count(); $i++ )
		{
			$new_file_id = $file->createLike($file_it->getId(), $use_notification);
			
			$file->modify_parms($new_file_id,
				array( 'WikiPage' => $new_object_id ), $use_notification);

			$file_it->moveNext();
		}
		
		return $new_object_id;
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
	
 	function cacheDeps( $page_it = null )
	{
		global $model_factory;

		if ( !is_object($this->cache) )
		{
			$object = $model_factory->getObject(get_class($this));
			
			$object->setFilters( $this->getFilters() );
			$object->addFilter( new WikiNotArchivedPredicate() );
			
			$object->addSort( new SortAttributeClause('ParentPage') );
			$object->addSort( new SortOrderedClause() );
			
			$this->cache = $object->getAll();

			$this->cache->buildPositionHash( array( 'WikiPageId', 'ParentPage' ) );
		}
	}
		
	function getChildrenIt( $parent_it )
	{
		global $model_factory;
		
		$this->cacheDeps( $parent_it );

		$this->cache->moveTo('ParentPage', $parent_it->getId() );
		
		return $this->cache;
	}

 	function _fillValues( $object_it, $values, $level, $actual, &$items_count )
 	{
 		for($i = 0; $i < $object_it->count(); $i++)
 		{
 			if ( $actual )
 			{
 				$skip_item = $object_it->IsArchived();
 			}
 			else
 			{
 				$skip_item = !$object_it->IsArchived();
 			}
 			
 			$skip_item = $skip_item || !$object_it->IsSelectable();
			
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