<?php

class ChangeLogRegistryProjectTemplate extends ObjectRegistrySQL
{
	function getSorts() 
	{
		return array_merge(
            array (
                new SortKeyClause()
            )
		);
	}
}