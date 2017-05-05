<?php

class FieldState extends FieldDictionary
{
    function __construct( $object )
    {
        parent::__construct($object);
        $this->setNullOption(false);
    }

    function getOptions()
    {
        $options = array();

        $entity_it = $this->getObject()->getAll();
        while( !$entity_it->end() )
        {
            $options[] = array (
                'value' => $entity_it->get('ReferenceName'),
                'referenceName' => $entity_it->get('ReferenceName'),
                'caption' => $entity_it->getDisplayName(),
                'disabled' => false
            );
            $entity_it->moveNext();
        }

        return $options;
    }
}
