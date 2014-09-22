<?php

include "ObjectTemplateIterator.php";
include "ObjectTemplateRegistry.php";
include "persisters/ObjectTemplatePersister.php";

include_once "ObjectTemplateItem.php";
include_once "ObjectTemplateItemValue.php";

abstract class ObjectTemplate extends Metaobject
{
	public function __construct()
	{
		parent::__construct('cms_Snapshot', new ObjectTemplateRegistry($this));
		
		$this->setAttributeVisible('ListName', false);
		
		$this->setAttributeVisible('SystemUser', false);
	}
	
	abstract public function getTypeName();
	
	abstract public function getListName();
	
	abstract public function getAttributesTemplated();
	
	public function createIterator()
	{
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
		global $model_factory;
		
		if ( $parms['items'] == '' ) throw new Exception('An object to be templated is required');

		$object = $model_factory->getObject($this->getTypeName());
		
		$object_it = $object->getExact($parms['items']);
		
		if ( $object_it->getId() < 1 ) throw new Exception('Cant find object to be templated');

		$snapshot_id = parent::add_parms( $parms );
		
		if ( $snapshot_id < 0 ) return $snapshot_id;
		
		// append items into snapshot
		
		$snapshotitem = new ObjectTemplateItem();
		
		$itemvalue = new ObjectTemplateItemValue();
		
		$item_id = $snapshotitem->add_parms(
			array ( 'Snapshot' => $snapshot_id,
					'ObjectId' => '0',
					'ObjectClass' => get_class($object) )
		);

		// freeze values of each item
		foreach ( $this->getAttributesTemplated() as $attribute )
		{
			$itemvalue->add_parms(
				array ( 'SnapshotItem' => $item_id,
					    'Caption' => $object->getAttributeUserName($attribute),
					    'ReferenceName' => $attribute,
					    'Value' => $object_it->getHtmlDecoded($attribute) )
			);
		}
		
		return $snapshot_id;
	}
}