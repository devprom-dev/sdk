<?php

class FormFieldList extends DictionaryItemsList
{
    function extendModel()
    {
        parent::extendModel();
    }

    function drawCell($object_it, $attr)
    {
        switch( $attr ) {
            case 'ReferenceName':
                echo getFactory()->getObject($object_it->get('Entity'))
                        ->getAttributeUserName($object_it->get($attr));
                break;
            default:
                parent::drawCell($object_it, $attr);
        }
    }

    function drawRefCell($entity_it, $object_it, $attr)
    {
        switch( $attr ) {
            case 'State':
                if ( $object_it->get($attr) == '' ) {
                    $entity_it = $object_it->getRef('Transition')->getRef('SourceState');
                }
                echo $entity_it->getDisplayName();
                break;
            default:
                parent::drawRefCell($entity_it, $object_it, $attr);
        }
    }
}
