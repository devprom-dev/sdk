<?php

class PMCustomAttributeIterator extends CacheableIterator
{
 	function toReferenceNames()
 	{
 		$names = array();
 		while ( !$this->end() )
 		{
 			array_push( $names, $this->get('ReferenceName')); 
 			$this->moveNext();
 		}
 		return $names;
 	}
 	
 	function toDictionary()
 	{
 		$lov = array();
 		foreach( preg_split('/\n\r?/', $this->get('ValueRange')) as $line ) {
 			if ( trim($line) == '' ) continue;
 			$parts = preg_split('/:/', $line );
 			$lov[trim($parts[0], ' '.chr(10))] = html_entity_decode(trim($parts[1]));
 		}
 		return $lov;
 	}
 	
 	function getEntityDisplayName() {
 		return $this->object->getEntityDisplayName($this->get('EntityReferenceName'), $this->get('ObjectKind'));
 	}

	function getEntityRegistry()
	{
		$class_name = getFactory()->getClass($this->get('EntityReferenceName'));
		if ( !class_exists($class_name, false) ) return null;

		$ref = getFactory()->getObject($class_name);
		$registry = $ref->getRegistry();

		$filters = $registry->getFilters();
		$filters[] = new FilterVpdPredicate();

		if ( $this->get('ObjectKind') != '' ) {
            $attributes = $ref->getAttributesByGroup('customattribute-descriptor');
            if ( count($attributes) > 0 ) {
                $filters[] = new FilterAttributePredicate($attributes[0], $this->get('ObjectKind'));
            }
		}

		$registry->setFilters($filters);
		return $registry;
	}

	function getDbType()
    {
        $db_type = $this->getRef('AttributeType')
            ->getDbType($this->getHtmlDecoded('DefaultValue'));

        if ( $db_type == '' ) {
            $db_type = 'VARCHAR';
        }
        if ( $db_type == 'reference' ) {
            return "REF_".$this->get('AttributeTypeClassName')."Id";
        }
        return $db_type;
    }

    function getGroups()
    {
        $groups = array(
            'permissions'
        );

        $type_it = $this->getRef('AttributeType');
        if ( $type_it->get('ReferenceName') == 'dictionary' ) {
            $groups[] = 'dictionary';
        }
        if ( $type_it->get('ReferenceName') == 'computed' ) {
            $groups[] = 'computed';
        }

        if ( $this->get('IsMultiple') == 'Y' ) {
            $groups[] = 'multiselect';
        }

        if ( $this->get('IsNotificationVisible') == 'N' ) {
            $groups[] = 'skip-notification';
        }

        foreach( \TextUtils::parseItems($this->get('Groups')) as $group ) {
            if ( preg_match('/[0-9a-z\-_]+/i', $group) ) {
                $groups[] = $group;
            }
        }

        if ( $this->get('ShowMainTab') != 'Y' && !in_array('trace', $groups) ) {
            $groups[] = 'additional';
        }
        return $groups;
    }
}
