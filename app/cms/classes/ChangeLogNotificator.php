<?php

include_once "ObjectFactoryNotificator.php";

define('CHLN_VISIBILITY_DEFAULT', 1);
define('CHLN_VISIBILITY_HIDDEN', 3);

class ChangeLogNotificator extends ObjectFactoryNotificator
{
 	var $default_visibility = CHLN_VISIBILITY_DEFAULT;

 	private $system_attributes = array();
 	
 	private $modified_attributes = array();
 	
 	function __construct()
 	{
 		parent::__construct();
 	}
 	
 	function setModifiedAttributes( $attributes )
 	{
 		$this->modified_attributes = $attributes;
 	}
 	
 	function getModifiedAttributes()
 	{
 		return $this->modified_attributes;
 	}
 	
 	function setVisibility( $visibility )
 	{
 		$this->default_visibility = $visibility;
 	}
 	
 	function add( $object_it ) 
	{
		$this->modified_attributes = array();
		
		$this->process( $object_it, 'added' );
	}

 	function modify( $prev_object_it, $object_it ) 
	{
		list($content, $this->modified_attributes) = $this->getContent( $prev_object_it, $object_it );

		$content != '' 
			? $this->process( $object_it, 'modified', $content, $this->default_visibility )
			: $this->process( $object_it, 'modified', $content, CHLN_VISIBILITY_HIDDEN );
	}

 	function delete( $object_it ) 
	{
		$this->modified_attributes = array();
		
		$this->process( $object_it, 'deleted' );
	}
	
	function getContent( $prev_object_it, $object_it )
	{
		$content = '';
		$modified_attributes = array();
		
		$attributes = $object_it->object->getAttributes();

		foreach( $attributes as $att_name => $attribute ) 
		{
			$was_value = $prev_object_it->getHtmlDecoded($att_name);
			$now_value = $object_it->getHtmlDecoded($att_name);

			if ( $object_it->object->getAttributeType($att_name) == 'wysiwyg' )
			{
				$was_value = preg_replace('/\r|\n/', '', $was_value); 
				$now_value = preg_replace('/\r|\n/', '', $now_value); 
			}
			
			if( $was_value == $now_value ) continue;
			
			$modified_attributes[] = $att_name;

			if( !$this->isAttributeVisible($att_name, $object_it, 'modify') ) continue; 
				
			if ( $object_it->object->IsReference($att_name) ) 
			{
				$now_ref = $object_it->getRef($att_name);

				$content .= translate($object_it->object->getAttributeUserName($att_name))
								.': '.$now_ref->getDisplayName().Chr(10).Chr(13);
			} 
			else 
			{
				if ( $now_value == 'Y' ) $now_value = translate('Да'); 
				if ( $now_value == 'N' ) $now_value = translate('Нет'); 

				$content .= translate($object_it->object->getAttributeUserName($att_name))
								.': '.$now_value.Chr(10).Chr(13);
			}
        }

        return array($content, $modified_attributes);
	}
	
	function process( $object_it, $kind, $content = '', $visibility = 1, $author_email = '') 
	{
		if( !$this->is_active($object_it) ) return;

		$change_log = getFactory()->getObject('ObjectChangeLog');
		
		$change_log->setVpdContext( $object_it );
		
		$class_name = strtolower(get_class($object_it->object));
		
		$parms['Caption'] = html_entity_decode( $object_it->getDisplayName(), ENT_COMPAT | ENT_HTML401, APP_ENCODING );
		$parms['ObjectId'] = $object_it->getId();
		$parms['ClassName'] = $class_name == 'metaobject' ? $object_it->object->getClassName() : $class_name;
		$parms['EntityRefName'] = $object_it->object->getEntityRefName();
		$parms['EntityName'] = translate($object_it->object->getDisplayName());
		$parms['ChangeKind'] = $kind;
		$parms['Author'] = $author_email != '' ? $author_email : getSession()->getUserIt()->get('Email');
		$parms['Content'] = $content;
		$parms['VisibilityLevel'] = $visibility;
		$parms['SystemUser'] = getSession()->getUserIt()->getId();

		$id = $change_log->add_parms($parms);
		
		$log_attribute = getFactory()->getObject('ObjectChangeLogAttribute');
		
		foreach( $this->modified_attributes as $attribute )
		{
			$log_attribute->add_parms(
					array (
							'ObjectChangeLogId' => $id,
							'Attributes' => $attribute
					)
			);
		}
	}
	
	function is_active( $object_it ) 
	{
		return false;
	}
	
	protected function getSystemAttributes( $object_it )
	{
		if ( isset($this->system_attributes[get_class($object_it->object)]) )
		{
			return $this->system_attributes[get_class($object_it->object)];
		}
		
		return $this->system_attributes[get_class($object_it->object)] = $object_it->object->getAttributesByGroup('system');
	}
	
	function isAttributeVisible( $attribute_name, $object_it, $action )
	{
		switch ( $attribute_name )
		{
			case 'Password':
				return false;
			
			default:	
				if ( $object_it->object->getAttributeType( $attribute_name ) == 'password' ) return false;

				$attributes = $this->getSystemAttributes($object_it);

				if ( in_array($attribute_name, $attributes) ) return false;
				
				return $object_it->object->IsAttributeVisible( $attribute_name ) && $object_it->object->IsAttributeStored($attribute_name);
		}
	}
}
