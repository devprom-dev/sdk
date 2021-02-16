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

 	function getDisplayNameExt( $prefix = '', $baselineId = 0 )
    {
        if ( $baselineId > 0 ) {
            $baselineIt = getFactory()->getObject('cms_Snapshot')->getExact($baselineId);
            if ( $baselineIt->getId() != '' ) {
                $prefix .= '['.$baselineIt->getDisplayName().'] ';
            }
        }
        else {
            if ( $this->get('DocumentVersion') != '' ) {
                $prefix .= '['.$this->get('DocumentVersion').'] ';
            }
        }
        if ( $this->get('DocumentName') != '' && $this->get('ParentPage') != '' ) {
            $prefix .= $this->get('DocumentName') . ' / ' ;
        }
        return parent::getDisplayNameExt($prefix);
    }

    function getDisplayNameSearch( $prefix = '' ) {
        if ( $this->get('DocumentVersion') != '' ) {
            $prefix .= '['.$this->get('DocumentVersion').'] ';
        }
        if ( $this->get('DocumentName') != '' && $this->get('ParentPage') != '' ) {
            $prefix .= $this->get('DocumentName') . ' / ' ;
        }
        return parent::getDisplayNameSearch($prefix);
    }

    function getTreeDisplayName( $options )
    {
        $value = $this->getDisplayName();

        if ( in_array('uid', $options) ) {
            $uid = $this->get('IncludesUID') != '' ? $this->get('IncludesUID') : $this->get('UID');
            $value = $uid.'&nbsp; '.$value;
        }

        if ( in_array('numbers', $options) && $this->get('SectionNumber') != '' ) {
            $value = $this->get('SectionNumber') . '.&nbsp;' . $value;
        }

        if ( in_array('state', $options) ) {
            if ( $this->get('IncludesState') != '' ) {
                $stateIt = WorkflowScheme::Instance()->getStateIt($this->object);
                $stateIt->moveTo('ReferenceName', $this->get('IncludesState'));
                $stateColor = $stateIt->get('RelatedColor');
            } else {
                $stateColor = $this->getStateIt()->get('RelatedColor');
            }
            $value = '<span class="pri-cir" style="color:'.$stateColor.'">&#x25cf;</span>' . $value;
        }

        if ( in_array('comments', $options) ) {
            if ( $this->get('NewComments') > 0 ) {
                $value = '<i class="icon-comment icon-white"></i> ' . $value;
            } elseif ( $this->get('CommentsCount') > 0 ) {
                $value = '<i class="icon-comment"></i> ' . $value;
            }
        }

        return $value;
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
	
	function Revert($revertIt)
	{
        $this->object->setNotificationEnabled(false);
        $this->object->modify_parms($this->getId(), array(
            'Content' => $revertIt->getHtmlDecoded('Content'),
            'Revert' => 'true'
        ));

        $change = getFactory()->getObject('WikiPageChange');
        $changeIt = $change->getRegistry()->Query(
            array(
                new FilterAttributePredicate('WikiPage', $this->getId()),
                new FilterNextKeyPredicate($revertIt)
            )
        );
        while( !$changeIt->end() ) {
            $change->delete($changeIt->getId());
            $changeIt->moveNext();
		}
        $change->delete($revertIt->getId());
	}
	
	function getValues()
	{
		return $this->object->getValues( $this );
	}
 
	function getUidUrl()
    {
        $parents = preg_split('/,/', trim($this->get('ParentPath'), ','));
        if ( $parents[0] < 1 ) $parents[0] = $this->getId();
        $queryString = array(
            'page' => $this->getId(),
            'document' => $parents[0]
        );
        if ( $this->get('ParentPage') != '' ) {
            $queryString['viewpages'] = 1;
        }
        $this->object->setVpdContext($this);
        return $this->object->getPage().http_build_query($queryString);
    }

    function getHash() {
        return md5($this->getHtmlDecoded('Content') . $this->getHtmlDecoded('Caption') );
    }

    function getAnnotationData()
    {
	    if ( $this->get('CommentsCount') < 1 ) return "";
        $commentIt = getFactory()->getObject('Comment')->getRegistryBase()->Query(
            array(
                new FilterAttributePredicate('ObjectId', $this->getId()),
                new FilterAttributePredicate('ObjectClass', get_class($this->object)),
                new FilterAttributePredicate('Closed', 'N')
            )
        );
        $data = array();
        while( !$commentIt->end() ) {
            if ( $commentIt->get('AnnotationText') . $commentIt->getHtmlDecoded('AnnotationPath') != '' ) {
                $data[] = array(
                    'p' => $commentIt->getHtmlDecoded('AnnotationPath'),
                    't' => $commentIt->getHtmlDecoded('AnnotationText'),
                    's' => $commentIt->get('AnnotationStart'),
                    'l' => $commentIt->get('AnnotationLength'),
                    'i' => $commentIt->getId()
                );
            }
            $commentIt->moveNext();
        }
        return \JsonWrapper::encode($data);
    }
}