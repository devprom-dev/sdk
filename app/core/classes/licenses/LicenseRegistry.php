<?php

class LicenseRegistry extends ObjectRegistrySQL
{
	private $licenses = array();
	
	function addLicense( $object )
	{
		array_unshift($this->licenses, $object);
	}

	function resetLicenses()
	{
		$this->licenses = array();
	}
	
	public function createSQLIterator( $sql )
	{
		foreach( getSession()->getBuilders('LicenseRegistryBuilder') as $builder )
		{
			$builder->build( $this ); 
		}
		
		$data = array();
		
		foreach( $this->licenses as $license )
		{
			$data[] = array (
					'LicenseType' => get_class($license),
					'Caption' => $license->getAttributeUserName('Caption'),
					'Description' => $license->getAttributeDescription('Caption')
			);
		}
		
		return $this->createIterator($data);
	}
}
