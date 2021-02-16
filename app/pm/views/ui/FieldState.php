<?php

class FieldState extends FieldDictionary
{
    private $instantiationAllowedOnly = false;

    function __construct( $object )
    {
        parent::__construct($object);
        $this->setNullOption(false);
    }

    function setInstantiationAllowedOnly( $value ) {
        $this->instantiationAllowedOnly = $value;
    }

    function getOptions()
    {
        $options = array();

        $entity_it = $this->getObject()->getAll();
        while( !$entity_it->end() )
        {
            if ( $this->instantiationAllowedOnly && $entity_it->get('IsNewArtifacts') != 'Y' ) {
                $entity_it->moveNext();
                continue;
            }
            $options[$entity_it->get('ReferenceName')] = array (
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
