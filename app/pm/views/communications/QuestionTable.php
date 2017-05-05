<?php
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
		$filters = array(
			new FilterStateMethod( $this->getObject() ),
			new FilterStateTransitionMethod( $this->getObject() ),
			$this->buildTagsFilter(),
			new FilterUserAuthorWebMethod()
		);
		
		return array_merge( $filters, parent::getFilters() );
	}

    protected function buildTagsFilter()
    {
        $tag = getFactory()->getObject('QuestionTag');
        $filter = new FilterObjectMethod($tag, translate('Тэги'), 'tag');
        $filter->setIdFieldName('Tag');
        return $filter;
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
}