<?php

class UserActiveRegistry extends ObjectRegistrySQL
{
	public function getFilters()
	{
		return array_merge( parent::getFilters(), 
				array (
						new UserStatePredicate('active')
				)
		);
	}

	public function getSorts()
	{
		return array_merge( parent::getSorts(), 
				array (
						new SortAttributeClause('Caption')
				)
		);
	}
}
 