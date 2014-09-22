<?php

class MethodologyPlanningModeRegistry extends ObjectRegistrySQL
{
	const None = 'N';
	const Releases = 'Y';
	const Iterations = 'I';
	
	public function getAll()
	{
		return $this->createIterator( array (
				array ( 'entityId' => MethodologyPlanningModeRegistry::None, 'Caption' => text(1718) ),
				array ( 'entityId' => MethodologyPlanningModeRegistry::Releases, 'Caption' => text(1719) ),
				array ( 'entityId' => MethodologyPlanningModeRegistry::Iterations, 'Caption' => text(1720) )
		));
	}
}