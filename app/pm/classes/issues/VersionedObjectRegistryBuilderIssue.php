<?php

include_once SERVER_ROOT_PATH."core/classes/versioning/VersionedObjectRegistryBuilder.php";

class VersionedObjectRegistryBuilderIssue extends VersionedObjectRegistryBuilder
{
	public function build( VersionedObjectRegistry & $registry )
	{
		$registry->add( 'Request', array('Estimation', 'Description') );
	}
}