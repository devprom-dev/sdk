<?php
include_once SERVER_ROOT_PATH."core/classes/ResourceBuilder.php";

class ResourceBuilderTerminology extends ResourceBuilder
{
    public function build( ResourceRegistry $object )
    {
    	$resource = new Resource(new ObjectRegistrySQL());
    	$resource_it = $resource->getAll();
    	while( !$resource_it->end() )
    	{
    		$object->addText($resource_it->get('ResourceKey'), $resource_it->get('ResourceValue'));
    		$resource_it->moveNext();
    	}
    }
}