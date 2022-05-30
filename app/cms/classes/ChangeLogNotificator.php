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
		$this->process($object_it, $object_it, 'added');
	}

 	function modify( $prev_object_it, $object_it ) 
	{
        $changeLogRegistry = (new Metaobject('ObjectChangeLog'))->getRegistry();
        $changeLogRegistry->setLimit(1);
        $logIt = $changeLogRegistry->Query(
            array(
                new FilterAttributePredicate('ObjectId', $object_it->getId()),
                new FilterAttributePredicate('EntityRefName', $object_it->object->getEntityRefName()),
                new FilterAttributePredicate('SystemUser', getSession()->getUserIt()->getId()),
                new FilterCreatedSinceSecondsPredicate(60),
                new SortRecentClause()
            )
        );
        $parms = array();
        if ( $logIt->getId() != '' && $logIt->get('ChangeKind') == 'modified' ) {
            $data = \JsonWrapper::decode($logIt->getHtmlDecoded('ObjectUrl'));
            if ( is_array($data) && count($data) > 0 ) {
                $prev_object_it = $prev_object_it->object->createCachedIterator(array($data));
                $parms = array_map(
                    function( $row ) {
                        return html_entity_decode($row);
                    }, $logIt->getData()
                );
            }
        }
        if ( count($parms) < 1 ) {
            $parms['ObjectUrl'] = \JsonWrapper::encode($prev_object_it->getData());
        }

		list($content, $this->modified_attributes) = $this->getContent( $prev_object_it, $object_it );

        $this->process($object_it, $prev_object_it, 'modified',
            $content, $content != '' ? $this->default_visibility : CHLN_VISIBILITY_HIDDEN, '', $parms);
	}

 	function delete( $object_it ) 
	{
		$this->modified_attributes = array();
		$this->process($object_it, $object_it, 'deleted');
	}
	
	function getContent( $prev_object_it, $object_it )
	{
		$modified_attributes = array();
		$modified_content = array();
		
		foreach( $object_it->object->getAttributes() as $att_name => $attribute )
		{
		    if ( !$object_it->defined($att_name) || !$prev_object_it->defined($att_name) ) continue;
            if ( !$this->isAttributeVisible($att_name, $object_it, 'modify') ) continue;

            $was_value = $this->getValue($prev_object_it, $att_name);
            $now_value = $this->getValue($object_it, $att_name);
			if( $was_value == $now_value ) continue;
			
			$modified_attributes[] = $att_name;

            $content = translate($object_it->object->getAttributeUserName($att_name)).': ';
            $content .= $this->getAttributeContent($object_it, $att_name, $was_value, $now_value);
            $modified_content[] = $content;
        }

        return array(join($modified_content, '<br/>'), $modified_attributes);
	}

	protected function getValue( $objectIt, $attribute )
    {
        switch( $objectIt->object->getAttributeType($attribute) )
        {
            case 'date':
                return $objectIt->getDateFormattedShort($attribute);
            case 'datetime':
                return getSession()->getLanguage()->getDateTimeFormatted($objectIt->get($attribute));
            case 'wysiwyg':
                return $objectIt->getHtmlDecoded($attribute);
            case 'float':
                return getSession()->getLanguage()->formatFloatValue(
                    $objectIt->get($attribute),
                    $objectIt->object->getAttributeGroups($attribute)
                );
            default:
                if ( $objectIt->object->IsReference($attribute) && $objectIt->get($attribute) != '' ) {
                    return $objectIt->getRef($attribute)->getDisplayName();
                }
                else {
                    $value = $objectIt->get($attribute);

                    if ( $value == 'Y' ) $value = translate('Да');
                    if ( $value == 'N' ) $value = translate('Нет');

                    return $value;
                }
        }
    }

	protected function getAttributeContent($object_it, $att_name, $wasValue, $nowValue)
    {
        return $nowValue;
    }

	function process($object_it, $prev_object_it, $kind, $content = '', $visibility = 1, $author_email = '', $parms = array())
	{
		if( !$this->is_active($object_it) ) return;
        $change_log = getFactory()->getObject('ObjectChangeLog');
        $change_log->setVpdContext( $object_it );
        $changeRegistry = new ObjectRegistrySQL($change_log);

        $userIt = getSession()->getUserIt();
        $userId = $userIt->getId();

        $parms['ChangeKind'] = $kind;
        $parms['Author'] = $author_email != '' ? $author_email : ($userId < 1 ? $userIt->getHtmlDecoded('Caption') : '');
        $parms['Content'] = $content;
        $parms['VisibilityLevel'] = $visibility;
        $parms['SystemUser'] = $userId;

		if ( $parms['ObjectChangeLogId'] != '' ) {
		    unset($parms['RecordModified']);
            unset($parms['RecordVersion']);
            unset($parms['Transaction']);

            $changeRegistry->Store($changeRegistry->QueryById($parms['ObjectChangeLogId']), $parms);
            $id = $parms['ObjectChangeLogId'];
        }
		else {
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
            $parms['UserName'] = $userIt->getHtmlDecoded('Caption');
            if ( $object_it->get('VPD') != '' ) $parms['VPD'] = $object_it->get('VPD');
            if ( $parms['AccessClassName'] == '' ) $parms['AccessClassName'] = $parms['ClassName'];

            $id = $changeRegistry->Create($parms)->getId();
        }

		$log_attribute = getFactory()->getObject('ObjectChangeLogAttribute');
        $log_attribute->setNotificationEnabled(false);
        $attributeRegistry = $log_attribute->getRegistry();
		foreach( $this->modified_attributes as $attribute ) {
		    if ( in_array($attribute, array('RecordModified','RecordCreated')) ) continue;
            $attributeRegistry->Merge(
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

				return $object_it->object->IsAttributeStored($attribute_name);
		}
	}
}
