<?php

class AutoActionRegistry extends ObjectRegistrySQL
{
	function getFilters()
	{
		return array_merge(
            parent::getFilters(),
            array (
                new FilterAttributePredicate('ClassName', strtolower($this->getObject()->getSubjectClassName()))
            )
		);
	}
	
	function createSQLIterator($sql)
	{
		$rows = parent::createSQLIterator($sql)->getRowset();
		foreach( $rows as $row => $data )
		{
			$action_fields = JsonWrapper::decode(html_entity_decode($data['Actions'], ENT_QUOTES | ENT_HTML401, APP_ENCODING ));
			if ( is_array($action_fields) ) {
				$rows[$row] = array_merge( $data, $action_fields );
			}
		}
		return $this->createIterator($rows);
	}
}