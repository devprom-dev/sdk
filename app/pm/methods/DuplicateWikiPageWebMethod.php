<?php

include_once "DuplicateWebMethod.php";

class DuplicateWikiPageWebMethod extends DuplicateWebMethod
{
    private $baselineIt = null;

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

	function getTargetIt($parms)
    {
        $it = getFactory()->getObject('Baseline')->getAll();
        $this->baselineIt = $it->object->getEmptyIterator();
        while( !$it->end() ) {
            if ( $it->getId() == $parms['Version'] ) {
                $this->baselineIt = $it->copy();
                break;
            }
            $it->moveNext();
        }

        if ( $this->baselineIt->getId() == '' ) return parent::getTargetIt($parms);

        return getFactory()->getObject('Project')->getByRef('VPD', $this->baselineIt->get('VPD'));
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
                            'Caption' => $this->baselineIt->getId() != '' ? $this->baselineIt->getDisplayName() : $parms['Version'],
                            'Description' => $parms['Description'],
                            'Stage' => $this->baselineIt->getId()
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

        $entityRefName = $object->getEntityRefName();
		$object_it = $object->getRegistry()->Query(
				array (
					new ParentTransitiveFilter($this->getObjectIt()->idsToArray())
				)
			);
		while( !$object_it->end() )
        {
            $link->add_parms( array(
                'SourcePage' => $object_it->getId(),
                'TargetPage' => $map[$entityRefName][$object_it->getId()],
                'IsActual' => 'Y',
                'SourceBaseline' => $_REQUEST['Snapshot'],
                'Type' => 'branch'
            ));

            // remap upstream traces
            $upstreamTraceIt = $link->getRegistry()->Query(
                array(
                    new FilterAttributePredicate('TargetPage', $map[$entityRefName][$object_it->getId()]),
                    new FilterHasNoAttributePredicate('Type', 'branch')
                )
            );
            if ( $upstreamTraceIt->get('SourcePage') != '' ) {
                $downstreamTraceIt = $link->getRegistry()->Query(
                    array(
                        new FilterAttributePredicate('SourcePage', $upstreamTraceIt->get('SourcePage')),
                        new FilterAttributePredicate('Type', 'branch'),
                        new WikiTraceTargetBaselinePredicate(html_entity_decode($this->baselineIt->getDisplayName()))
                    )
                );
                if ( $downstreamTraceIt->get('TargetPage') != '' ) {
                    $link->getRegistry()->Store(
                        $upstreamTraceIt,
                        array (
                            'SourcePage' => $downstreamTraceIt->get('TargetPage')
                        )
                    );
                }
            }

            $parms = array();

            // remap dependencies
            $dependencies =
                array_map(
                    function( $item ) use ( $map, $entityRefName ) {
                        list($className, $id) = preg_split('/:/', $item);
                        if ( $id == '' ) return $item;
                        if ( $map[$entityRefName][$id] > 0 ) {
                            $id = $map[$entityRefName][$id];
                        }
                        return $className . ':' . $id;
                    },
                    preg_split('/,/', $object_it->get('Dependency'))
                );
            $dependencyNew = join(',', $dependencies);
            if ( $dependencyNew != $object_it->get('Dependency') ) {
                $parms['Dependency'] = $dependencyNew;
            }

            // remap includes
            if ( array_key_exists($object_it->get('Includes'), $map[$entityRefName]) ) {
                $parms['Includes'] = $map[$entityRefName][$object_it->get('Includes')];
            }

            if ( count($parms) > 0 ) {
                $object->getRegistry()->Store($object_it, $parms);
            }

            $object_it->moveNext();
        }

 	    if ( is_object($this->baselineIt) && $this->baselineIt->getId() != '' )
        {
            $trace = getFactory()->getObject('RequestTraceRequirement');
            $trace->setNotificationEnabled(false);

            foreach( $this->getObjectIt()->idsToArray() as $sourceId )
            {
                $documentId = $map[$object->getEntityRefName()][$sourceId];
                $matches = array();

                if ( preg_match('/I-(\d+)/i', $this->baselineIt->getId(), $matches) )
                {
                    $trace->getRegistry()->Merge(
                        array(
                            'ObjectId' => $documentId,
                            'ObjectClass' => $trace->getObjectClass(),
                            'ChangeRequest' => $matches[1],
                            'Type' => REQUEST_TRACE_REQUEST,
                        )
                    );
                }
            }
        }
 	}
 	
 	function storeBranch( & $map, & $object, $parms )
 	{
 	 	$snapshot = getFactory()->getObject('Snapshot');

		foreach( $this->getObjectIt()->idsToArray() as $object_id )
		{
            $documentId = $map[$object->getEntityRefName()][$object_id];
			if ( $documentId == '' ) continue;

			$snapshot->add_parms( array (
                'Caption' => $this->baselineIt->getId() != '' ? html_entity_decode($this->baselineIt->getDisplayName()) : $parms['Version'],
                'Description' => $parms['Description'],
                'ListName' => 'branch',
                'Stage' => $this->baselineIt->getId(),
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
		$trace = getFactory()->getObject('WikiPageTrace');
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

			$baselineIt = $previousName
                    ? getFactory()->getObject('Baseline')->getExact($object_it->get('DocumentVersion'))
                    : $this->baselineIt;

            $title = $previousName ? $object_it->getHtmlDecoded('DocumentVersion') : $parms['Version'];
            if ( $title == '' ) $title = text(2306);
            $caption = $baselineIt->getId() != '' ? $baselineIt->getDisplayName() : $title;

			$branch_it = $snapshot->getRegistry()->Query(
				array (
					new FilterAttributePredicate('ObjectId', $object_id),
					new FilterAttributePredicate('ObjectClass', get_class($object)),
					new FilterTextExactPredicate('Caption', $caption),
					new FilterTextExactPredicate('ListName', get_class($object).':'.$object_id)
				)
			);
			if ( $branch_it->getId() != '' ) {
                $object_it->moveNext();
                continue;
            }

			$versionId = $snapshot->add_parms( array (
                'Caption' => $caption,
                'Description' => $parms['Description'],
                'ListName' => get_class($object).':'.$object_id,
                'Stage' => $baselineIt->getId(),
                'ObjectId' => $object_id,
                'ObjectClass' => get_class($object),
                'SystemUser' => getSession()->getUserIt()->getId()
            ));
			$snapshot->freeze(
                $versionId,
				$versioned_it->getId(),
				array($object_id),
				$versioned_it->get('Attributes')
			);

			// fix dependency for downstream traces
			$ids = array_filter(
                $trace->getRegistry()->Query(
                        array(
                            new WikiTraceSourceDocumentPredicate($object_id)
                        )
                    )->idsToArray(),
			    function($id) {
			        return $id > 0;
                }
            );
			if ( count($ids) > 0 && $versionId > 0 ) {
                DAL::Instance()->Query(" UPDATE WikiPageTrace SET SourceBaseline = ".$versionId." WHERE WikiPageTraceId IN (".join(',',$ids).") ");
            }

            // fix dependency for upstream traces
			if ( $versionId > 0 ) {
                $traceIt = $trace->getRegistry()->Query(
                    array(
                        new FilterAttributePredicate('TargetPage', $object_id)
                    )
                );
                $versionIt = $snapshot->getRegistry()->Query(
                    array(
                        new FilterAttributePredicate('Stage', $baselineIt->getId()),
                        new FilterAttributePredicate('ObjectId', $traceIt->get('SourcePage')),
                        new FilterHasNoAttributePredicate('Type', 'branch')
                    )
                );

                $ids = array_filter(
                    $trace->getRegistry()->Query(
                        array(
                            new WikiTraceTargetDocumentPredicate($object_id)
                        )
                    )->idsToArray(),
                    function($id) {
                        return $id > 0;
                    }
                );
                if ( count($ids) > 0 ) {
                    $sourceBaseline = $versionIt->getId() > 0 ? $versionIt->getId() : 'NULL';
                    DAL::Instance()->Query(" UPDATE WikiPageTrace SET SourceBaseline = ".$sourceBaseline." WHERE WikiPageTraceId IN (".join(',',$ids).") ");
                }
            }

            $object_it->moveNext();
        }
    }

	function hasAccess() {
		return getFactory()->getAccessPolicy()->can_create($this->getObject());
	}

	private $parent_it;
}
