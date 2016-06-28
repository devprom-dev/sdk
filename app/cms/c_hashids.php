<?php

 class HashIdsIterator extends OrderedIterator
 {
 	function getIds()
 	{
 		$ids = preg_split('/-/', $this->get('Ids'));
 		
 		if ( count($ids) < 1 )
 		{
 			$ids[0] = 0;
 		}
 		
 		return $ids;
 	}
 }

 class HashIds extends Metaobject
 {
 	function HashIds() 
 	{
 		parent::Metaobject('cms_IdsHash');
 	}
 	
 	function createIterator()
 	{
 		return new HashIdsIterator($this);
 	}
 	
 	function getHash( $iterator )
 	{
 		$ids = $iterator->idsToArray();
 		$ids = join('-',$ids);

 		$hash = md5( $iterator->object->getClassName().$ids ); 
 		
 		if ( $this->getByRefArrayCount( array('HashKey' => $hash) ) < 1 )
 		{
 			$this->add_parms( array('HashKey' => $hash, 'Ids' => $ids) );
 		}
 		
 		return $hash;
 	}
 	
 	function getHashIds( $hash )
 	{
 		$it = $this->getByRef('HashKey', $hash);
 		
 		if ( $it->count() < 1 )
 		{
 			return array(0);
 		}
 		else
 		{
 			return $it->getIds();
 		}
 	}
 }
