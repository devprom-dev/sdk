<?php

 class BuildCodeRevisionPersister extends ObjectSQLPersister
 {
 	function getSelectColumns( $alias )
 	{
 		return array( 
 			" ( SELECT '' ) BuildRevision " 
 		);
 	}
 }
