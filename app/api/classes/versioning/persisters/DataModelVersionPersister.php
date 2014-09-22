<?php

class DataModelVersionPersister extends ObjectSQLPersister
{
    function getSelectColumns( $alias )
    {
        $columns = array();

        if ( $this->getObject() instanceof WikiPage )
        {
        	$columns[] = 
        		" ( SELECT s.cms_SnapshotId FROM cms_Snapshot s ".
        		"    WHERE s.ObjectId = t.DocumentId ".
        		"      AND s.ObjectClass = '".get_class($this->getObject())."' ".
        		"      AND s.Type = 'branch' ) Version ";
        	
        	$columns[] = 
        		" ( SELECT s.Caption FROM cms_Snapshot s ".
        		"    WHERE s.ObjectId = t.DocumentId ".
        		"      AND s.ObjectClass = '".get_class($this->getObject())."' ".
        		"      AND s.Type = 'branch' ) VersionName ";
        }
        else
        {
        	$columns[] = 
        		" ( SELECT s.cms_SnapshotId FROM cms_Snapshot s ".
        		"    WHERE s.ObjectId = ".$this->getPK($alias).
        		"      AND s.ObjectClass = '".get_class($this->getObject())."' ) Version ";
        	
        	$columns[] = 
        		" ( SELECT s.Caption FROM cms_Snapshot s ".
        		"    WHERE s.ObjectId = ".$this->getPK($alias).
        		"      AND s.ObjectClass = '".get_class($this->getObject())."' ) VersionName ";
        }

        return $columns;
    }
}
