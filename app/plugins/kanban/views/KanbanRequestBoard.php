<?php

class KanbanRequestBoard extends RequestBoard
{
	function buildBoardAttributeIterator()
	{
		return getFactory()->getObject('IssueState')->getRegistry()->Query(
				array (
						new FilterVpdPredicate(array_shift($this->getTable()->getProjectVpds())),
						new SortAttributeClause('OrderNum')
				)
		);
	}
}