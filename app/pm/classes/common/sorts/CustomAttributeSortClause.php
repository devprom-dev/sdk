<?php

class CustomAttributeSortClause extends SortAttributeClause
{
 	function clause()
 	{
        $attr_it = getFactory()->getObject('pm_CustomAttribute')->getRegistry()->Query(
            array(
                new FilterAttributePredicate('EntityReferenceName', strtolower(get_class($this->getObject()))),
                new FilterAttributePredicate('ReferenceName', $this->getAttributeName()),
                new FilterBaseVpdPredicate()
            )
        );
        if ( $attr_it->getId() == '' ) return "";

        return
            "(SELECT cav.".$attr_it->getRef('AttributeType')->getValueColumn()." FROM pm_AttributeValue cav ".
            "  WHERE cav.ObjectId = ".$this->getAlias().".".$this->getObject()->getIdAttribute().
            "    AND cav.VPD = ".$this->getAlias().".VPD ".
            "    AND cav.CustomAttribute = ".$attr_it->getId()." LIMIT 1) ".$this->getSortType();
 	}
}