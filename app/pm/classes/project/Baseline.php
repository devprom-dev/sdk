<?php
include "BaselineIterator.php";
include "BaselineRegistry.php";

class Baseline extends Metaobject
{
	public function __construct() {
		parent::__construct('pm_Version', new BaselineRegistry($this));
	}

	function createIterator() {
        return new BaselineIterator($this);
    }

    function IsDeletedCascade( $object ) {
		return false;
	}
 	
	function IsUpdatedCascade( $object ) {
		return false;
	}

	function getVpdValue() {
        return '';
    }

    function getBySnapshotId( $snapshotId ) {
	    $snapshotIt = getFactory()->getObject('cms_Snapshot')->getExact($snapshotId);
	    return $this->createCachedIterator( array(
                array(
                    'pm_VersionId' => $snapshotIt->get('Stage')
                )
            )
        );
    }
}