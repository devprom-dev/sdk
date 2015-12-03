<?php

include_once "DuplicateWebMethod.php";

class DuplicateWikiPageWebMethod extends DuplicateWebMethod
{
	function getMethodName()
	{
		return 'Method:'.get_class($this).':Version:CopyOption:Description:Snapshot:Project';
	}
	
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

	public function getSourceIt()
	{
	 	$object = $this->getObject();
 	    
 	    if ( $_REQUEST['Snapshot'] != '' )
 	    {
			$version_it = getFactory()->getObject('Snapshot')->getExact($_REQUEST['Snapshot']);
 	    	 	    	
 	    	$object->addPersister( new SnapshotItemValuePersister($version_it->getId()) );
			
    		$registry = new WikiPageRegistryVersion();
    			
    		$registry->setDocumentIt($this->getObjectIt());
    		$registry->setSnapshotIt($version_it);
	    	$object->setRegistry($registry);
		}
 	    
 	    $object->addFilter( new WikiRootTransitiveFilter($this->getObjectIt()->idsToArray()) );
 	    $object->addSort( new SortDocumentClause() );
 	    
 	    return $object->getAll();
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
 			$branch = getFactory()->getObject('Snapshot');
 			
 			while( !$object_it->end() )
 			{
	 			$branch_it = $branch->getRegistry()->Query(
	 					array (
	 							new FilterAttributePredicate('ObjectId', $object_it->getId()),
	 							new FilterAttributePredicate('ObjectClass', get_class($this->getObject())),
	 							new FilterAttributePredicate('Type', 'branch')
	 					)
	 			);
	 			
	 			if ( $branch_it->getId() > 0 )
	 			{
	 				$branch->modify_parms($branch_it->getId(),
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

			if ( count($map) > 0 ) {
				$this->storeBranch( $map, $this->getObject() );
			}

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
			if ( $map[$object->getEntityRefName()][$object_id] == '' ) continue;
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
