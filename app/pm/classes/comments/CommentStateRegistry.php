<?php

class CommentStateRegistry extends ObjectRegistrySQL
{
    public function getQueryClause(array $parms)
    {
        return "(
            SELECT 'new' entityId, '".text(3000)."' Caption,
                   'new' ReferenceName, 1 OrderNum
             UNION  
            SELECT 'mine' entityId, '".text(3003)."' Caption,
                   'mine' ReferenceName, 2 OrderNum
             UNION  
            SELECT 'unanswered' entityId, '".text(3300)."' Caption,
                   'unanswered' ReferenceName, 3 OrderNum
             UNION  
            SELECT 'open' entityId, '".text(3001)."' Caption,
                   'open' ReferenceName, 4
             UNION  
            SELECT 'closed' entityId, '".text(3002)."' Caption,
                   'closed' ReferenceName, 5
        )";
    }
}