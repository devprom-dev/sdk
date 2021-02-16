<?php

class CommentBaseIterator extends OrderedIterator
{
    function getDisplayName()
    {
        if ( $this->get('AuthorName') == '' ) return parent::getDisplayName();
        return $this->get('AuthorName') . ', ' . $this->getDateFormattedShort('RecordCreated');
    }

	function getLastCommentIt()
	{
 		$this->object->defaultsort = 'RecordCreated DESC';
 		return $this->object->getByRefArray(
			array( 'ObjectId' => $this->get('ObjectId'), 
				   'LCASE(ObjectClass)' => strtolower($this->get('ObjectClass')) ), 
			1 );
	}
	
	function getThreadIt( $limit =  0 )
	{
	    $registry = $this->object->getRegistry();
	    if ( $limit > 0 ) {
            $registry->setLimit($limit);
        }
		return $registry->Query(
            array (
                new FilterAttributePredicate('PrevComment', $this->getId()),
                new SortAttributeClause('RecordCreated')
            )
		);
	}
	
	function getRollupIt()
	{
		$comment_array = array();
		$comment_it = $this;
		
		while( $comment_it->getId() > 0 )
		{
			$comment_array[] = $comment_it->getData();
			if ( $comment_it->get('PrevComment') < 1 ) break;

			$comment_it = $comment_it->object->getRegistry()->Query(
				array ( new FilterInPredicate($comment_it->get('PrevComment')) )
			);
		}
		
		return $this->object->createCachedIterator(array_reverse($comment_array));
	}
	
	function getAnchorIt()
	{
	    $class_name = getFactory()->getClass($this->get('ObjectClass'));
	    if ( !class_exists($class_name) ) return $this->object->getEmptyIterator();

	    if ( is_subclass_of($class_name, 'WikiPage') ) {
	        $registry = new WikiPageRegistryContent(getFactory()->getObject($class_name));
	        return $registry->Query(
	            array(
                    new FilterInPredicate($this->get('ObjectId')),
                    new EntityProjectPersister()
                )
            );
        }
        else {
            return getFactory()->getObject($class_name)->getExact($this->get('ObjectId'));
        }
	}
	
	function getViewUrl()
	{
		if ( getSession()->getProjectIt()->getId() != '' ) {
			return '/pm/'.getSession()->getProjectIt()->get('CodeName').'/O-'.$this->getId();
		}
		else {
			return _getServerUrl().'O-'.$this->getId();		
		}
	}
}