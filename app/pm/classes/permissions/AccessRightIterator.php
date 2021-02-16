<?php

class AccessRightIterator extends OrderedIterator
{
 	var $hash;

	function getViewAccess( $role_id, $object_id, $reference_name )
	{	
 		$access = $this->getHashedAccess( $role_id, $object_id, $reference_name );

		switch ( $access )
		{
			case 'view':
				return 1;
				
			case 'none':
				return 0;
				
			default:
				return -1;
		}
	}
	
	function setRowset( $rowset )
	{
		parent::setRowset($rowset);
		
		while ( !$this->end() )
		{
			$this->hash[md5($this->get('ProjectRole').strtolower($this->get('ReferenceName')).
				$this->get('ReferenceType'))] = $this->get('AccessType');
				
			$this->moveNext();
		}

		$this->moveFirst();
	}
	
 	function getModuleAccess( $role_id, $object_it )
 	{
 		return $this->getViewAccess( $role_id, $object_it->getId(), 'PMPluginModule' );
 	}

 	function getReportAccess( $role_id, $object_it )
 	{
 		return $this->getViewAccess( $role_id, $object_it->getId(), 'PMReport' );
 	}

 	function getWikiAccess( $role_id, $page_it )
 	{
 		$access = $this->getHashedAccess( $role_id,
 			get_class($page_it->object), 'Y' );

		switch ( $access )
		{
			case 'modify':
				return 2;
				
			case 'view':
				return 1;
				
			case 'none':
				return 0;

			default:
				return -1;
		}
 	}

 	function getAttributeAccess( $role_id, &$object, $attribute_refname )
 	{
 		$access = $this->getHashedAccess( $role_id, 
 			get_class($object).'.'.$attribute_refname, 'A' );

		switch ( $access )
		{
			case 'modify':
				return 2;
				
			case 'view':
				return 1;
				
			case 'none':
				return 0;

			default:
				return -1;
		}
 	}
 	
 	function getEntityAccess( $role_id, $entity_ref_name )
 	{
 		$access = $this->getHashedAccess( $role_id, $entity_ref_name, 'Y' );

		switch ( $access )
		{
            case 'cru':
                return 3;

			case 'modify':
				return 2;
				
			case 'view':
				return 1;
				
			case 'none':
				return 0;

			default:
				return -1;
		}
 	}

 	function getClassAccess( $role_id, $classes )
 	{
 	    foreach( $classes as $class_name ) {
            $access = $this->getHashedAccess( $role_id, $class_name, 'Y' );
            switch ( $access )
            {
                case 'cru':
                    return 3;

                case 'modify':
                    return 2;

                case 'view':
                    return 1;

                case 'none':
                    return 0;
            }
        }
        return -1;
 	}
 	
 	function getHashedAccess( $role_id, $object, $type )
 	{ 
 		return $this->hash[md5($role_id.strtolower($object).$type)];
 	}
 	
 	function getDisplayName()
 	{
		$role_it = $this->getRef('ProjectRole');
 		$caption = $role_it->getDisplayName();
 		
 		switch ( $this->get('ReferenceType') )
 		{
 			case 'Y':
 				$caption .= ': '.translate('Объект');
 				break;

 			case 'A':
 				$caption .= ': '.translate('Атрибут');
 				break;
 				
 			case 'O':
				$object = getFactory()->getObject('pm_ObjectAccess');
				
				$object_it = $object->getByRefArray( array (
					'ObjectId' => $this->get('ReferenceName'),
					'ObjectClass' => $this->get('DisplayName')
				));

 				$caption .= ': '.$object_it->getDisplayName();
 				break;
 				
 			case 'M':
 			    break;
 				
 			case 'PMPluginModule':
 			    $caption .= ': '.getFactory()->getObject('Module')->getDisplayName();
 				break;
 			    
 			default:
				$object = getFactory()->getObject($this->get('ReferenceType'));
 				$caption .= ': '.$object->getDisplayName();
 				break;
 		}
 		
 		$caption .= ' ('.$this->getObjectName().')';
 		switch ( $this->get('AccessType') )
 		{
 			case 'none':
 				$caption .= ' ['.translate('Нет').']';
 				break;

 			case 'view':
 				$caption .= ' ['.translate('Просмотр').']';
 				break;

 			case 'modify':
 				$caption .= ' ['.text(2811).']';
 				break;

            case 'cru':
                $caption .= ' ['.text(2812).']';
                break;
 		}
 		
 		return $caption;
 	}
 	
 	function getObjectName()
 	{
 		global $model_factory;
 		
		switch ( $this->get('ReferenceType') )
		{
			case 'Y':
				$object = $model_factory->getObject($this->get('ReferenceName'));
				return $object->getDisplayName();

			case 'A':
				$parts = preg_split('/\./', $this->get('ReferenceName'));
				$object = $model_factory->getObject($parts[0]);
				return translate($object->getAttributeUserName($parts[1]));

			case 'M':
			    return '';
				
			case 'PMPluginModule':

			    $object_it = $model_factory->getObject('Module')->getExact($this->get('ReferenceName'));
				
				return $object_it->getDisplayName();
			    
			default:
				$object = $model_factory->getObject($this->get('ReferenceType'));
				$object_it = $object->getExact($this->get('ReferenceName'));
				
				return $object_it->getDisplayName();
		} 		
 	}
 	
 	function getViewUrl()
 	{
 		$info = getFactory()->getObject('Module')
 					->getExact('permissions/settings')->buildMenuItem('role='.$this->get('ProjectRole'));
 		return $info['url'];
 	}
 	
 	function getRecordKey( $reference_name, $reference_type, $project_role )
 	{
 		return join( ',', array($reference_name, $reference_type, $project_role) );
 	}
} 