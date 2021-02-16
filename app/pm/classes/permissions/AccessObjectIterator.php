<?php

class AccessObjectIterator extends OrderedIterator
{
 	var $hash;

 	function getAccess( $role_id, $object_it )
 	{
 		$access = $this->getHashedAccess( $role_id, $object_it );

		switch ( $access )
		{
			case 'view':
				return 1;
				
			case 'none':
				return 0;
				
			default:
				return -1;
		}
 	}

	function setRowset( $rowset )
	{
		parent::setRowset( $rowset );
		
		while ( !$this->end() )
		{
			$this->hash[md5($this->get('ProjectRole').strtolower($this->get('ObjectClass')).
				$this->get('ObjectId'))] = $this->get('AccessType');
				
			$this->moveNext();
		}
		
		$this->moveFirst();
	}
 	
	function getHashedAccess( $role_id, $object_it )
 	{ 
 		return $this->hash[md5($role_id.
 			strtolower(get_class($object_it->object)).$object_it->getId())];
 	}

	function getObjectIt()
	{
		$class_name = getFactory()->getClass( $this->get('ObjectClass') );
		if ( !class_exists($class_name) ) $this->object->getEmptyIterator();
		
		$object = getFactory()->getObject($this->get('ObjectClass'));
        $object->setRegistry(new ObjectRegistrySQL($object));
		return $object->getExact( $this->get('ObjectId') );
	}
	
 	function getDisplayName()
 	{
 		$role_it = $this->getRef('ProjectRole');
 		$object_it = $this->getObjectIt();
 		
 		if ( $object_it->count() > 0 )
 		{
	 		$uid = new ObjectUID;
			$caption = $uid->getObjectUid($object_it).' '.$object_it->getDisplayName();
 		}
 		
 		$caption .= ' ('.$role_it->getDisplayName().')';

 		switch ( $this->get('AccessType') )
 		{
 			case 'none':
 				$caption .= ' ['.translate('Нет').']';
 				break;

 			case 'view':
 				$caption .= ' ['.translate('Просмотр').']';
 				break;

 			case 'modify':
 				$caption .= ' ['.text(2811).']';
 				break;

            case 'cru':
                $caption .= ' ['.text(2812).']';
                break;
 		}
 		
 		return $caption;
 	}
 	
 	function getViewUrl()
 	{
		$object_it = $this->getObjectIt();
 		$info = getFactory()->getObject('Module')
 					->getExact('permissions/settings')->buildMenuItem(
 							'class='.$object_it->object->getClassName().'&id='.$object_it->getId());
 		return $info['url'];
 	}
} 
