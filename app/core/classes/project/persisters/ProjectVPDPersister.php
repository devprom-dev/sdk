<?php

class ProjectVPDPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array(
            " LCASE(CodeName) LowerCodeName ",
            " CodeName ProjectCodeName "
        );
 	}
}
