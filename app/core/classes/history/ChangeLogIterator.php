<?php

class ChangeLogIterator extends OrderedIterator
{
 	var $uid;
 	
 	function ChangeLogIterator( $object )
 	{
 		parent::OrderedIterator( $object );
 		
 		$this->uid = new ObjectUID;
 	}
 	
 	function get( $attr )
 	{
 		if ( $attr == 'Content' ) {
 			switch ( $this->get('ClassName') ) {
 				default:
		 			$value = parent::get( $attr );
			 		if ( !$this->object->canreadcapacity ) {
			 			$value = str_replace(translate('Фактическая'), 'fact', $value);
			 			$value = preg_replace('/fact.+\n/mi', '', $value);
			 		}	
			 		return $value;
 			}
 		}
 		return parent::get( $attr );
 	}
 	
 	function getIcon()
 	{
		switch ($this->get('ChangeKind')) 
		{
			case 'added':
            case 'submitted':
                $change_kind = 'icon-plus-sign';
				break;
				
			case 'modified': 
				$change_kind = 'icon-edit'; 
				break;

			case 'deleted': 
				$change_kind = 'icon-minus-sign'; 
				break;
				
			case 'commented': 
				$change_kind = 'icon-comment'; 
				break;

			case 'comment_modified': 
				$change_kind = 'icon-comment'; 
				break;

			case 'comment_deleted': 
				$change_kind = 'icon-comment'; 
				break;
				
			default:
				$change_kind = 'icon-edit'; 
				break;
		}
		return $change_kind;
 	}
 	
 	function getObjectIt()
 	{
 		$class_name = getFactory()->getClass( $this->get('ClassName') );
		if ( $class_name == 'metaobject' ) $class_name = getFactory()->getClass( $this->get('EntityRefName') );

		if ( $class_name == '' || !class_exists($class_name, false) ) {
			return $this->object->getEmptyIterator();
		}
		
		$object = getFactory()->getObject($class_name);
		$object->setVpdContext( $this );

		if ( $this->get('ChangeKind') == 'deleted' ) {
			return $object->createCachedIterator(
				array (
					array (
						$object->getIdAttribute() => $this->get('ObjectId')
					)
				)
			);
		}

		return $this->get('ObjectId') != '' 
            ? $object->getExact($this->get('ObjectId')) 
		    : $object->getEmptyIterator();
 	}
}
