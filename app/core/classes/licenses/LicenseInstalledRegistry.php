<?php

class LicenseInstalledRegistry extends ObjectRegistrySQL
{
	public function createSQLIterator( $sql )
	{
		$iterator = parent::createSQLIterator( $sql );
		
		if ( $iterator->count() < 1 )
		{
			getFactory()->error('License entity wasn\'t found');
			
			return $iterator;
		}
		
		if ( !class_exists($iterator->get('LicenseType')) )
		{
			getFactory()->error('License class wasn\'t found: '.$iterator->get('LicenseType'));
			
			return $iterator;
		}
		
		$class_name = $iterator->get('LicenseType');
		
		$license = new $class_name;
		
		return $license->createCachedIterator($iterator->getRowset());
	}
}
