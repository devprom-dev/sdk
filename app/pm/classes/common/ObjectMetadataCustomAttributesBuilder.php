<?php

define( 'ORIGIN_CUSTOM', 'custom' );
include_once "persisters/CustomAttributesPersister.php";
include_once "sorts/CustomAttributeSortClause.php";

class ObjectMetadataCustomAttributesBuilder extends ObjectMetadataBuilder 
{
	private $classes = array();

	public function __construct()
	{
		$result = DAL::Instance()->QueryArray("SELECT GROUP_CONCAT(DISTINCT EntityReferenceName) Classes FROM pm_CustomAttribute");
		$this->classes = preg_split('/,/',$result[0]);
	}

    public function build( ObjectMetadata $metadata )
    {
        $object = $metadata->getObject();

        if ( $object->getEntityRefName() == 'pm_Project' ) return;
        if ( !in_array(strtolower(get_class($object)), $this->classes) ) return;
        
        $attr = getFactory()->getObject('pm_CustomAttribute');
        
		$attr_it = $attr->getByEntity($object);
		
		$attributes = array();
		$firstTabAttributes = array();
        $uidOverriden = false;
    	while( !$attr_it->end() )
		{
			if ( $attr_it->get('ReferenceName') == 'UID' ) {
                $uidOverriden = true;
                $metadata->setAttributeDefault('UID', $attr_it->getHtmlDecoded('DefaultValue'));
                $metadata->addAttributeGroup('UID', 'computed');
				$attr_it->moveNext();
				continue;
			}

            $db_type = $attr_it->getDBType();
            $groups = array_merge(
                array(
                    'permissions'
                ),
                $attr_it->getGroups()
            );

            $description = $attr_it->get('Description');

			if ( in_array('dictionary',$groups) ) {
				$url = getSession()->getApplicationUrl($attr_it).'project/dicts/PMCustomAttribute'.$attr_it->getEditUrl();
				$description .= ' '.str_replace('%1', $url, text(2183));
			}

			$attributes[$attr_it->get('ReferenceName')] = array(
				'dbtype' => $db_type,
				'caption' => $attr_it->get('Caption'),
				'visible' => $attr_it->get('IsVisible') == 'Y',
				'stored' => false,
				'type' => $db_type,
				'description' => $description,
				'ordernum' => $attr_it->get('OrderNum'),
				'origin' => ORIGIN_CUSTOM,
				'default' => $attr_it->get('ObjectKind') == '' ? $attr_it->getHtmlDecoded('DefaultValue') : '',
				'required' => $attr_it->get('ObjectKind') == '' ? $attr_it->get('IsRequired') == 'Y' : false,
				'groups' => $groups 
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