<?php

class WikiPageRegistryComparison extends ObjectRegistrySQL
{
	public function setPageIt($page_it)
	{
		$this->page_it = $page_it;
	}
	
	public function setBaselineIt($baseline_it)
	{
		$this->baseline_it = $baseline_it;
	}
	
	function Query( $parms = array() )
	{
		$persisters = array();
		
		$query_filters = array();
		
		if ( $this->baseline_it->get('Type') == 'document' )
		{
			$document_id = $this->baseline_it->get('ObjectId');
		}
		else
		{
			// version or baseline given
			if ( $this->baseline_it->get('Type') != 'branch' )
			{
				// just version of the document
				$persisters[] = new SnapshotItemValuePersister($this->baseline_it->getId());
				
				$query_filters = array ( 
						new FilterInPredicate($this->page_it->getId())
				); 
			}
			else
			{
				$document_id = $this->baseline_it->get('ObjectId');
			}
		}			

		if ( count($query_filters) < 1 && !$this->page_it->IsPersisted() )
		{
			// if source page is fake then return the real one as comparitive
			$query_filters = array ( 
					new FilterInPredicate($this->page_it->getId())
			); 
		}
		
		if ( count($query_filters) < 1 && $document_id > 0 )
		{
			if ( !$this->page_it->IsPersisted() )
			{ 
				$query_filters = array ( 
						new FilterInPredicate($this->page_it->getId())
				); 
			}
			
			// baseline given
			$query_filters = array();
			 
			// transitive origins (baselines) of the document
			$trace_registry = getFactory()->getObject('WikiPageTrace')->getRegistry();
			
			$source = $this->page_it->getId();
		
			while( count($query_filters) < 1 && $source > 0 )
			{
				$trace_it = $trace_registry->Query(
						array (
								new FilterAttributePredicate('TargetPage', $source),
								new WikiTraceSourceDocumentPredicate($document_id),
								new FilterAttributePredicate('Type', 'branch')
						)
					);

				if ( $trace_it->count() > 0 )
				{
					$query_filters = array (
							new FilterInPredicate($trace_it->fieldToArray('SourcePage'))		
					);
					
					break;
				}
				
				$source = $trace_it->get('SourcePage');
			}

			$source = $this->page_it->getId();
		
			while( count($query_filters) < 1 && $source > 0 )
			{
				$trace_it = $trace_registry->Query(
						array (
								new FilterAttributePredicate('SourcePage', $source),
								new WikiTraceTargetDocumentPredicate($document_id),
								new FilterAttributePredicate('Type', 'branch')
						)
					);

				if ( $trace_it->count() > 0 )
				{
					$query_filters = array (
							new FilterInPredicate($trace_it->fieldToArray('TargetPage'))		
					);
					
					break;
				}
				
				$source = $trace_it->get('TargetPage');
			}
			
			if ( count($query_filters) < 1 ) $query_filters[] = new FilterInPredicate(0);
		}
							
		$this->page_it->object->setSortDefault(array());
		return $this->page_it->object->getRegistry()->Query(array_merge($query_filters, $persisters));		
	}
	
	private $baseline_it;
	
	private $page_it;
}