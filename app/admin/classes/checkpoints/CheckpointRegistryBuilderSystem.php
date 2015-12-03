<?php

if ( !class_exists('CheckpointRegistryBuilder', false) )
{
	include_once dirname(__FILE__)."/../CheckpointRegistryBuilder.php";
}

include 'CheckpointPHPSetting.php';
include 'CheckpointSystemAdminEmail.php';
include 'CheckpointApacheModuleLoaded.php';
include 'CheckpointCronRunning.php';
include 'CheckpointDirectoryWritable.php';
include 'CheckpointDiskSpace.php';
include 'CheckpointExtentionLoaded.php';
include 'CheckpointHasAdmininstrator.php';
include 'CheckpointPhpVersion.php';
include 'CheckpointWindowsSMTP.php';
include 'CheckpointNoCrashedTables.php';
include 'CheckpointTablesOptimized.php';
include "CheckpointUpdatesAvailable.php";
include "CheckpointTablesPartitioned.php";
include "CheckpointMySQLVariables.php";
include "CheckpointBackups.php";
include "CheckpointSupportPayed.php";

class CheckpointRegistryBuilderSystem extends CheckpointRegistryBuilder
{
	public function build( & $registry )
	{
		$entries = array (
			new CheckpointSystemAdminEmail(),
			new CheckpointDiskSpace(SERVER_ROOT_PATH),
			new CheckpointDiskSpace(sys_get_temp_dir()),
			new CheckpointCronRunning(),
			new CheckpointHasAdmininstrator(),
			new CheckpointWindowsSMTP(),
			new CheckpointPhpVersion(),
			new CheckpointPHPSetting(),
		    new CheckpointApacheModuleLoaded( 'mod_rewrite' ),
			new CheckpointDirectoryWritable(),
			new CheckpointMySQLVariables(),
			new CheckpointNoCrashedTables(),
			new CheckpointTablesOptimized(),
		    new CheckpointTablesPartitioned(),
			new CheckpointBackups(),
			new CheckpointSupportPayed(),
		    new CheckpointUpdatesAvailable()
		);

		$registry->registerEntries( $entries );
	}
}