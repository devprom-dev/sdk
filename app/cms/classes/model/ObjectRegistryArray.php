<?php
include_once "ObjectRegistrySQL.php";

class ObjectRegistryArray extends ObjectRegistrySQL
{
    function Query( $parms = array() )
    {
        $rowset = $this->getAll()->getRowset();

        foreach( $parms as $parm )
        {
            if ( $parm instanceof FilterInPredicate )
            {
                $id_key = $this->getObject()->getIdAttribute();
                $id_value = array_filter(
                    !is_array($parm->getValue()) ? preg_split('/,/', $parm->getValue()) : $parm->getValue(),
                    function($value) {
                        return $value != '';
                    }
                );
                if ( count($id_value) > 0 ) {
                    $rowset = array_filter( $rowset, function(&$row) use($id_key, $id_value) {
                        return in_array($row[$id_key], $id_value);
                    });
                }
                else {
                    $rowset = array();
                }
            }
            if ( $parm instanceof FilterAttributePredicate )
            {
                $id_key = $parm->getAttribute();

                if ( !in_array($parm->getValue(), array('all','')) ) {
                    $id_value = preg_split('/,/', $parm->getValue());
                    $rowset = array_filter($rowset, function (&$row) use ($id_key, $id_value) {
                        return in_array($row[$id_key], $id_value);
                    });
                }
            }
            if ( $parm instanceof FilterSearchAttributesPredicate )
            {
                $id_key = 'Caption';
                $id_value = $parm->getValue();

                if ( $id_value != '' ) {
                    $rowset = array_filter($rowset, function (&$row) use ($id_key, $id_value) {
                        return mb_stripos($row[$id_key], $id_value) !== false;
                    });
                }
                else {
                    $rowset = array();
                }
            }
        }
        return $this->createIterator(array_values($rowset));
    }

    function Store(OrderedIterator $object_it, array $data)
    {
        return $this->getObject()->getEmptyIterator();
    }

    function Create(array $data)
    {
    }
}