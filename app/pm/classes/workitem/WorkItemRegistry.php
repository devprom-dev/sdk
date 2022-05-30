<?php
include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectUIDPersister.php";
include "WorkItemRegistryBuilder.php";
include "WorkItemRegistryTaskBuilder.php";
include "WorkItemRegistryIssueBuilder.php";

class WorkItemRegistry extends ObjectRegistrySQL
{
    private $descriptionIncluded = true;
    private $sqls = array();

    function getPersisters() {
        $result = array(
            new EntityProjectPersister()
        );
        return $result;
    }

    public function setDescriptionIncluded( $flag = true ) {
        $this->descriptionIncluded = $flag;
    }

    public function getDescriptionIncluded() {
        return $this->descriptionIncluded;
    }

    function mergeSQL( $sql ) {
        $this->sqls[] = $sql;
    }

 	function getQueryClause(array $parms)
 	{
        $filters = $this->extractPredicates($parms);

        $builders = array(
            new WorkItemRegistryTaskBuilder(),
            new WorkItemRegistryIssueBuilder()
        );
        foreach( $builders as $builder ) {
            $builder->build($this, $filters);
        }
        foreach( getSession()->getBuilders('WorkItemRegistryBuilder') as $builder ) {
            $builder->build($this, $filters);
        }

        return " (".join('UNION', $this->sqls).") ";
 	}

    function getInnerFilterPredicate( $object, $filters )
    {
        $predicate = '';
        foreach( $filters as $filter ) {
            $filter->setObject($object);
            $filter->setAlias('t');
            $predicate .= $filter->getPredicate();
        }
        return $predicate;
    }
}