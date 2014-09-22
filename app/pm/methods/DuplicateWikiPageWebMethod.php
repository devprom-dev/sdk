<?php

include_once "DuplicateWebMethod.php";

class DuplicateWikiPageWebMethod extends DuplicateWebMethod
{
	protected function buildContext()
	{
		$context = parent::buildContext();

		$context->setResetState( false );
		
		return $context;
	}
	
	public function getParentIt()
	{
		if ( !is_object($this->parent_it) )
		{
			$this->parent_it = $_REQUEST['parent'] != '' 
					? $this->getObject()->getExact($_REQUEST['parent'])
					: $this->getObject()->getEmptyIterator();
		}
		
		return $this->parent_it;
	}
	
	public function setParentIt( $parent_it )
	{
		$this->parent_it = $parent_it;
	}
	
	function getIdsMap( & $object )
	{
		if ( is_a($object, get_class($this->getObject())) )
		{
			$object_it = $this->getObjectIt();
			
			$map[$object_it->object->getEntityRefName()] = array();
			
			while( !$object_it->end() )
			{
				$map[$object_it->object->getEntityRefName()][$object_it->get('ParentPage')] = '';
				
				$object_it->moveNext();
			}

			$parent_it = $this->getParentIt();
		
			if ( $parent_it->getId() > 0 )
			{
				$map[$this->getObject()->getEntityRefName()][$this->getObjectIt()->get('ParentPage')] = $parent_it->getId();
			}
			
			return $map;
		}
		
		return parent::getIdsMap( $object );
	}

 	function duplicate( $project_it )
 	{
 		if ( strtolower($_REQUEST['CopyOption']) == "" )
 		{
	 		$map = array();
	 		
 			$object_it = $this->getObjectIt();
 			
 			while( !$object_it->end() )
 			{
	 			$branch_it = getFactory()->getObject('Snapshot')->getRegistry()->Query(
	 					array (
	 							new FilterAttributePredicate('ObjectId', $object_it->getId()),
	 							new FilterAttributePredicate('ObjectClass', get_class($this->getObject())),
	 							new FilterAttributePredicate('Type', 'branch')
	 					)
	 			);
	 			
	 			if ( $branch_it->getId() > 0 )
	 			{
	 				$branch_it->modify(
	 						array(
								'Caption' => IteratorBase::utf8towin($_REQUEST['Version']),
								'Description' => IteratorBase::utf8towin($_REQUEST['Description'])
	 						)
	 				);
	 			}
	 			else
	 			{
	 				$map[$this->getObject()->getEntityRefName()][$object_it->getId()] = $object_it->getId();
	 			}
	 							
	 			$object_it->moveNext();
 			}

	 		$this->storeBranch( $map, $this->getObject() );
 			
 			return new CloneContext();
 		}
 		else
 		{
	 		// make the copy
			$context = parent::duplicate( $project_it );
	
			$map = $context->getIdsMap();
			
			// for documents only
			if ( $this->getObjectIt()->get('ParentPage') == '' )
			{
				// make the snapshot means it is a branch
				$this->storeBranch( $map, $this->getObject() );
			}
			
			// make traces on source requirements
			$this->storeTraces( $map, $this->getObject() );
			
	 	    return $context;
 		}
 	}
 	
 	function storeTraces( & $map, & $object )
 	{
 	 	$link = getFactory()->getObject('WikiPageTrace');

		$object_it = $object->getRegistry()->Query( 
				array (
					new WikiRootTransitiveFilter($this->getObjectIt()->idsToArray())
				)
			);
		
 	    foreach( $object_it->idsToArray() as $source_id )
 	    {
    		$link->add_parms( array( 
    		    'SourcePage' => $source_id,
    			'TargetPage' => $map[$object->getEntityRefName()][$source_id],
    			'IsActual' => 'Y',
    			'Baseline' => $_REQUEST['Snapshot'],
    			'Type' => 'branch'
    		));
 	    }
 	}
 	
 	function storeBranch( & $map, & $object )
 	{
 	 	$snapshot = getFactory()->getObject('Snapshot');

		foreach( $this->getObjectIt()->idsToArray() as $object_id )
		{
			$snapshot->add_parms( array (
					'Caption' => IteratorBase::utf8towin($_REQUEST['Version']),
					'Description' => IteratorBase::utf8towin($_REQUEST['Description']),
					'ListName' => 'branch',
					'ObjectId' => $map[$object->getEntityRefName()][$object_id],
					'ObjectClass' => get_class($object),
					'SystemUser' => getSession()->getUserIt()->getId(),
					'Type' => 'branch'
			));
		}
 	}
 	
 	private $parent_it;
}
