<?php
include_once SERVER_ROOT_PATH."pm/classes/project/ProjectMetricsModelBuilder.php";
include "persisters/ProjectMetricDatesPersister.php";

class ProjectMetricsModelBuilderDates extends ProjectMetricsModelBuilder
{
	function build( Metaobject $object )
	{
        $object->addAttribute('EstimatedFinishDate', 'INTEGER', '', false);
        $object->addAttributeGroup('EstimatedFinishDate', 'metrics');
        $object->addPersister( new ProjectMetricDatesPersister() );
	}
}