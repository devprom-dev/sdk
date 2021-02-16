<?php

class StateBaseModelPersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array('Actions', 'Attributes', 'Transitions');
    }

    function getSelectColumns( $alias )
 	{
 		return array(
            " ( SELECT GROUP_CONCAT(CAST(a.pm_StateActionId AS CHAR) ORDER BY a.OrderNum) ".
            "	  FROM pm_StateAction a WHERE a.State = ".$this->getPK($alias)." ) Actions ",

            " ( SELECT GROUP_CONCAT(CAST(a.pm_StateAttributeId AS CHAR) ORDER BY a.OrderNum) ".
            "	  FROM pm_StateAttribute a WHERE a.State = ".$this->getPK($alias)." ) Attributes ",

            " ( SELECT GROUP_CONCAT(CAST(a.pm_TransitionId AS CHAR) ORDER BY a.OrderNum) ".
            "	  FROM pm_Transition a WHERE a.SourceState = ".$this->getPK($alias)." ) Transitions "
        );
 	}
}