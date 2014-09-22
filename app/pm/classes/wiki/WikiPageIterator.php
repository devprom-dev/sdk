<?php

class WikiPageIterator extends StatableIterator
{
	private $content_storage;
	
	private $style_storage;
	
   	function get_native( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'Content':
 				if ( $this->hasAttribute($attr) ) return parent::get_native( $attr );
 				
 				if ( !isset($this->content_storage[$this->getId()]) ) $this->cacheContentAndStyle(); 
 				
		        return $this->content_storage[$this->getId()];
 				
 			case 'UserField3':
 				if ( $this->hasAttribute($attr) ) return parent::get_native( $attr );
 				
 				if ( !isset($this->style_storage[$this->getId()]) ) $this->cacheContentAndStyle(); 
 				
		        return $this->style_storage[$this->getId()];
		        
		    default:
 				return parent::get_native( $attr );
 		}
 	}
     
    function get( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'Content':
 			case 'UserField3':
 				return $this->get_native($attr);
 				
 			default:
 				return parent::get( $attr );
 		}
 	}
 	
 	private function cacheContentAndStyle()
 	{
 		if ( $this->getId() < 1 ) return;
 		
 		$registry = new WikiPageRegistryContent($this->object);
 		
 		$it = $registry->Query(array(
 				new FilterInPredicate($this->getId())
 			));
 		
 		$this->content_storage[$this->getId()] = $it->get_native('PageContent');
 		$this->style_storage[$this->getId()] = $it->get_native('PageStyle');
    }
 	
	function getChildrenIt()
	{
		return $this->object->getChildrenIt( $this );
	}
	
 	function hasChildren() 
 	{
		$parms = array( 'ParentPage' => $this->getId() );
		
		return $this->object->getByRefArrayCount($parms) > 0;	
	}

	function hasChanges()
	{
		global $model_factory;
		
		$change = $model_factory->getObject('WikiPageChange');
		
		return $change->getByRefArrayCount( 
			array('WikiPage' => $this->getId()) );
	}

	/*
	 * returns all children of a current node
	 */
	function getAllChildrenIds()
	{
		return $this->object->getRegistry()->Query(
				array (
						new WikiRootTransitiveFilter($this->getId())
				)
		)->idsToArray();
	}
	
	function getSiblings() 
	{
		$parms = array( 
			'ParentPage' => $this->get('ParentPage'),
			'ReferenceName' => $this->object->getReferenceName() );
		
		return $this->object->getByRefArray($parms);
	}
	
	function hasArchivedChildren()
	{
		$sql = " SELECT p.WikiPageId, p.IsArchived FROM WikiPage p " .
			   "  WHERE p.ParentPage = ".$this->getId();
			   
		$it = $this->object->createSQLIterator( $sql );
		
		while ( !$it->end() )
		{
			if ( $it->IsArchived() )
			{
				return true;
			}

			if ( $it->hasArchivedChildren() )
			{
				return true;
			}
			
			$it->moveNext();
		}
		
		return false;
	}
	
	function getParentsArray()
	{
		return preg_split('/,/', trim($this->get('ParentPath'), ','));
	}
	
	/*
	 * returns root iterator for the given leaf
	 */
	function getRootIt()
	{
	    if ( trim($this->get('ParentPath'), ',') == '' )
	    {
	    	return $this->object->getEmptyIterator();
	    }
	    
	    $parents = $this->getParentsArray();
	    
		$registry = new WikiPageRegistry($this->object);
		
		return $registry->Query( array(
				 new FilterInPredicate($parents[0])
		));
	}
	
 	function getTransitiveRootArray()
	{
	    $roots = array();
	    
		$parent_page = $this->getId();
		
		while( $parent_page != '' ) 
		{
		    $roots[] = $parent_page;
		    
			$parent_page_it = $this->object->getExact($parent_page);
			
			if( $parent_page_it->get('ParentPage') == '' ) break; 
			
			$parent_page = $parent_page_it->get("ParentPage");
		}
		
		return $roots;
	}
	
	function getParentsIt()
	{
	    if ( trim($this->get('ParentPath'), ',') == '' ) return $this->object->getEmptyIterator();
	    
		$registry = new WikiPageRegistry($this->object);
		
		return $registry->Query( array(
				 new FilterInPredicate($this->getParentsArray())
		));
	}
	
	function getDisplayName()
	{
		$title = parent::getDisplayName();

		if ( $this->get('DocumentVersion') != '' )
		{
			$title .= " [".$this->get('DocumentVersion')."]";
		}
		
		return $title; 
	}
	
	function getDisplayNamePath() 
	{
		$uid = new ObjectUID;

		return $uid->getUidIcon( $this ).
			' '.$this->getPath( $this->object->getPageNameObject( $this->getId() ) );
	}	

	function getRevertUrl()
	{
		$url = parent::getEditUrl();
		return $url.'&wiki_action=revert'; 
	}

	function IsPersisted()
	{
		return is_numeric($this->get('RecordVersion'));
	}
	
	function IsArchived() 
	{
		return $this->get('IsArchived') == 'Y';
	}

	function IsDraft() 
	{
		return $this->get('IsDraft') == 'Y';
	}

	/*
	 * defines if a node can be selected in selectors
	 */
	function IsSelectable()
	{
		return true;
	}
	
	/*
	 * defines if a node can be a parent node like $object_it
	 */
	function IsParentable( $object_it )
	{
		return true;
	}

	function onFileChanged( $file_it )
	{
	}

	function Archive()
	{
		$this->_archive( $this );
	}
	
	function Extract()
	{
		$this->_archive( $this, false );
	}

 	function _getChildren() 
 	{
		$parms = array( 'ParentPage' => $this->getId() );
		
		return $this->object->getByRefArray($parms);	
	}
	
	function _archive( $page_it, $put = true )
	{
 		$children_it = $page_it->_getChildren();
 		
 		for( $i = 0; $i < $children_it->count(); $i++ )
 		{
 			$this->_archive( $children_it, $put );
 			
 			$children_it->modify( 
 				array('IsArchived' => ($put ? 'Y' : 'N') ) );
 				 
 			$children_it->moveNext();
 		}

		$page_it->modify( 
			array('IsArchived' => ($put ? 'Y' : 'N') ) ); 
	}
	
	function Version( $was_content )
	{
		global $model_factory;
		
		$change = $model_factory->getObject('WikiPageChange');
		
		$change->setAttributeType('WikiPage', 'REF_'.get_class($this->object).'Id');
		
		$change->add_parms( array(
		    'WikiPage' => $this->getId(),
		    'Content' => $was_content,
		    'Author' => getSession()->getParticipantIt()->getId()
		));
	}
	
	function Revert()
	{
		global $model_factory;
		
		$change = $model_factory->getObject('WikiPageChange');
   		$change->defaultsort = 'RecordCreated DESC';

		$change->setAttributeType('WikiPage', 'REF_'.get_class($this->object).'Id');
   		
		$change_it = $change->getByRefArray( 
			array('WikiPage' => $this->getId()), 1);
		
		if ( $change_it->count() > 0 )
		{
			$this->modify( array(
				'Content' => $change_it->getHtmlDecoded('Content'),
				'Revert' => 'true'
			));
				
			$change->delete($change_it->getId());
		}
	}
	
	function getValues()
	{
		return $this->object->getValues( $this );
	}
 
 	function getTagsIt()
	{
		global $model_factory;
		
		$tag = $model_factory->getObject('WikiTag');
		return $tag->getTagsByWiki( $this->getId() );
	}
	
	function getViewUrl()
	{
	    $parents = preg_split('/,/', trim($this->get('ParentPath'), ','));
	    
	    if ( $parents[0] < 1 ) $parents[0] = $this->getId();
	    
	    return parent::getViewUrl().'&document='.$parents[0];
	}
}