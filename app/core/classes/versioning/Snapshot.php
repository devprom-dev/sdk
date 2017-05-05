<?php

include "SnapshotIterator.php";
include "predicates/SnapshotObjectPredicate.php";
include "predicates/SnapshotBeforeDatePredicate.php";
include "predicates/SnapshotsByObjectPredicate.php";
include "persisters/SnapshotItemValuePersister.php";

class Snapshot extends Metaobject
{
 	function Snapshot() 
 	{
		parent::Metaobject('cms_Snapshot');
 		$this->defaultsort = 'RecordCreated DESC';
	}
	
	function createIterator() 
	{
		return new SnapshotIterator( $this );
	}
	
	function getDisplayName()
	{
		return translate('Версия');
	}
	
	/*
	 * makes a snapshot with caption $caption of $items of class $classname,
	 * adds values with reference names in $attributes array 
	 */
	function freeze( $snapshot_id, $classname, $items, $attributes )
	{
		global $model_factory;
		
		// get records to be snapshot items
		$anchor = $model_factory->getObject($classname);
		
	 	if ( $anchor instanceof WikiPage && count($items) == 1 )
 		{
 			$anchor_it = $anchor->getRegistry()->Query( array (
 					new WikiRootTransitiveFilter($items)
 			));
 		}
 		else
 		{
			$anchor_it = $anchor->getExact($items);
 		}
		
		// append items into snapshot
		$snapshotitem = $model_factory->getObject('cms_SnapshotItem');

		$itemvalue = $model_factory->getObject('cms_SnapshotItemValue');
		
		while ( !$anchor_it->end() )
		{
			$item_id = $snapshotitem->add_parms( array ( 
					'Snapshot' => $snapshot_id,
					'ObjectId' => $anchor_it->getId(),
					'ObjectClass' => $classname 
			));

			// freeze values of each item
			foreach ( $attributes as $attribute )
			{
				$itemvalue->add_parms( array ( 
						'SnapshotItem' => $item_id,
						'Caption' => $anchor->getAttributeUserName($attribute),
						'ReferenceName' => $attribute,
						'Value' => $anchor_it->getHtmlDecoded($attribute) 
				));
			}
			
			$anchor_it->moveNext();
		}
		
		return $snapshot_id;
	}
	
	function getPage()
	{
		return getSession()->getApplicationUrl().'versioning/revisions?';
	}
	
	function getMakePage( $anchor_it, $iterator, $list_name = '', $url = '' )
	{
		$items = $iterator->count() > 1 ? getFactory()->getObject('HashIds')->getHash( $iterator ) : $iterator->getId(); 
			
		return $this->getPageName().
 			'&class='.get_class($iterator->object).'&ObjectId='.$anchor_it->getId().'&ObjectClass='.get_class($anchor_it->object).
 			'&items='.$items.'&ListName='.$list_name.'&redirect='.($url != '' ? $url : urlencode($_SERVER['REQUEST_URI']));
	}
	
	function add_parms( $parms )
	{
		if ( $parms['Type'] == 'branch' )
		{
			// check there is only one version of the object with 'branch' type
			$object_it = $this->getRegistry()->Query( 
					array (
							new FilterAttributePredicate('ObjectClass', $parms['ObjectClass']),  
							new FilterAttributePredicate('ObjectId', $parms['ObjectId']),
							new FilterAttributePredicate('Type', 'branch')
					)
			);
			
			if ( $object_it->count() > 0 )
			{
				throw new Exception('Only one snapshot of "branch" type is allowed');
			}
		}

        $snapshotId = parent::add_parms($parms);

        if ( $snapshotId > 0 && $parms['Type'] == 'branch' ) {
            DAL::Instance()->Query(
                "UPDATE WikiPage SET DocumentVersion = (SELECT s.Caption FROM cms_Snapshot s WHERE s.cms_SnapshotId = ".$snapshotId.") WHERE DocumentId = ".$parms['ObjectId']
            );
        }

        return $snapshotId;
	}

	function modify_parms($id, $parms)
    {
        $snapshotIt = $this->getExact($id);
        $result = parent::modify_parms($id, $parms);

        if ( $snapshotIt->get('Type') == 'branch' ) {
            DAL::Instance()->Query(
                "UPDATE WikiPage SET DocumentVersion = (SELECT s.Caption FROM cms_Snapshot s WHERE s.cms_SnapshotId = ".$id.") WHERE DocumentId = ".$snapshotIt->get('ObjectId')
            );
        }

        return $result;
    }
}