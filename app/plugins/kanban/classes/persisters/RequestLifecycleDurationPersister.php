<?php

class RequestLifecycleDurationPersister extends ObjectSQLPersister
{
	function getAttributes() {
		return array (
			"LifecycleDuration"
		);
	}

 	function getSelectColumns( $alias )
 	{
 		return array (
			" (SELECT t.LifecycleDuration / 24 ) LifecycleDuration "
		);
 	}
}