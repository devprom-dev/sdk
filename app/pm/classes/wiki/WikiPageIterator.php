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
				$native = parent::get_native( $attr );
 				if ( $native != '' || array_key_exists('Content', $this->getData()) ) return $native;

 				if ( !isset($this->content_storage[$this->getId()]) ) $this->cacheContentAndStyle();
		        return $this->content_storage[$this->getId()];

 			case 'UserField3':
				if ( parent::get_native('ContentPresents') != 'Y' ) return parent::get_native( $attr );

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

 	function getDisplayNameExt( $prefix = '' )
    {
        if ( $this->get('DocumentVersion') != '' ) {
            $prefix .= '['.$this->get('DocumentVersion').'] ';
        }
        if ( $this->get('DocumentName') != '' && $this->get('ParentPage') != '' ) {
            $prefix .= $this->get('DocumentName') . ' / ' ;
        }
        return parent::getDisplayNameExt($prefix);
    }

    function getTreeDisplayName() {
        return $this->get('Caption');
    }

    private function cacheContentAndStyle()
 	{
 		if ( $this->getId() < 1 ) return;

 		$registry = new WikiPageRegistryContent($this->object);

 		$it = $registry->Query(array(
 				new FilterInPredicate($this->getId())
 			));

 		$this->content_storage[$this->getId()] = $it->get_native('Content');
 		$this->style_storage[$this->getId()] = $it->get_native('UserField3');
    }

	function getChildrenIt()
	{
		return $this->object->getRegistry()->Query(
				array (
						new ParentTransitiveFilter($this->getId()),
						new FilterNotInPredicate(array($this->getId()))
				)
		);
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
						new ParentTransitiveFilter($this->getId())
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
	
	function getParentsArray()
	{
		return array_filter(preg_split('/,/', trim($this->get('ParentPath'), ',')), function($value) {
				return $value > 0;
		});
	}

	function getLevel() {
        return count(preg_split('/,/', $this->get('ParentPath'))) - 3;
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
		$roots[] = $parent_page;

		$parent_page = $this->get('ParentPage');
		if ( $parent_page != '' ) {
			$parent_it = $this->object->getRegistryBase()->Query(array(new FilterInPredicate($parent_page)));
			$roots = array_merge($roots,
				array_reverse(
					array_filter(preg_split('/,/',$parent_it->get('ParentPath')), function($value) {
						return $value > 0;
					})
				)
			);
		}

		return $roots;
	}
	
	function getParentsIt( $object = null )
	{
	    if ( trim($this->get('ParentPath'), ',') == '' ) return $this->object->getEmptyIterator();
	    if ( !is_object($object) ) $object = $this->object;

		return $object->getRegistryBase()->Query( array(
				 new FilterInPredicate($this->getParentsArray())
		));
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

	function Version( $was_content )
	{
		$change = getFactory()->getObject('WikiPageChange');
		$change->setAttributeType('WikiPage', 'REF_'.get_class($this->object).'Id');

		$change->add_parms( array(
		    'WikiPage' => $this->getId(),
		    'Content' => $was_content,
		    'Author' => getSession()->getUserIt()->getId()
		));
	}
	
	function Revert()
	{
		$change = getFactory()->getObject('WikiPageChange');
   		$change->defaultsort = 'RecordCreated DESC';

		$change->setAttributeType('WikiPage', 'REF_'.get_class($this->object).'Id');
		$change_it = $change->getByRefArray( 
			array('WikiPage' => $this->getId()), 1);
		
		if ( $change_it->count() > 0 )
		{
			$this->object->setNotificationEnabled(false);
			$this->object->modify_parms($this->getId(), array(
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