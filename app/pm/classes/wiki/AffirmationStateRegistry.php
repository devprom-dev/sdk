<?php

class AffirmationStateRegistry extends ObjectRegistrySQL
{
    public function getQueryClause()
    {
        return "(
            SELECT 'myturn' entityId, '".text(2946)."' Caption,
                   'myturn' ReferenceName, 1 OrderNum
             UNION  
            SELECT 'ready' entityId, '".text(2947)."' Caption,
                   'ready' ReferenceName, 2
        )";
    }
}