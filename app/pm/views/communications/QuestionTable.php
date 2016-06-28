<?php

if ( !class_exists('FilterTagWebMethod', false) ) include(SERVER_ROOT_PATH.'pm/methods/c_tag_methods.php');
include_once SERVER_ROOT_PATH."pm/methods/FilterStateTransitionMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/FilterStateMethod.php";
include "QuestionList.php";

class QuestionTable extends PMPageTable
{
	function getList()
	{
		return new QuestionList( $this->object );
	}

	function getFilters()
	{
		global $model_factory;
		
		$filters = array(
			new FilterStateMethod( $this->getObject() ),
			new FilterStateTransitionMethod( $this->getObject() ),
			new FilterTagWebMethod( $model_factory->getObject('QuestionTag') ),
			new FilterUserAuthorWebMethod()
		);
		
		return array_merge( $filters, parent::getFilters() );
	}
	
	function getFiltersDefault()
	{
		return array('author', 'state', 'tag');
	}
	
	function getSortDefault( $sort_parm = 'sort' )
	{
		if ( $sort_parm == 'sort' )
		{
			return 'LastCommentDate.D';
		}

		return parent::getSortDefault($sort_parm);
	}
	
	function getActions()
	{
		global $project_it, $plugins;
		
		$actions = parent::getActions();
		
		if ( $actions[count($actions) - 1]['name'] != '' )
		{
			array_push( $actions, array() );
		}

		$method = new ExcelExportWebMethod();

		array_push($actions, array( 'name' => $method->getCaption(),
			'url' => $method->url( $this->getCaption(), 'IteratorExportExcel') ) );
		
		$method = new HtmlExportWebMethod();

		array_push($actions, array( 'name' => $method->getCaption(),
			'url' => $method->url( 'IteratorExportHtml' ) ) );

		return $actions;
	}	
}