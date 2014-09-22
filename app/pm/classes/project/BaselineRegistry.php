<?php

class BaselineRegistry extends ObjectRegistrySQL
{
	public function getAll()
	{
		$stage_it = getFactory()->getObject('Stage')->getAll();
		
		$values = array();
		
		while( !$stage_it->end() )
		{
			$values[$stage_it->getDisplayName()] = array (
					'pm_VersionId' => $stage_it->getDisplayName(),
					'Caption' => $stage_it->getDisplayName() 
			);
			
			$stage_it->moveNext();
		}
		
		$branch_it = getFactory()->getObject('cms_Snapshot')->getRegistry()->Query(
					array (
						new FilterVpdPredicate($this->getObject()->getVpds()),
						new FilterAttributePredicate('Type', 'branch')
					) 
			 );

		while( !$branch_it->end() )
		{
			$values[$branch_it->getDisplayName()] = array (
					'pm_VersionId' => $branch_it->getDisplayName(),
					'Caption' => $branch_it->getDisplayName() 
			);
			
			$branch_it->moveNext();
		}
		
		return $this->createIterator(array_values($values));
	}
}