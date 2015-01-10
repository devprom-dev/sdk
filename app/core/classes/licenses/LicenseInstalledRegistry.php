<?php

class LicenseInstalledRegistry extends ObjectRegistrySQL
{
	public function createSQLIterator( $sql )
	{
		$iterator = parent::createSQLIterator( $sql );
		
		if ( $iterator->count() < 1 ) return $iterator;
		if ( !class_exists($iterator->get('LicenseType')) ) return $iterator;
		
		$class_name = $iterator->get('LicenseType');
		
		$license = new $class_name;
		
		return $license->createCachedIterator($iterator->getRowset());
	}
}
