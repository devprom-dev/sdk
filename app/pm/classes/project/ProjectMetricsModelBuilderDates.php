<?php
include_once SERVER_ROOT_PATH."pm/classes/project/ProjectMetricsModelBuilder.php";
include "persisters/ProjectMetricDatesPersister.php";

class ProjectMetricsModelBuilderDates extends ProjectMetricsModelBuilder
{
	function build( Metaobject $object )
	{
        $object->addAttribute('EstimatedFinishDate', 'DATE', '', false, false, text(2543));
        $object->addAttributeGroup('EstimatedFinishDate', 'metrics');

        $object->addAttribute('ProjectVelocity', 'FLOAT', '', false, false, text(2284));
        $object->addAttributeGroup('ProjectVelocity', 'metrics');

        $object->addPersister( new ProjectMetricDatesPersister() );
	}
}