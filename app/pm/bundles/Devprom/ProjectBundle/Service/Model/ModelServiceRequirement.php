<?php

namespace Devprom\ProjectBundle\Service\Model;
use Devprom\ProjectBundle\Service\Model\ModelService; 

class ModelServiceRequirement extends ModelService
{
	function getObject( $entity_name )
	{
		$object = parent::getObject($entity_name);

		$registry = new \ObjectRegistrySQL();
		$registry->setPersisters($object->getPersisters());
		$object->setRegistry($registry);

		foreach( array('DocumentId', 'DocumentVersion') as $field ) {
			$object->addAttributeGroup($field, 'system');
		}

		return $object;
	}
}