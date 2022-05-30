<?php

class ModelFactorySOAP extends ModelFactoryProject
{
    function getObject($class_name)
    {
        $object = parent::getObject($class_name);
        if ( $object->getEntityRefName() == 'WikiPage' ) {
            $object->setRegistry(new WikiPageRegistryContent());
        }
        return $object;
    }
}
