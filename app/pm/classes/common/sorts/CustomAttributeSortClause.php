<?php

class CustomAttributeSortClause extends SortAttributeClause
{
 	function clause()
 	{
        $attr_it = getFactory()->getObject('pm_CustomAttribute')->getRegistry()->Query(
            array(
                new FilterAttributePredicate('EntityReferenceName', strtolower(get_class($this->getObject()))),
                new FilterAttributePredicate('ReferenceName', $this->getAttributeName())
            )
        );
        if ( $attr_it->getId() == '' ) return "";

        $column = "cav.".$attr_it->getRef('AttributeType')->getValueColumn();
        if ( in_array($this->getObject()->getAttributeType($this->getAttributeName()), array('integer','float')) ) {
            $column = "CAST({$column} as SIGNED INTEGER)";
        }

        return
            "(SELECT {$column} FROM pm_AttributeValue cav ".
            "  WHERE cav.ObjectId = ".$this->getAlias().".".$this->getObject()->getIdAttribute().
            "    AND cav.CustomAttribute IN (".join(',',$attr_it->idsToArray()).") LIMIT 1) {$this->getSortType()}";
 	}
}