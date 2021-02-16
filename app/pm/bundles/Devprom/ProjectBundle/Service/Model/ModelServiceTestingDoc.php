<?php

namespace Devprom\ProjectBundle\Service\Model;
use Devprom\ProjectBundle\Service\Model\ModelService; 

class ModelServiceTestingDoc extends ModelService
{
	function getObject( $entity_name )
	{
		$object = parent::getObject($entity_name);

		if ( $object instanceof \TestScenario ) {
            $object->setAttributeDefault('PageType', $object->getScenarioTypeIt()->getId());
            $object->setAttributeRequired('PageType', true);

            foreach( array('DocumentVersion') as $field ) {
                $object->addAttributeGroup($field, 'system');
            }
        }
        else {
            foreach( array('DocumentId', 'DocumentVersion', 'ParentPage') as $field ) {
                $object->addAttributeGroup($field, 'system');
            }
        }

		return $object;
	}
}