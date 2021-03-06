<?php

class CustomAttributeFinalForm extends PMPageForm
{
	function __construct( $object ) 
	{
		$parts = preg_split('/\:/', $_REQUEST['EntityReferenceName']);
		
		if ( count($parts) > 1 )
		{
			$_REQUEST['EntityReferenceName'] = $parts[0];
			$_REQUEST['ObjectKind'] = $parts[1];
		}

		parent::__construct( $object );
	}

	protected function getAttributeType()
	{
		$attr_type = $this->getFieldValue('AttributeType');
		if ( $attr_type == '' ) return $attr_type;

		return getFactory()->getObject('CustomAttributeType')->getExact($attr_type)->get('ReferenceName');
	}

	function extendModel()
	{
		$object = $this->getObject();
		$object->setAttributeDescription('DefaultValue', text(1083));
		$object->setAttributeVisible('ShowMainTab', true);

		parent::extendModel();

        foreach( $object->getAttributesByGroup('additional') as $field ) {
            $object->setAttributeRequired($field, false);
        }
        $object->setAttributeVisible( 'OrderNum', true );

		$type = $this->getAttributeType();
		if ( $type == 'computed' ) {
			$object->setAttributeCaption('DefaultValue', text(2133));
			$object->setAttributeDescription('DefaultValue', text(2134));
		}

		if ( $_REQUEST['formonly'] == '' ) {
            $object->setAttributeVisible('EntityReferenceName', false);
            $object->setAttributeVisible('AttributeType', false);
        }
	}

	function validateInputValues( $id, $action )
	{
		$object = $this->getObject();

		// check for conflicts with metadata attributes
		$reserved = array();

		$className = getFactory()->getClass($_REQUEST['EntityReferenceName']);
		if ( class_exists($className) ) {
            $entity = getFactory()->getObject($className);

            foreach( $entity->getAttributes() as $key => $attribute ) {
                if ( !$entity->IsAttributeStored( $key ) ) continue;
                if ( $key == 'UID' ) continue;
                $reserved[] = strtolower($key);
            }

            if ( in_array( strtolower(trim($_REQUEST['ReferenceName'])), $reserved ) ) {
                return text(1086);
            }

            // check for db-column correctness
            if ( !\TextUtils::checkDatabaseColumnName($_REQUEST['ReferenceName']) ) {
                return text(1126);
            }

            // check for duplicates across custom attributes
            $dup_it = $object->getByRefArray(
                array( 'LCASE(ReferenceName)' => strtolower($_REQUEST['ReferenceName']),
                    'EntityReferenceName' => $_REQUEST['EntityReferenceName'] )
            );
            if ( $dup_it->count() > 0 && $dup_it->getId() != $id ) {
                return text(1086);
            }
        }
		else {
		    unset($_REQUEST['ReferenceName']);
        }

		$attr_type = $_REQUEST['AttributeType'];
		if ( $attr_type == '' )
		{
			$object_it = $object->getExact( $id );
			$attr_type = $object_it->get('AttributeType');
		}
		
		if ( $attr_type == 'dictionary' )
		{
			$lines = preg_split('/\n/', trim($_REQUEST['ValueRange'], '\n\r'));
			
			if ( count($lines) < 1 ) return text(1091);

			$was_keys = array();
			
			foreach( $lines as $line )
			{
				if ( $line == '' ) continue;
				
				$parts = preg_split('/:/', $line);

				if( count($parts) < 2 ) return str_replace('%1', $line, text(1092));

				if ( in_array( $parts[0], $was_keys ) ) return text(1093);
					
				array_push( $was_keys, $parts[0] );
				
				if ( !is_numeric( $parts[0] ) || $parts[0] < 1 ) return str_replace('%1', $line, text(1094));
			}
		}
		
		// check for valid default values
		$default_value = $_REQUEST['DefaultValue'];
		
		if ( $default_value != '' )
		{
			$mapper = new ModelDataTypeMapper();
			
			$default_value = $mapper->getMapper(
						$this->getObject()->getAttributeObject('AttributeType')->getExact($attr_type)->getDbType()
				)->map($default_value);
			
			if ( $default_value == '' )
			{
				return text(1741);
			}
		}
		
		return parent::validateInputValues( $id, $action );
	}
	
	function IsAttributeVisible( $attr_name )
	{
		switch ( $attr_name )
		{
			case 'ValueRange':
				return in_array($this->getAttributeType(), array('dictionary'));
			case 'DefaultValue':
				return !in_array($this->getAttributeType(), array('date','password'));
			case 'IsRequired':
				return !in_array($this->getAttributeType(), array('computed'));
            case 'IsMultiple':
                return in_array($this->getAttributeType(), array('dictionary','reference'));
			case 'IsUnique':
				return !in_array($this->getAttributeType(), array('computed'));
			default:
				return parent::IsAttributeVisible( $attr_name );
		}
	}
	
	function IsAttributeRequired( $attr_name ) 
	{
		switch ( $attr_name )
		{
			case 'ValueRange':
				return $this->getAttributeType() == 'dictionary';
			default:
				return parent::IsAttributeRequired( $attr_name );
		}
	}
	
	function createFieldObject( $attr_name ) 
	{
		switch( $attr_name )
		{
			case 'DefaultValue':
				$atttype = $this->getAttributeType();
				switch ( $atttype )
				{
					case 'integer':
						return new FieldNumber();
                    case 'reference':
                        $className = getFactory()->getClass($this->getFieldValue('AttributeTypeClassName'));
                        if ( class_exists($className) ) {
                            return new FieldAutoCompleteObject(getFactory()->getObject($className));
                        }
                        else {
                            return parent::createFieldObject( $attr_name );
                        }
					default:
						return parent::createFieldObject( $attr_name );
				}
				break;
				
			default:
				return parent::createFieldObject( $attr_name );
		}
	}
	
	function createField( $attr_name ) 
	{
		$field = parent::createField( $attr_name );

		switch( $attr_name )
		{
			case 'EntityReferenceName':
				$field->setReadonly(true);
				$field->setText( $this->getObject()->getEntityDisplayName(
					$this->getFieldValue('EntityReferenceName'), $this->getFieldValue('ObjectKind'))
				); 
				break;
				
			case 'AttributeType':
				$field->setReadonly(true);
				$type = new CustomAttributeType();
				$type_it = $type->getExact($this->getFieldValue('AttributeType'));
				$className = getFactory()->getClass($this->getFieldValue('AttributeTypeClassName'));
				if ( $type_it->get('ReferenceName') == 'reference' && class_exists($className) ) {
					$field->setText(
                        $type_it->getDisplayName().': '.getFactory()->getObject($className)->getDisplayName()
					);
				}
				else {
                    $field->setText($type_it->getDisplayName());
                }
				break;
				
			case 'OrderNum':
				$object_it = $this->getObjectIt();
				if ( !is_object($object_it) ) {
					$ref = getFactory()->getObject( $this->getFieldValue('EntityReferenceName') );
					$field->setValue( $ref->getLatestOrderNum() + 10 );
				}
				break;
		}
		
		return $field;
	}
	
	function getFieldDescription( $attr )
	{
		$type = $this->getAttributeType();
		
		switch ( $attr )
		{
			case 'Caption':
				return text(1081);

			case 'ReferenceName':
				return text(1082);

			case 'IsVisible':
				return text(1084);

			case 'OrderNum':
				return text(1085);

			case 'Description':
				return text(1149);

			case 'IsUnique':
				return text(1175);

            case 'ShowMainTab':
                return text(2682);

            case 'IsRequired':
                return text(2683);

			case 'ValueRange':
				switch ( $type )
				{
					case 'dictionary':
						return text(1087);
						
					default:
						return ''; 
				}
				
			default:
				return parent::getFieldDescription( $attr );
		}
	}

	function redirectOnAdded($object_it, $redirect_url = '')
    {
        $redirect_url = $this->getObject()->getPage();
        parent::redirectOnAdded($object_it, $redirect_url);
    }

    function getBodyTemplate() {
        return "core/PageFormBody.php";
    }
}