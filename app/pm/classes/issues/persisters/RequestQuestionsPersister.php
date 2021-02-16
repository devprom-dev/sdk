<?php

class RequestQuestionsPersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array('Question');
    }

    function getSelectColumns( $alias )
 	{
 		$trace = getFactory()->getObject('RequestTraceQuestion');
 		return array( 
 			" ( SELECT GROUP_CONCAT(CAST(l.ObjectId AS CHAR)) " .
			"     FROM pm_ChangeRequestTrace l " .
			"    WHERE l.ChangeRequest = t.pm_ChangeRequestId" .
 			"      AND l.ObjectClass = '".$trace->getObjectClass()."' ) Question " 
 		);
 	}
}
 