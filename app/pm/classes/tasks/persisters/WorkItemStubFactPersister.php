<?php

class WorkItemStubFactPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array (
 			" t.Fact "
		);
 	}
}
