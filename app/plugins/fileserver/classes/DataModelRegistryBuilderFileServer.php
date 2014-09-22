<?php

include_once SERVER_ROOT_PATH."api/classes/model/IDataModelRegistryBuilder.php";

class DataModelRegistryBuilderFileServer implements IDataModelRegistryBuilder
{
	public function build( DataModelRegistry & $registry )
	{ 
		$registry->addClass( array (
				'Artefact',
				'ArtefactType'
		));
	}
}