<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/persisters/StateDurationPersister.php";

class FormLinkedEmbedded extends PMFormEmbedded
{
	private $anchor_it = null;
	
	public function setAnchorIt( $anchor_it )
	{
		$this->anchor_it = $anchor_it;
	}
	
	public function getTargetIt( $object_it )
	{
		return is_object($this->anchor_it) && $this->anchor_it->getId() == $object_it->get('TargetRequest') 
	        	? $object_it->getRef('SourceRequest') : $object_it->getRef('TargetRequest');
	}
	
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'TargetRequest':
 			case 'LinkType':
 				return true;

 			default:
 				return false;
 		}
 	}
 	
 	function getItemDisplayName( $object_it )
 	{
 		$attribute = 
 			is_object($this->anchor_it) && $this->anchor_it->getId() == $object_it->get('TargetRequest')
 				?  'SourceRequest' : 'TargetRequest';

		$target_it = $object_it->object->getAttributeObject($attribute)->getRegistry()->Query(
			array (
					new FilterInPredicate($object_it->get($attribute)),
					new StateDurationPersister()
			)
		);

 		$link_type_it = $object_it->getRef('LinkType');

 		$type_title = 
 			is_object($this->anchor_it) && $this->anchor_it->getId() == $object_it->get('TargetRequest')
 				?  $link_type_it->get('BackwardCaption') : $link_type_it->get('Caption');
		
	    $uid = new ObjectUID;
	    
 		return translate($type_title).': '.$uid->getUidWithCaption( $target_it );
 	}
 	
	function createField( $attr )
	{
		$object = $this->getAttributeObject( $attr );

		switch ( $attr )
		{
			case 'TargetRequest':
				$field = new FieldAutoCompleteObject( getFactory()->getObject('Request') );
				$field->setCrossProject();
				return $field;

			case 'LinkType':
				$field = new FieldDictionary( $object );
				$field->translateOptions();
				return $field;
				
			default:
				return parent::createField( $attr );			
		}
	}
	
	function getActions( $object_it, $item )
	{
	    $target_it = $this->getTargetIt( $object_it );

	    $actions = array ( array (
	            'name' => translate('Перейти'),
	            'url' => $target_it->getViewUrl()     
	    ));
	
	    $actions[] = array();
	
	    return array_merge($actions, parent::getActions( $object_it, $item ));
	}
}