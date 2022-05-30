<?php
include "SnapshotIterator.php";
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
	
	function createIterator() {
		return new SnapshotIterator( $this );
	}
	
	function getDisplayName()
	{
		return translate('Версия');
	}

    function getDefaultAttributeValue( $attr )
    {
        switch( $attr )
        {
            case 'SystemUser':
                return getSession()->getUserIt()->getId();
            default:
                return parent::getDefaultAttributeValue( $attr );
        }
    }

	/*
	 * makes a snapshot with caption $caption of $items of class $classname,
	 * adds values with reference names in $attributes array 
	 */
	function freeze( $snapshot_id, $anchor, $items, $attributes )
	{
		// get records to be snapshot items
	 	if ( $anchor instanceof WikiPage && count($items) == 1 )
 		{
 			$anchor_it = $anchor->getRegistry()->Query( array (
 					new ParentTransitiveFilter($items)
 			));
 		}
 		else
 		{
			$anchor_it = $anchor->getExact($items);
 		}
		
		// append items into snapshot
		$snapshotitem = getFactory()->getObject('cms_SnapshotItem');
		$itemvalue = getFactory()->getObject('cms_SnapshotItemValue');
		
		while ( !$anchor_it->end() )
		{
			$item_id = $snapshotitem->add_parms( array ( 
					'Snapshot' => $snapshot_id,
					'ObjectId' => $anchor_it->getId(),
					'DataHash' => $anchor_it->get('DataHash'),
					'ObjectClass' => get_class($anchor)
			));

			// freeze values of each item
			foreach ( $attributes as $attribute )
			{
			    $type = $anchor->getAttributeType($attribute);
			    switch ( $type ) {
                    case 'wysiwyg':
                        $editor = WikiEditorBuilder::build();
                        $parser = $editor->getHtmlParser();
                        $parser->setObjectIt( $anchor_it->copy() );
                        $value = $parser->parse( $anchor_it->getHtmlDecoded($attribute) );
                        break;
                    default:
                        $value = $anchor_it->getHtmlDecoded($attribute);
                        break;
                }

				$itemvalue->add_parms( array (
                    'SnapshotItem' => $item_id,
                    'Caption' => $anchor->getAttributeUserName($attribute),
                    'ReferenceName' => $attribute,
                    'Value' => $value
				));
			}
			
			$anchor_it->moveNext();
		}
		
		return $snapshot_id;
	}
	
	function getPage()
	{
		return getSession()->getApplicationUrl($this).'versioning/revisions?';
	}
	
	function getMakePage( $anchor_it, $iterator, $list_name = '', $url = '' )
	{
		$items = \TextUtils::buildIds($iterator->idsToArray());
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