<?php

include ('JobList.php');

class JobTable extends PageTable
{
	function getList()
	{
		return new JobList( $this->object );
	}

	function getSortDefault( $sort_parm )
	{
		if ( $sort_parm == 'sort' ) return 'OrderNum';
		return parent::getSortDefault( $sort_parm );
	}

	function getCaption()
    {
        return text(2024);
    }
}
