<?php

class ReqManagementModeRegistry extends ObjectRegistrySQL
{
	const None = 'N';
	const RDD = 'Y';
	const Documenting = 'I';

	public function Query($parms = array())
	{
		return $this->createIterator( array (
				array ( 'entityId' => self::None, 'Caption' => text(2179) ),
				array ( 'entityId' => self::Documenting, 'Caption' => text(2180) ),
				array ( 'entityId' => self::RDD, 'Caption' => text(2181) )
		));
	}
}