<?php

class WikiPageWorkflowPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array(
			" (SELECT CONCAT('[',GROUP_CONCAT(CONCAT('{\"action\":\"', tr.Caption, '\",\"author\":\"', u.Caption, '\",\"date\":\"', o.RecordCreated, '\"}') ORDER BY o.RecordCreated DESC SEPARATOR ','),']')
    		     FROM pm_StateObject o, pm_Transition tr, cms_User u
                WHERE o.ObjectId = t.WikiPageId
                  AND o.ObjectClass = '".get_class($this->getObject())."'
                  AND o.Transition = tr.pm_TransitionId
                  AND u.cms_UserId = o.Author) Workflow "
		);
 	}
}
