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
 		if ( $attr == 'Content' )
 		{
 			switch ( $this->get('ClassName') )
 			{
 				case 'blogpost':
 				    
 					$object_it = $this->getObjectIt();
 					
 					$content = parent::get('Content');
 					
 					if ( strpos( $content, translate('Содержание') ) !== false )
 					{
 						return '';
 					}
 					else
 					{
 						return parent::get( $attr );
 					}
	 			
 				default:
 				    
		 			$value = parent::get( $attr );
		 			
			 		if ( !$this->object->canreadcapacity )
			 		{
			 			$value = str_replace(translate('Фактическая'), 'fact', $value);
			 			$value = preg_replace('/fact.+\n/mi', '', $value);
			 		}	
			 		
			 		return $value;		
 			}
 		}
 		
 		return parent::get( $attr );
 	}
 	
 	function getChangeKind()
 	{
		switch ($this->get('ChangeKind')) 
		{
			case 'added': 
				$change_kind = translate('добавлено'); 
				break;
				
			case 'modified': 
				$change_kind = translate('изменено'); 
				break;

			case 'deleted': 
				$change_kind = translate('удалено'); 
				break;
		}
	
		return $change_kind;
 	}
 	
 	function getImage()
 	{
		switch ($this->get('ChangeKind')) 
		{
			case 'added': 
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
 		global $model_factory;

 		$class_name = $model_factory->getClass( $this->get('ClassName') );
		
		if ( $class_name == 'metaobject' ) $class_name = $model_factory->getClass( $this->get('EntityRefName') );
 		
		if ( $class_name == '' || !class_exists($class_name, false) ) return $this->object->getEmptyIterator();
		
		$object = $model_factory->getObject($class_name);
		
		$object->setVpdContext( $this );

		return $this->get('ObjectId') != '' 
            ? $object->getExact($this->get('ObjectId')) 
		    : $object->getEmptyIterator();
 	}
}
