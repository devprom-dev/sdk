<?php

class ObjectRegistryMemory extends ObjectRegistryArray
{
    function getAll()
    {
        global $memoryDb;
        $data = $memoryDb[get_class($this->getObject())];
        if ( !is_array($data) ) $data = array();
        return $this->getObject()->createCachedIterator(
            array_values($data)
        );
    }

    function Store(OrderedIterator $object_it, array $data)
    {
        global $memoryDb;
        $memoryDb[get_class($this->getObject())][$object_it->getId()] = array_merge(
            $object_it->getData(),
            $data
        );
        return 1;
    }

    function Create(array $data)
    {
        global $memoryDb;
        $id = count($memoryDb[get_class($this->getObject())]) + 1;
        $memoryDb[get_class($this->getObject())][$id] = $data;
        return $this->getObject()->createCachedIterator(
            array(
                array_merge(
                    array (
                        $this->getObject()->getIdAttribute() => $id
                    ),
                    $data
                )
            )
        );
    }

    public function Delete( OrderedIterator $object_it )
    {
        global $memoryDb;
        unset($memoryDb[get_class($this->getObject())][$object_it->getId()]);
   }
}