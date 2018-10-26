<?php

class MetricRegistry extends ObjectRegistrySQL
{
	function createSQLIterator($sql)
	{
	    $data = array();

	    $projectMetric = getFactory()->getObject('ProjectMetric');
        foreach( getSession()->getBuilders('ProjectMetricsModelBuilder') as $builder ) {
            $builder->build($projectMetric);
        }

	    foreach( $projectMetric->getAttributesByGroup('metrics') as $attribute ) {
	        $title = $projectMetric->getAttributeDescription($attribute);
	        if ( $title == '' ) continue;
            $data[] = array (
                'entityId' => $attribute,
                'Caption' => $title
            );
        }

        return $this->createIterator($data);
	}
}