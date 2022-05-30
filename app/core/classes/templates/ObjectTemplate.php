<?php
include "ObjectTemplateIterator.php";
include "ObjectTemplateRegistry.php";
include "persisters/ObjectTemplatePersister.php";
include "ObjectTemplateItem.php";
include "ObjectTemplateItemValue.php";

abstract class ObjectTemplate extends Metaobject
{
	public function __construct()
	{
		parent::__construct('cms_Snapshot', new ObjectTemplateRegistry($this));
		$this->setSortDefault( new SortAttributeClause('Caption') );
		$this->setAttributeVisible('ListName', false);
		$this->setAttributeVisible('SystemUser', false);
        $this->setAttributeVisible('Recurring', true);
	}
	
	abstract public function getTypeName();
	abstract public function getListName();
	abstract public function getAttributesTemplated();
	
	public function createIterator() {
		return new ObjectTemplateIterator($this);
	}
	
	public function getDefaultAttributeValue( $attr )
	{
		switch ( $attr )
		{
		    case 'SystemUser':
		    	return getSession()->getUserIt()->getId();
		    default:
		    	return parent::getDefaultAttributeValue( $attr );
		}
	}
	
	public function add_parms( $parms )
	{
		$snapshot_id = parent::add_parms( $parms );
		if ( $snapshot_id < 0 ) return $snapshot_id;
		
		// append items into snapshot
        if ( $parms['items'] != '' ) {
            $object_it = getFactory()->getObject($this->getTypeName())->getExact($parms['items']);
            if ( $object_it->getId() < 1 ) throw new Exception('Cant find object to be templated');

            $this->persistSnapshot($snapshot_id, $object_it);
        }

		return $snapshot_id;
	}

	public function persistSnapshot( $snapshot_id, $object_it )
    {
        $snapshotitem = new ObjectTemplateItem();
        $itemvalue = new ObjectTemplateItemValue();

        $snapshotIt = $snapshotitem->getRegistry()->Merge(
            array (
                'Snapshot' => $snapshot_id,
                'ObjectId' => '0',
                'ObjectClass' => get_class($object_it->object)
            )
        );

        $registry = $itemvalue->getRegistry();
        foreach ( $this->getAttributesTemplated() as $attribute ) {
            $registry->Merge(
                array (
                    'SnapshotItem' => $snapshotIt->getId(),
                    'Caption' => $object_it->object->getAttributeUserName($attribute),
                    'ReferenceName' => $attribute,
                    'Value' => $object_it->getHtmlDecoded($attribute)
                ),
                array('SnapshotItem', 'ReferenceName')
            );
        }
    }

    function processRecurringAction( $objectIt, $logger )
    {
        $templateIt = $this->getRegistry()->Query(
            array(
                new FilterInPredicate($objectIt->getId()),
                new ObjectTemplatePersister()
            )
        );
        if ( $templateIt->getId() == '' ) return false;

        $parms = array();
        foreach( $this->getAttributesTemplated() as $attribute ) {
            $parms[$attribute] = $templateIt->getHtmlDecoded($attribute);
        }

        $entity = getFactory()->getObject($this->getTypeName());
        getFactory()->createEntity($entity, $parms);

        return true;
    }
}