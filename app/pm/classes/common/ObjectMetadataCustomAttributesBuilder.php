<?php

define( 'ORIGIN_CUSTOM', 'custom' );
include_once "persisters/CustomAttributesPersister.php";
include_once "sorts/CustomAttributeSortClause.php";

class ObjectMetadataCustomAttributesBuilder extends ObjectMetadataBuilder 
{
	private $classes;

	public function __construct()
	{
		$result = DAL::Instance()->QueryArray("SELECT GROUP_CONCAT(DISTINCT EntityReferenceName) Classes FROM pm_CustomAttribute");
		$this->classes = preg_split('/,/',$result[0]);
	}

    public function build( ObjectMetadata $metadata )
    {
        $object = $metadata->getObject();
        if ( in_array($object->getEntityRefName(), array('pm_Project','pm_CustomAttribute','pm_ProjectLink')) ) return;

        $attr = getFactory()->getObject('pm_CustomAttribute');
        if ( count(array_intersect($attr->getEntityClasses($object), $this->classes)) < 1 ) return;
        
		$attr_it = $attr->getByEntity($object);

		$attributes = array();
		$firstTabAttributes = array();
        $uidOverriden = false;
    	while( !$attr_it->end() )
		{
			if ( $attr_it->get('ReferenceName') == 'UID' ) {
                $uidOverriden = true;
                $metadata->setAttributeDefault('UID', $attr_it->getHtmlDecoded('DefaultValue'));
                $metadata->setAttributeCaption('UID', $attr_it->getHtmlDecoded('Caption'));
                $metadata->addAttributeGroup('UID', 'computed');
				$attr_it->moveNext();
				continue;
			}

            $db_type = $attr_it->getDBType();

			$attributes[$attr_it->get('ReferenceName')] = array(
				'dbtype' => $db_type,
				'caption' => $attr_it->get('Caption'),
				'visible' => $attr_it->get('IsVisible') == 'Y',
				'stored' => false,
				'type' => $db_type,
				'description' => $attr_it->get('Description'),
				'ordernum' => $attr_it->get('OrderNum'),
				'origin' => ORIGIN_CUSTOM,
				'default' => $attr_it->get('ObjectKind') == '' ? $attr_it->getHtmlDecoded('DefaultValue') : '',
				'required' => $attr_it->get('ObjectKind') == '' ? $attr_it->get('IsRequired') == 'Y' : false,
				'groups' => $attr_it->getGroups()
			);
			if ( $attr_it->get('ShowMainTab') == 'Y' ) {
				$firstTabAttributes[] = $attr_it->get('ReferenceName');
			}
			$attr_it->moveNext();
		}
		
		if ( $object->getEntityRefName() == 'pm_ChangeRequest' ) {
		    unset($attributes['Description']);
		}
		
		if ( count($attributes) < 1 && !$uidOverriden ) return;
		
		foreach( $attributes as $key => $attribute )
		{
			$metadata->setAttribute( $key, $attribute );
			if ( !in_array($key, $firstTabAttributes) && !in_array('trace', $attribute['groups']) ) {
				$metadata->addAttributeGroup($key, 'additional');
			}
			$metadata->addAttributeGroup($key, 'bulk');
		}
		
		$metadata->addPersister( new CustomAttributesPersister() );
    }
}