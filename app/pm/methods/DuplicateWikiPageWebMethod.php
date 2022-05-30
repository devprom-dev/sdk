<?php
use Devprom\ProjectBundle\Service\Wiki\WikiBaselineService;
include_once "DuplicateWebMethod.php";

class DuplicateWikiPageWebMethod extends DuplicateWebMethod
{
    private $baselineIt = null;

	function getMethodName() {
		return 'Method:'.get_class($this).':Version:Description:Project:CopyOption=N';
	}

    public function setObjectIds( $ids )
    {
        $this->setObjectIt(
            $this->getObject()->getRegistry()->Query(
                array(
                    new FilterInPredicate($ids),
                    new SortDocumentSourceClause()
                )
            )
        );
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
        return parent::getTargetIt($parms);
    }

    public function getSourceIt()
	{
	 	$object = $this->getObject();
	 	$parms = array();
 	    
 	    if ( $_REQUEST['Snapshot'] != '' ) {
			$version_it = getFactory()->getObject('Snapshot')->getExact($_REQUEST['Snapshot']);
			if ( $version_it->getId() != '' ) {
                $registry = new WikiPageRegistryVersion();
                $registry->setDocumentIt($this->getObjectIt());
                $registry->setSnapshotIt($version_it);
                $object->setRegistry($registry);
            }
			else {
                $parms[] = new ParentTransitiveFilter($this->getObjectIt()->idsToArray());
            }
		}
 	    else {
            $parms[] = new ParentTransitiveFilter($this->getObjectIt()->idsToArray());
        }

 	    return $object->getRegistry()->Query(
 	        array_merge(
                $parms,
                array(
                    new SortDocumentClause()
                )
            )
        );
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
 	    $baselineService = new WikiBaselineService(getFactory(), getSession());

 		if ( $parms['CopyOption'] == "N" )
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
	 			    $caption = $this->baselineIt->getId() != '' ? $this->baselineIt->getDisplayName() : $parms['Version'];
	 				$branch->modify_parms($branch_it->getId(),
                        array(
                            'Caption' => $caption,
                            'Description' => $parms['Description'],
                            'Stage' => $this->baselineIt->getId()
                        )
	 				);

                    $wasBranchIt = $branch->getRegistry()->Query(
                        array (
                            new FilterAttributePredicate('ObjectId', $object_it->getId()),
                            new FilterAttributePredicate('ObjectClass', get_class($this->getObject())),
                            new FilterTextExactPredicate('Caption', $caption),
                            new FilterTextExactPredicate('ListName', get_class($this->getObject()).':'.$object_it->getId())
                        )
                    );
                    if ( $wasBranchIt->getId() != '' ) {
                        $branch->delete($wasBranchIt->getId());
                    }
	 			}
	 			else
	 			{
	 				$map[$this->getObject()->getEntityRefName()][$object_it->getId()] = $object_it->getId();
	 			}
				$versionMap[$this->getObject()->getEntityRefName()][$object_it->getId()] = $object_it->getId();

	 			$object_it->moveNext();
 			}

            $this->getObject()->setRegistry( new WikiPageRegistry() );

			if ( count($map) > 0 ) {
				$this->storeBranch( $map, $this->getObject(), $parms, $baselineService );
			}

			$this->storeVersion( $versionMap, $this->getObject(), $parms, $baselineService );

 			return new CloneContext();
 		}
 		else
 		{
	 		// make the copy
			$context = parent::duplicate( $project_it, $parms );

			$this->getObject()->setRegistry( new WikiPageRegistry() );
	
			$map = $context->getIdsMap();

			// for documents only
			if ( $this->getObjectIt()->get('ParentPage') == '' )
			{
                foreach( $this->getObjectIt()->idsToArray() as $object_id )
                {
                    $documentId = $map[$this->getObject()->getEntityRefName()][$object_id];
                    if ( $documentId == '' ) continue;

                    $documentIt = $this->getObject()->getExact($documentId);
                    $baselineService->storeInitialBaseline($documentIt);
                }

                // make the snapshot means it is a branch
				$this->storeBranch( $map, $this->getObject(), $parms, $baselineService );
			}

			// make traces on source requirements
			$this->storeTraces( $map, $this->getObject(),
                $this->baselineIt->getId() != '' ? $this->baselineIt->getDisplayName() : $parms['Version'] );
			
	 	    return $context;
 		}
 	}
 	
 	function storeTraces( & $map, & $object, $baselineName )
 	{
 	 	$link = getFactory()->getObject('WikiPageTrace');
        $entityRefName = $object->getEntityRefName();

        $registry = new ObjectRegistrySQL($object);
		$object_it = $registry->Query(
				array (
					new ParentTransitiveFilter($this->getObjectIt()->idsToArray()),
                    new FilterAttributePredicate('DocumentId', $this->getObjectIt()->fieldToArray('DocumentId'))
				)
			);

		while( !$object_it->end() )
        {
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

            $link->add_parms( array(
                'SourcePage' => $object_it->getId(),
                'TargetPage' => $map[$entityRefName][$object_it->getId()],
                'IsActual' => 'Y',
                'SourceBaseline' => $_REQUEST['Snapshot'],
                'Type' => 'branch'
            ));

            $parms = array();

            // remap dependencies
            $dependencies =
                array_map(
                    function( $item ) use ( $registry, $baselineName ) {
                        if ( $item == '' ) return '';
                        list($className, $id) = preg_split('/:/', $item);
                        $id = $registry->Query(
                                array(
                                    new WikiPageBranchFilter($baselineName),
                                    new WikiPageSameUIDPredicate($id)
                                )
                            )->getId();
                        if ( $id == '' ) return $item;
                        return $className . ':' . $id;
                    },
                    preg_split('/,/', $object_it->get('Dependency'))
                );
            $dependencyNew = join(',', $dependencies);
            if ( $dependencyNew != $object_it->get('Dependency') ) {
                $parms['Dependency'] = $dependencyNew;
            }

            // remap includes
            if ( $object_it->get('Includes') != '' ) {
                $parms['Includes'] = $registry->Query(
                        array(
                            new WikiPageBranchFilter($baselineName),
                            new WikiPageSameUIDPredicate($object_it->get('Includes'))
                        )
                    )->getId();
            }

            $targetId = $map[$entityRefName][$object_it->getId()];
            if ( count($parms) > 0 && $targetId > 0 ) {
                $object->modify_parms($targetId, $parms);
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

                if ( preg_match('/[IU]-(\d+)/i', $this->baselineIt->getId(), $matches) )
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
 	
 	function storeBranch( & $map, $object, $parms, $baselineService )
 	{
 	 	$snapshot = getFactory()->getObject('Snapshot');

		foreach( $this->getObjectIt()->idsToArray() as $object_id )
		{
            $documentId = $map[$object->getEntityRefName()][$object_id];
			if ( $documentId == '' ) continue;

            $baselineService->storeBranch(
                $object->createCachedIterator(array(
                    array(
                        $object->getIdAttribute() => $documentId
                    )
                )),
                $this->baselineIt,
                $parms['Version'],
                $parms['Description']
            );
		}
 	}

	function storeVersion( & $map, $object, $parms, $baselineService )
	{
		$snapshot = getFactory()->getObject('Snapshot');
		$trace = getFactory()->getObject('WikiPageTrace');
        $productTrace = getFactory()->getObject('RequestTraceBase');
        $request = getFactory()->getObject('Request');

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

            if ( $object_it->getHtmlDecoded('DocumentVersion') != '' ) {
                $baselineIt = $this->baselineIt;
                $caption = $object_it->getHtmlDecoded('DocumentVersion');
            }
            else {
                $caption = text(2306);
                $baselineIt = getFactory()->getObject('Baseline')->getEmptyIterator();
            }

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

            $versionId = $baselineService->storeBaseline($object_it, $caption, $baselineIt->getId());
            if ( $versionId < 1 ) {
                $object_it->moveNext();
                continue;
            }

			// fix dependency for downstream traces
			$ids = array_filter(
                $trace->getRegistry()->QueryKeys(
                        array(
                            new WikiTraceSourceDocumentPredicate($object_id)
                        )
                    )->idsToArray(),
			    function($id) {
			        return $id > 0;
                }
            );
			if ( count($ids) > 0 ) {
                DAL::Instance()->Query(" UPDATE WikiPageTrace SET SourceBaseline = ".$versionId." WHERE WikiPageTraceId IN (".join(',',$ids).") ");
            }

            // fix dependency for upstream traces
            $traceIt = $trace->getRegistry()->Query(
                array(
                    new FilterAttributePredicate('TargetPage', $object_id)
                )
            );
            if ( $traceIt->getId() != '' ) {
                $versionIt = $snapshot->getRegistry()->Query(
                    array(
                        new FilterAttributePredicate('Stage', $parms['Version']),
                        new FilterAttributePredicate('ObjectId', $traceIt->get('SourcePage')),
                        new FilterHasNoAttributePredicate('Type', 'branch')
                    )
                );
            }
            else {
                $versionIt = $snapshot->getEmptyIterator();
            }

            $ids = array_filter(
                $trace->getRegistry()->QueryKeys(
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

            // fix increments description
            DAL::Instance()->Query(" 
                UPDATE pm_ChangeRequestTrace t SET t.Baseline = ".$versionId." 
                 WHERE EXISTS (SELECT 1 FROM WikiPage p 
                                WHERE p.ParentPath LIKE '%,".$object_it->getId().",%' 
                                  AND p.WikiPageId = t.ObjectId)
                   AND t.Type = '".REQUEST_TRACE_PRODUCT."' 
                   AND t.Baseline IS NULL
            ");

            $ids = $productTrace->getRegistry()->Query(
                    array(
                        new \RequestTraceRequirementLinkedPredicate($object_it)
                    )
                )->fieldToArray('ChangeRequest');

            $action = new RequestBusinessActionMaterializeContent();
            $requestIt = $request->getExact($ids);
            while( !$requestIt->end() ) {
                $action->apply($requestIt);
                $requestIt->moveNext();
            }

            $object_it->moveNext();
        }
    }

	function hasAccess() {
		return getFactory()->getAccessPolicy()->can_create($this->getObject());
	}

    function getJSCall($parms = array())
    {
        return parent::getJSCall(
            array_merge($parms,
                array(
                    'CopyOption' => 'N'
                )
            )
        );
    }

	private $parent_it;
}
