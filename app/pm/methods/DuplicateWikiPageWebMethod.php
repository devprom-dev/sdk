<?php

include_once "DuplicateWebMethod.php";

class DuplicateWikiPageWebMethod extends DuplicateWebMethod
{
	function getMethodName() {
		return 'Method:'.get_class($this).':Version:CopyOption:Description:Snapshot:Project';
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
 	    	 	    	
    		$registry = new WikiPageRegistryVersion();
    		$registry->setDocumentIt($this->getObjectIt());
    		$registry->setSnapshotIt($version_it);
	    	$object->setRegistry($registry);
		}
 	    
 	    $object->addFilter( new ParentTransitiveFilter($this->getObjectIt()->idsToArray()) );
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

 	function duplicate( $project_it, $parms )
 	{
 		if ( strtolower($parms['CopyOption']) == "" )
 		{
	 		$map = array();
			$versionMap = array();
	 		
 			$object_it = $this->getObjectIt();
 			$branch = getFactory()->getObject('Snapshot');
 			$baselineIt = getFactory()->getObject('Baseline')->getExact($parms['Version']);
 			
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
                            'Caption' => $baselineIt->getId() != '' ? $baselineIt->getDisplayName() : $parms['Version'],
                            'Description' => $parms['Description'],
                            'Stage' => $baselineIt->getId()
                        )
	 				);
	 			}
	 			else
	 			{
	 				$map[$this->getObject()->getEntityRefName()][$object_it->getId()] = $object_it->getId();
	 			}
				$versionMap[$this->getObject()->getEntityRefName()][$object_it->getId()] = $object_it->getId();

	 			$object_it->moveNext();
 			}

			if ( count($map) > 0 ) {
				$this->storeBranch( $map, $this->getObject(), $parms );
			}

			$this->storeVersion( $versionMap, $this->getObject(), $parms, true );

 			return new CloneContext();
 		}
 		else
 		{
	 		// make the copy
			$context = parent::duplicate( $project_it, $parms );
	
			$map = $context->getIdsMap();
			
			// for documents only
			if ( $this->getObjectIt()->get('ParentPage') == '' ) {
				// make the snapshot means it is a branch
				$this->storeBranch( $map, $this->getObject(), $parms );
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
					new ParentTransitiveFilter($this->getObjectIt()->idsToArray())
				)
			);
		
 	    foreach( $object_it->idsToArray() as $source_id )
 	    {
    		$link->add_parms( array( 
    		    'SourcePage' => $source_id,
    			'TargetPage' => $map[$object->getEntityRefName()][$source_id],
    			'IsActual' => 'Y',
    			'SourceBaseline' => $_REQUEST['Snapshot'],
    			'Type' => 'branch'
    		));
 	    }
 	}
 	
 	function storeBranch( & $map, & $object, $parms )
 	{
 	 	$snapshot = getFactory()->getObject('Snapshot');
 	 	$baselineIt = getFactory()->getObject('Baseline')->getExact($parms['Version']);

		foreach( $this->getObjectIt()->idsToArray() as $object_id )
		{
            $documentId = $map[$object->getEntityRefName()][$object_id];
			if ( $documentId == '' ) continue;

			$snapshot->add_parms( array (
                'Caption' => $baselineIt->getId() != '' ? $baselineIt->getDisplayName() : $parms['Version'],
                'Description' => $parms['Description'],
                'ListName' => 'branch',
                'Stage' => $baselineIt->getId(),
                'ObjectId' => $documentId,
                'ObjectClass' => get_class($object),
                'SystemUser' => getSession()->getUserIt()->getId(),
                'Type' => 'branch'
			));
		}
 	}

	function storeVersion( & $map, & $object, $parms, $previousName = false )
	{
		$snapshot = getFactory()->getObject('Snapshot');
		$versioned = new VersionedObject();
		$versioned_it = $versioned->getExact(get_class($object));

        $object_it = $this->getObjectIt();
        $object_it->moveFirst();

        while( !$object_it->end() )
        {
            $object_id = $object_it->getId();

			if ( $map[$object->getEntityRefName()][$object_id] == '' ) {
                $object_it->moveNext();
                continue;
            }
			$object_id = $map[$object->getEntityRefName()][$object_id];

            $baselineIt = getFactory()->getObject('Baseline')->getExact(
                $previousName ? $object_it->get('DocumentVersion') : $parms['Version']
            );

            $title = $previousName ? $object_it->get('DocumentVersion') : $parms['Version'];
            if ( $title == '' ) $title = text(2306);

			$branch_it = $snapshot->getRegistry()->Query(
				array (
					new FilterAttributePredicate('ObjectId', $object_id),
					new FilterAttributePredicate('ObjectClass', get_class($object)),
					new FilterAttributePredicate('Caption', $baselineIt->getId() != '' ? $baselineIt->getDisplayName() : $title),
					new FilterAttributePredicate('ListName', get_class($object).':'.$object_id)
				)
			);
			if ( $branch_it->getId() != '' ) {
                $object_it->moveNext();
                continue;
            }

			$snapshot->freeze(
				$snapshot->add_parms( array (
					'Caption' => $title,
					'Description' => $parms['Description'],
					'ListName' => get_class($object).':'.$object_id,
                    'Stage' => $baselineIt->getId(),
					'ObjectId' => $object_id,
					'ObjectClass' => get_class($object),
					'SystemUser' => getSession()->getUserIt()->getId()
				)),
				$versioned_it->getId(),
				array($object_id),
				$versioned_it->get('Attributes')
			);

            $object_it->moveNext();
        }
    }

	function hasAccess() {
		return getFactory()->getAccessPolicy()->can_create($this->getObject());
	}

	private $parent_it;
}
