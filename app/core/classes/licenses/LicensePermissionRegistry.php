<?php
include_once "LicensePermissionRegistryBuilder.php";

class LicensePermissionRegistry extends ObjectRegistrySQL
{
	private $data = array();
	
	function add( $plugin, $title, $entities = array(), $global = false )
	{
		$this->data[] = array(
		    'Plugin' => $plugin,
		    'Caption' => $title,
            'Entities' => $entities,
            'AppliedGlobally' => $global
        );
	}

	public function createSQLIterator( $sql )
	{
		foreach( getSession()->getBuilders('LicensePermissionRegistryBuilder') as $builder ) {
			$builder->build( $this ); 
		}
        $licenseIt = getFactory()->getObject('LicenseInstalled')->getAll();

		$data = array();
		foreach( $this->data as $permission )
		{
			$data[] = array (
                'entityId' => $permission['Plugin'],
                'Caption' => $permission['Caption'],
                'ReferenceName' => join(',', $permission['Entities']),
                'IsGlobal' => $licenseIt instanceof LicenseTrialIterator ? 'Y' : ($permission['AppliedGlobally'] ? 'Y' : 'N')
			);
		}
		return $this->createIterator($data);
	}
}
