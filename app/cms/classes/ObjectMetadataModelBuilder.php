<?php

class ObjectMetadataModelBuilder extends ObjectMetadataBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
        $entity_it = $metadata->getObject()->getEntity();
         
        if ( !is_object($entity_it) ) return;

		$attribute_it = $entity_it->getAttributeIt();
			
		$attributes = array();
		
		$latest = 0;
			
		while( !$attribute_it->end() )
		{
			$type = $attribute_it->get('AttributeType');
			$ref_name = $attribute_it->get('ReferenceName');
			
			$attributes[$ref_name]['dbtype'] = $type;
			$attributes[$ref_name]['default'] = $attribute_it->get('DefaultValue');
			$attributes[$ref_name]['required'] = $attribute_it->get('IsRequired') == 'Y';
			
			if($type == 'RICHTEXT') $type = 'TEXT';
			if($type == 'LARGETEXT') $type = 'TEXT';
	
			$attributes[$ref_name] = array_merge($attributes[$ref_name], array (
				'caption' => translate($attribute_it->get_native('Caption')),
				'visible' => $attribute_it->get('IsVisible') == 'Y',
				'stored' => true,
				'type' => $type,
				'description' => '',
				'ordernum' => $attribute_it->get('OrderNum'),
				'origin' => ORIGIN_METADATA
			));
			
			if ( in_array($type, array('FILE','IMAGE')) )
			{
				$attributes[$ref_name.'Ext'] = $attributes[$ref_name];
				$attributes[$ref_name.'Ext']['visible'] = false; 
				$attributes[$ref_name.'Ext']['stored'] = false; 
				$attributes[$ref_name.'Ext']['type'] = 'TEXT';
				$attributes[$ref_name.'Ext']['required'] = false; 
				
				$attributes[$ref_name.'Path'] = $attributes[$ref_name];
				$attributes[$ref_name.'Path']['visible'] = false; 
				$attributes[$ref_name.'Path']['stored'] = false; 
				$attributes[$ref_name.'Path']['type'] = 'TEXT'; 
				$attributes[$ref_name.'Path']['required'] = false; 
			}
				
			$latest = $attribute_it->get('OrderNum');
			
			$attribute_it->moveNext();
		}

		// use ordering
		if ( $entity_it->get('IsOrdered') == 'Y' ) 
		{
			$attributes['OrderNum'] = array (
				'dbtype' => 'INTEGER',
				'caption' => 'Номер',
				'visible' => $entity_it->get('IsDictionary') == 'Y',
				'stored' => true,
				'type' => 'INTEGER',
				'description' => '',
				'ordernum' => $latest += 10,
				'origin' => ORIGIN_METADATA,
				'required' => true
			);
		}

		// creation and modification dates
		$attributes['RecordCreated'] = array (
			'dbtype' => 'DATETIME',
			'caption' => 'Дата создания',
			'visible' => false,
			'stored' => true,
			'type' => 'DATETIME',
			'description' => '',
			'ordernum' => $latest += 10,
			'origin' => ORIGIN_METADATA
		);

		$attributes['RecordModified'] = array (
			'dbtype' => 'DATETIME',
			'caption' => 'Дата изменения',
			'visible' => false,
			'stored' => true,
			'type' => 'DATETIME',
			'description' => '',
			'ordernum' => $latest += 10,
			'origin' => ORIGIN_METADATA
		);

		foreach( $attributes as $key => $attribute ) {
			$metadata->setAttribute( $key, $attribute );
		}
		foreach( array('RecordCreated', 'RecordModified') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'non-form');
		}
		if ( $entity_it->get('IsDictionary') == 'Y' && array_key_exists('ReferenceName', $attributes) ) {
			$metadata->addAttributeGroup('ReferenceName', 'alternative-key');
		}
    }
}