<?php

include "ArtefactList.php";

class ArtefactTable extends PMPageTable
{
	function getList()
	{
		return new ArtefactList( $this->getObject() );
	}

	function getFilters()
	{
		global $model_factory;
		
		$filters = array (
			new FilterObjectMethod( $model_factory->getObject('pm_ArtefactType'),
				translate('Каталог') )
		);
		
		$filters[] = new FilterObjectMethod(
			$model_factory->getObject('Version'), translate('Версия') );
		
		return $filters;
	}
} 