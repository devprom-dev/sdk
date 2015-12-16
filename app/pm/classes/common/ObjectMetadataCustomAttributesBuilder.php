<?php

define( 'ORIGIN_CUSTOM', 'custom' );

include "PMCustomDictionary.php";
include "persisters/CustomAttributesPersister.php";

class ObjectMetadataCustomAttributesBuilder extends ObjectMetadataBuilder 
{
	private $classes = array();

	public function __construct()
	{
		$result = mysql_fetch_array(DAL::Instance()->Query("SELECT GROUP_CONCAT(DISTINCT EntityReferenceName) Classes FROM pm_CustomAttribute"));
		$this->classes = preg_split('/,/',$result[0]);
	}

    public function build( ObjectMetadata $metadata )
    {
        $object = $metadata->getObject();

        if ( !in_array(strtolower(get_class($object)), $this->classes) ) return;
        
        $attr = getFactory()->getObject('pm_CustomAttribute');
        
		$attr_it = $attr->getByEntity($object);
		
		$attributes = array();
		
    	while( !$attr_it->end() )
		{
			$groups = array('permissions');
			
			$db_type = $attr_it->getRef('AttributeType')->getDbType();
			
			if ( $db_type == 'reference' )
			{
				$db_type = "REF_".$attr_it->get('AttributeTypeClassName')."Id";
			}
			
			$attributes[$attr_it->get('ReferenceName')] = array(
				'dbtype' => $db_type,
				'caption' => translate($attr_it->get('Caption')),
				'visible' => $attr_it->get('IsVisible') == 'Y',
				'stored' => false,
				'type' => $db_type,
				'description' => $attr_it->get('Description'),
				'ordernum' => $attr_it->get('OrderNum'),
				'origin' => ORIGIN_CUSTOM,
				'default' => $attr_it->get('ObjectKind') == '' ? $attr_it->getHtmlDecoded('DefaultValue') : '',
				'required' => $attr_it->get('ObjectKind') == '' ? $attr_it->get('IsRequired') == 'Y' : false,
				'groups' => $groups 
			);
			
			$attr_it->moveNext();
		}
		
		if ( $object->getEntityRefName() == 'pm_ChangeRequest' )
		{
		    unset($attributes['Description']);
		}
		
		if ( count($attributes) < 1 ) return;
		
		foreach( $attributes as $key => $attribute )
		{
			$metadata->setAttribute( $key, $attribute );
			$metadata->addAttributeGroup($key, 'additional');
		}
		
		$metadata->addPersister( new CustomAttributesPersister() );
    }
}