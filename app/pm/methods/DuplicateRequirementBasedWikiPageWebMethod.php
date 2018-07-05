<?php

include_once SERVER_ROOT_PATH."pm/methods/DuplicateWikiPageWebMethod.php";

abstract class DuplicateRequirementBasedWikiPageWebMethod extends DuplicateWikiPageWebMethod
{
	abstract protected function getRequirementAttribute();

 	function storeTraces( & $map, & $object )
 	{
 		foreach( $this->getObjectIt()->idsToArray() as $object_id )
 		{
 			// get hierarchy with the root of $object_id
			$object_it = $object->getRegistry()->Query( 
					array(
						new ParentTransitiveFilter($object_id)
					)
			);
	 		
			$ids = array();
			foreach( $object_it->fieldToArray($this->getRequirementAttribute()) as $req_id )
			{
				$ids = array_merge( $ids, array_filter(preg_split('/,/', $req_id), function( $value ) {
						return $value > 0;
				}));
			}
			
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
				
				$document_id = array_shift(array_unique($trace_it->fieldToArray('TargetDocumentId')));
		 	 	if ( $document_id > 0 ) {
	 	 			$this->makeTracesOnRequirementVersion( $map, $document_id, $object_it ); 
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
			foreach( preg_split('/,/', $object_it->get($this->getRequirementAttribute())) as $requirement_id )
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
			$ids = array_filter(preg_split('/,/', $object_it->get($this->getRequirementAttribute())), function($value) {
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
