<?php

class DocumentStatePersister extends ObjectSQLPersister
{
    function getSelectColumns( $alias )
    {
        $columns = array();
        
        $class_name = strtolower(get_class($this->getObject()));
        
        $columns[] = " (SELECT d.State FROM WikiPage d, pm_State s WHERE d.DocumentId = t.WikiPageId AND s.ReferenceName = d.State AND s.ObjectClass = '".$class_name."' AND s.VPD = t.VPD ORDER BY s.OrderNum LIMIT 1) State ";

        $columns[] = " (SELECT s.Caption FROM WikiPage d, pm_State s WHERE d.DocumentId = t.WikiPageId AND s.ReferenceName = d.State AND s.ObjectClass = '".$class_name."' AND s.VPD = t.VPD ORDER BY s.OrderNum LIMIT 1) StateName ";
        
        return $columns;
    }
}
