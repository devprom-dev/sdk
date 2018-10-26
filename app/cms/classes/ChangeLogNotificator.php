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
		    if ( !$object_it->defined($att_name) || !$prev_object_it->defined($att_name) ) continue;

            if ( $object_it->object->getAttributeType($att_name) == 'date' ) {
                $was_value = getSession()->getLanguage()->getDateFormattedShort($prev_object_it->get($att_name));
                $now_value = getSession()->getLanguage()->getDateFormattedShort($object_it->get($att_name));
            }
            elseif ( $object_it->object->getAttributeType($att_name) == 'datetime' ) {
                $was_value = getSession()->getLanguage()->getDateTimeFormatted($prev_object_it->get($att_name));
                $now_value = getSession()->getLanguage()->getDateTimeFormatted($object_it->get($att_name));
            }
            else {
                $was_value = $prev_object_it->getHtmlDecoded($att_name);
                $now_value = $object_it->getHtmlDecoded($att_name);
            }
			if( $was_value == $now_value ) continue;
			
			$modified_attributes[] = $att_name;
			if( !$this->isAttributeVisible($att_name, $object_it, 'modify') ) continue;

            $content = translate($object_it->object->getAttributeUserName($att_name)).': ';
            $content .= $this->getAttributeContent($prev_object_it, $object_it, $att_name);
        }

        return array($content, $modified_attributes);
	}

	protected function getAttributeContent($prev_object_it, $object_it, $att_name)
    {
        if ( $object_it->object->IsReference($att_name) )
        {
            return html_entity_decode($object_it->getRef($att_name)->getDisplayName());
        }
        else {
            $now_value = $object_it->getHtmlDecoded($att_name);

            if ( $now_value == 'Y' ) $now_value = translate('Да');
            if ( $now_value == 'N' ) $now_value = translate('Нет');

            return $now_value;
        }
    }

	function process( $object_it, $kind, $content = '', $visibility = 1, $author_email = '', $parms = array())
	{
		if( !$this->is_active($object_it) ) return;

		$userIt = getSession()->getUserIt();
        $userId = $userIt->getId();

		$change_log = getFactory()->getObject('ObjectChangeLog');
		$change_log->setVpdContext( $object_it );

		$title = '';
		$uid = new ObjectUID;
		if ( $uid->hasUid( $object_it ) ) {
			$title .= $uid->getUidOnly( $object_it );
		}
		$title .= html_entity_decode( $object_it->getDisplayName(), ENT_COMPAT | ENT_HTML401, APP_ENCODING );

		$class_name = strtolower(get_class($object_it->object));
		$parms['Caption'] = $title;
		$parms['ObjectId'] = $object_it->getId();
		$parms['ClassName'] = $class_name == 'metaobject' ? $object_it->object->getClassName() : $class_name;
		$parms['EntityRefName'] = $object_it->object->getEntityRefName();
		$parms['EntityName'] = translate($object_it->object->getDisplayName());
		$parms['ChangeKind'] = $kind;
		$parms['Author'] = $author_email != '' ? $author_email : ($userId < 1 ? $userIt->getHtmlDecoded('Caption') : '');
		$parms['Content'] = $content;
		$parms['VisibilityLevel'] = $visibility;
		$parms['SystemUser'] = $userId;
        $parms['UserName'] = $userIt->getHtmlDecoded('Caption');
		if ( $parms['AccessClassName'] == '' ) $parms['AccessClassName'] = $parms['ClassName'];

		$id = $change_log->add_parms($parms);
		
		$log_attribute = getFactory()->getObject('ObjectChangeLogAttribute');
        $log_attribute->setNotificationEnabled(false);
		foreach( $this->modified_attributes as $attribute )
		{
		    if ( in_array($attribute, array('RecordModified','RecordCreated')) ) continue;
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
