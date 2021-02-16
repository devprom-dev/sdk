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
			$this->buildStateFilter(),
			new FilterStateTransitionMethod( $this->getObject() ),
			$this->buildTagsFilter(),
			$this->buildAuthorFilter()
		);
		
		return array_merge( $filters, parent::getFilters() );
	}

	protected function buildAuthorFilter() {
        $filter = new FilterObjectMethod(getFactory()->getObject('ProjectUser'), translate('Автор'), 'author');
        return $filter;
    }

    protected function buildStateFilter() {
        $filter = new FilterStateMethod( $this->getObject() );
        $filter->setDefaultValue('none,N,I');
        return $filter;
    }

    protected function buildTagsFilter()
    {
        $tag = getFactory()->getObject('QuestionTag');
        $filter = new FilterObjectMethod($tag, translate('Тэги'), 'tag');
        $filter->setIdFieldName('Tag');
        return $filter;
    }

	function getSortDefault( $sort_parm = 'sort' )
	{
		if ( $sort_parm == 'sort' )
		{
			return 'LastCommentDate.D';
		}

		return parent::getSortDefault($sort_parm);
	}

    protected function getFamilyModules( $module )
    {
        return array(
            'whatsnew',
            'project-log'
        );
    }
}