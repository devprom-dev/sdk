<?php

class AttributePermissionEntityRegistry extends ObjectRegistrySQL
{
    private $items = array();

    public function add( $entityId, $referenceName = '', $displayName = '' )
    {
        $entity = getFactory()->getObject($entityId);
        $this->items[] = array (
            'entityId' => $entityId,
            'ReferenceName' => $referenceName == '' ? $entity->getEntityRefName() : $referenceName,
            'Caption' => $displayName == '' ? $entity->getDisplayName() : $displayName
        );
    }

    function createSQLIterator( $sql ) {
        foreach( getSession()->getBuilders('AttributePermissionEntityBuilder') as $builder ) {
            $builder->build($this);
        }
        return $this->createIterator($this->items);
    }
}