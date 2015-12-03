<?php

class LicenseStateRegistry extends ObjectRegistrySQL
{
	static $license_state = array();
	
	public function createSQLIterator($sql)
	{
		if ( count(self::$license_state) < 1 )
		{
			$it = getFactory()->getObject('LicenseInstalled')->getAll();
			if ( !class_exists($it->get('LicenseType')) ) {
				return $this->getObject()->getEmptyIterator();
			}
			
			self::$license_state[] = array (
					'cms_LicenseId' => $it->getId(),
					'IsValid' => $it->valid() && $it->getLeftDays() >=0 ? 'Y' : 'N',
					'Caption' => $it->getName(),
					'LicenseType' => $it->get('LicenseType'),
					'Options' => $it->getOptions()
			); 
		}

		return $this->createIterator(self::$license_state);
	}
}

