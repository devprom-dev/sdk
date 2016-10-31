<?php

class RequestFactPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array(
 			" t.Fact "
 		);
 	}
}

