<?php

class CommentStateRegistry extends ObjectRegistrySQL
{
    public function getQueryClause()
    {
        return "(
            SELECT 'new' entityId, '".text(3000)."' Caption,
                   'new' ReferenceName, 1 OrderNum
             UNION  
            SELECT 'mine' entityId, '".text(3003)."' Caption,
                   'mine' ReferenceName, 1 OrderNum
             UNION  
            SELECT 'open' entityId, '".text(3001)."' Caption,
                   'open' ReferenceName, 2
             UNION  
            SELECT 'closed' entityId, '".text(3002)."' Caption,
                   'closed' ReferenceName, 3
        )";
    }
}