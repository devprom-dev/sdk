<?php

include_once SERVER_ROOT_PATH."pm/methods/DuplicateWikiPageWebMethod.php";

class DuplicateRequirementBasedWikiPageWebMethod extends DuplicateWikiPageWebMethod
{
 	function storeTraces( & $map, & $object )
 	{
 		foreach( $this->getObjectIt()->idsToArray() as $object_id )
 		{
 			// get hierarchy with the root of $object_id
			$object_it = $object->getRegistry()->Query( 
					array(
						new WikiRootTransitiveFilter($object_id)
					)
			);
	 		
			$ids = array_filter(preg_split('/,/', $object_it->get('Requirement')), function( $value ) {
					return $value > 0;
			});
			
			if ( count($ids) > 0 )
			{
				// get requirement branch 
				$trace_it = getFactory()->getObject('WikiPageTrace')->getRegistry()->Query( 
						array(
							new FilterAttributePredicate('SourcePage', $ids),
							new FilterAttributePredicate('Type', 'branch'),
							new WikiTraceTargetBaselinePredicate(IteratorBase::utf8towin($_REQUEST['Version']))
						)
				);

		 	 	if ( $trace_it->get('TargetPage') > 0 ) {
	 	 			$this->makeTracesOnRequirementVersion( $map, $trace_it->get('TargetPage'), $object_it ); 
	 		 	}
	 		 	else {
	 		 		$this->makeTracesOnSourceRequirements( $map, $object_it );
	 		 	}
			}
			else {
				$this->makeTracesOnSourceRequirements( $map, $object_it );
			}
 		}
 	 	
 	 	parent::storeTraces( $map, $object );
 	}
 	
 	protected function makeTracesOnSourceRequirements( & $map, & $object_it )
 	{
		$link = getFactory()->getObject('WikiPageTrace');
		
		while( !$object_it->end() )
		{
			foreach( preg_split('/,/', $object_it->get('Requirement')) as $requirement_id )
			{
				$attributes = array( 
	    		    'SourcePage' => $requirement_id,
	    			'TargetPage' => $map[$object_it->object->getEntityRefName()][$object_it->getId()]
	    		);
				
				if ( $link->getByRefArray($attributes)->count() < 1 )
				{
					$link->add_parms(array_merge($attributes, array('IsActual' => 'Y')));
				}
			}
			
    		$object_it->moveNext();
		}
 	}

 	protected function makeTracesOnRequirementVersion( & $map, $document_id, & $object_it )
 	{
		$link = getFactory()->getObject('WikiPageTrace');
		
		while( !$object_it->end() )
		{
			$ids = array_filter(preg_split('/,/', $object_it->get('Requirement')), function($value) {
					return $value > 0;
			});
			
			if ( count($ids) < 1 )
			{
				$object_it->moveNext(); continue;
			}
			
			// get req-to-req traces where target belongs to the document (document_id)
			$trace_it = $link->getRegistry()->Query( 
					array(
						new FilterAttributePredicate('SourcePage', $ids),
						new FilterAttributePredicate('Type', 'branch'),
						new WikiTraceTargetDocumentPredicate($document_id)
					)
			);
			
			// cover requirements of the document (document_id) by the testing document 
			foreach( $trace_it->fieldToArray('TargetPage') as $requirement_id )
			{
				$attributes = array( 
	    		    'SourcePage' => $requirement_id,
	    			'TargetPage' => $map[$object_it->object->getEntityRefName()][$object_it->getId()]
	    		);

				if ( $link->getByRefArray($attributes)->count() < 1 )
				{
					$link->add_parms(array_merge($attributes, array('IsActual' => 'Y')));
				}
			}
			
    		$object_it->moveNext();
		}
 	}
}
