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
		
		if ( getSession()->getProjectIt()->getMethodologyIt()->HasVersions() )
		{
			$filters[] = new FilterAutoCompleteWebMethod( 
				$model_factory->getObject('Version'), translate('Версия') );
		}
		
		return $filters;
	}
	
 	function getActions()
	{
		global $model_factory;
		
		$list = $this->getListRef();
		
		$actions = array();
		
		array_push($actions, array( 'name' => translate('Выбрать все'),
			'url' => 'javascript: checkRowsTrue(\''.$list->getId().'\');', 'title' => text(969) ) );

		array_push($actions, array( 'name' => translate('Массовые операции'),
			'url' => 'javascript: processBulkMethod();', 'title' => text(651) ) );
		$actions[] = array();

		$base_actions = parent::getActions();
		
		return array_merge( array_slice($base_actions, 0, 2),
			$actions, array_slice($base_actions, 2) );
	}	
} 