<?php
include_once SERVER_ROOT_PATH."pm/methods/DuplicateWikiPageWebMethod.php";

abstract class DuplicateRequirementBasedWikiPageWebMethod extends DuplicateWikiPageWebMethod
{
	abstract protected function getRequirementAttribute();

 	function storeTraces( & $map, & $object, $baselineName )
 	{
 		foreach( $this->getObjectIt()->idsToArray() as $object_id )
 		{
 			// get hierarchy with the root of $object_id
			$object_it = $object->getRegistry()->Query( 
                array(
                    new ParentTransitiveFilter($object_id)
                )
			);
	 		
			$ids = \TextUtils::parseIds(join(',',$object_it->fieldToArray($this->getRequirementAttribute())));
			if ( count($ids) > 0 )
			{
				// get requirement branch 
				$trace_it = getFactory()->getObject('WikiPageTrace')->getRegistry()->Query( 
                    array(
                        new FilterAttributePredicate('SourcePage', $ids),
                        new FilterAttributePredicate('Type', 'branch'),
                        new WikiTraceTargetBaselinePredicate($baselineName)
                    )
				);

                $documents = array_unique($trace_it->fieldToArray('TargetDocumentId'));
		 	 	if ( count($documents) > 0 ) {
		 	 	    foreach( $documents as $document_id ) {
                        $this->makeTracesOnRequirementVersion( $map, $document_id, $object_it );
                    }
	 		 	}
	 		 	else {
	 		 		$this->makeTracesOnSourceRequirements( $map, $object_it );
	 		 	}
			}
			else {
				$this->makeTracesOnSourceRequirements( $map, $object_it );
			}
 		}
 	 	
 	 	parent::storeTraces( $map, $object, $baselineName );
 	}
 	
 	protected function makeTracesOnSourceRequirements( & $map, & $object_it )
 	{
		$link = getFactory()->getObject('WikiPageTrace');
        $linkRegistry = $link->getRegistry();

        $object_it->moveNext();
		while( !$object_it->end() )
		{
            $ids = \TextUtils::parseIds($object_it->get($this->getRequirementAttribute()));
            if ( count($ids) < 1 ) {
                $object_it->moveNext();
                continue;
            }

            foreach( $ids as $requirement_id ) {
                $linkRegistry->Merge(
                    array(
                        'SourcePage' => $requirement_id,
                        'TargetPage' => $map[$object_it->object->getEntityRefName()][$object_it->getId()],
                        'IsActual' => 'Y'
                    ),
                    array('SourcePage', 'TargetPage')
                );
            }
    		$object_it->moveNext();
		}
 	}

 	protected function makeTracesOnRequirementVersion( & $map, $document_id, & $object_it )
 	{
		$link = getFactory()->getObject('WikiPageTrace');
		$linkRegistry = $link->getRegistry();

        $object_it->moveFirst();
        while( !$object_it->end() )
		{
            $ids = \TextUtils::parseIds($object_it->get($this->getRequirementAttribute()));
			if ( count($ids) < 1 ) {
				$object_it->moveNext();
				continue;
			}
			
			// get req-to-req traces where target belongs to the document (document_id)
			$trace_it = $linkRegistry->Query(
                array(
                    new FilterAttributePredicate('SourcePage', $ids),
                    new FilterAttributePredicate('Type', 'branch'),
                    new WikiTraceTargetDocumentPredicate($document_id)
                )
			);
			
			// cover requirements of the document (document_id) by the testing document 
			foreach( $trace_it->fieldToArray('TargetPage') as $requirement_id ) {
                $linkRegistry->Merge(
                    array(
                        'SourcePage' => $requirement_id,
                        'TargetPage' => $map[$object_it->object->getEntityRefName()][$object_it->getId()],
                        'IsActual' => 'Y'
                    ),
                    array('SourcePage', 'TargetPage')
                );
			}
			
    		$object_it->moveNext();
		}
 	}
}
