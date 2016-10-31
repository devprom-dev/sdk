<?php

class WikiPageWorkflowPersister extends ObjectSQLPersister
{
	function getAttributes()
	{
		return array('Workflow');
	}

 	function getSelectColumns( $alias )
 	{
 		return array(
			" (SELECT CONCAT('[',GROUP_CONCAT(CONCAT('{\"action\":\"', IF(tr.SourceState=tr.TargetState,tr.Caption,st.Caption), '\",\"author\":\"', u.Caption, '\",\"date\":\"', o.RecordCreated, '\",\"author_id\":\"', u.cms_UserId, '\"}') ORDER BY o.RecordCreated DESC SEPARATOR ','),']')
    		     FROM pm_StateObject o, pm_Transition tr, pm_State st, cms_User u
                WHERE o.ObjectId = t.WikiPageId
                  AND o.ObjectClass = '".get_class($this->getObject())."'
                  AND o.Transition = tr.pm_TransitionId
                  AND st.pm_StateId = tr.TargetState
                  AND u.cms_UserId = o.Author) Workflow "
		);
 	}
}
