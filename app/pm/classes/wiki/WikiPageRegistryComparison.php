<?php

class WikiPageRegistryComparison extends ObjectRegistrySQL
{
	public function setPageIt($page_it)	{
		$this->page_it = $page_it;
	}
	
	public function setBaselineIt($baseline_it)	{
		$this->baseline_it = $baseline_it;
	}
	
	function Query( $parms = array() )
	{
		$persisters = array();
		
		$query_filters = array();
		$registry = $this->page_it->object->getRegistry();

		if ( $this->baseline_it->get('Type') == 'document' )
		{
			$document_id = $this->baseline_it->get('ObjectId');
			$registry = new WikiPageRegistryContent($this->page_it->object);
		}
		else
		{
			// version given
			if ( $this->baseline_it->object instanceof WikiPageComparableSnapshot )
			{
				if ( $this->baseline_it->get('Type') != 'branch' ) {
					// just version of the document
					$persisters[] = new SnapshotItemValuePersister($this->baseline_it->getId());
					$query_filters = array (
						new FilterInPredicate($this->page_it->getId())
					);
				}
				else {
					// or baseline given
					$document_id = $this->baseline_it->get('ObjectId');
				}
			}
			else {
				// or the document as itself
				$document_id = $this->baseline_it->getId();
				$registry = new WikiPageRegistryContent($this->page_it->object);
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
			// baseline given
			$query_filters = array(
				new FilterAttributePredicate('UID', $this->page_it->get('UID')),
				new FilterAttributePredicate('DocumentId', $document_id)
			);
		}

		return $registry->Query(array_merge($query_filters, $persisters));
	}
	
	private $baseline_it;
	private $page_it;
}