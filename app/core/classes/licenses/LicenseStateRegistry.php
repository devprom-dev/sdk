<?php

class LicenseStateRegistry extends ObjectRegistrySQL
{
    private static $data = array();

	public function getAll()
	{
	    if ( count(self::$data) > 0 ) {
            return $this->createIterator(self::$data);
        }

        $it = getFactory()->getObject('LicenseInstalled')->getAll();
        if ( !class_exists($it->get('LicenseType')) ) {
            return $this->getObject()->getEmptyIterator();
        }

		return $this->createIterator(
            self::$data = array(
                array (
                    'cms_LicenseId' => $it->getId(),
                    'IsValid' => $it->valid() && $it->getLeftDays() >=0 ? 'Y' : 'N',
                    'Caption' => $it->getName(),
                    'LicenseType' => $it->get('LicenseType'),
                    'Options' => $it->getOptions()
                )
            )
        );
	}
}

