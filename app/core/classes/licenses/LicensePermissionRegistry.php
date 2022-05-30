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

        $licenseParms = $licenseIt->getOptions();
        $options = is_array($licenseParms['options']) ? \TextUtils::parseItems($licenseParms['options']) : array();
        if ( in_array('ee', $options) ) {
            $options = array_unique(array_merge($options, array('ppm','resm','perm')));
        }
        if ( in_array('agile', $options) ) {
            $options = array_unique(array_merge($options, array('ppm','resm','perm', 'dev')));
        }

		$data = array();
		foreach( $this->data as $permission ) {
            if ( count($options) > 0 && !in_array($permission['Plugin'], $options) ) continue;
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
