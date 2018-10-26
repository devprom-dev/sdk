<?php

namespace Devprom\ProjectBundle\Service\Model;
use Devprom\ProjectBundle\Service\Model\ModelService; 

class ModelServiceTesting extends ModelService
{
	function getObject( $entity_name )
	{
		$object = parent::getObject($entity_name);

		if ( $object instanceof \TestExecution ) {
            foreach( array('OrderNum', 'TestScenario') as $field ) {
                $object->addAttributeGroup($field, 'system');
            }
        }
        else {
            foreach( array('OrderNum', 'Version', 'Environment') as $field ) {
                $object->addAttributeGroup($field, 'system');
            }
        }

		return $object;
	}
}