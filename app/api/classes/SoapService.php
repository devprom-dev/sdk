<?php

// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

include_once SERVER_ROOT_PATH."pm/classes/sessions/SessionBuilderProject.php";
include_once SERVER_ROOT_PATH."pm/classes/project/predicates/ProjectAccessibleVpdPredicate.php";

class SoapService
{
    private $methodsDelimiter = '.';
    private $attributes = array();

	function SoapService()
	{
	}
	
	static function logInfo( $message )
	{
		try
		{
			Logger::getLogger('SOAP')->info( $message );
		}
		catch( Exception $e )
		{
			error_log( $e->getMessage() );
		}
	}
		
	static function logError( $message )
	{
		try
		{
			Logger::getLogger('SOAP')->error( $message );
			
			return $message;
		}
		catch( Exception $e )
		{
			error_log( $e->getMessage() );
		}
	}
	
	// Returns token to authenticate user
	function login( $token )
	{
	    global $session, $server;
	    
	    $_REQUEST['token'] = $token;

		$session = new SOAPSession();

		$user_it = $session->getUserIt();
		if ( $user_it->getId() < 1 ) {
			$server->fault('', $this->logError(text(224)));
			return;
		}

		$project_id = $session->getProject();
	    if ( $project_id < 1 ) return;

		$builders = $session->getBuilders();

		$project_it = getFactory()->getObject('ProjectAccessible')->getRegistry()->Query(
		    array ( new FilterInPredicate($project_id) )
        );
        if ( $project_it->count() < 1 ) {
            $parts = getFactory()->getObject('Participant')->getRegistry()->Count(
                array ( new FilterAttributePredicate('Project', $project_id) )
            );
            if ( $parts > 0 ) {
                $server->fault('', $this->logError(text(224)));
                return;
            }
        }

		$session = new PMSession( $project_it, $session->getAuthenticationFactory() );

		foreach( $builders as $builder ) {
			$session->addBuilder($builder);
		}
	}

	// Transforms base64 representation of a file into local file
	function storeFiles( $object, $parms )
	{
		global $_FILES;

		$attributes = array_keys($object->getAttributes());
		for( $i = 0; $i < count($attributes); $i++ )
		{
			switch ( $object->getAttributeType($attributes[$i]) )
			{
				case 'file':
				case 'image':
					if ( $parms[$attributes[$i]] != '' )
					{
						$filename = tempnam(SERVER_UPDATE_PATH, 'tux');
						$file = fopen($filename, 'w+');
						 
						fwrite( $file, $parms[$attributes[$i]] );
						fclose( $file );
						 
						$_FILES[$attributes[$i]]['tmp_name'] = $filename;
						$_FILES[$attributes[$i]]['name'] = basename($parms[$attributes[$i].'Path']);
						$_FILES[$attributes[$i]]['type'] = $parms[$attributes[$i].'Ext'];
					}
			}
		}
	}

	// Loads object data using the given ID
	function load( $token, $classname, $id )
	{
		global $model_factory, $server;

		$this->login( $token );

		$object = $model_factory->getObject($classname);

		if ( !getFactory()->getAccessPolicy()->can_read($object) )
		{
			$server->fault('', $this->logError(text(549)));
			return;
		}

		$it = $object->getExact($id);
		 
		if ( $it->getId() == '' )
		{
			$server->fault('', $this->logError(text(708)));
			return;
		}
		 
		$result = $this->serializeToSoap( $it );
		return $result;
	}

	// Appends new object using the given ID and parameters
	function add( $token, $classname, $parms )
	{
		global $_REQUEST, $server;
		
		$this->login( $token );

		$object = getFactory()->getObject($classname);
		
		if ( !getFactory()->getAccessPolicy()->can_create($object) )
		{
			$server->fault('', $this->logError(text(706)));
			return;
		}

		$attrs = $this->getAttributes( $object );
		foreach( array_keys($parms) as $key )
		{
			$parms[$key] = $this->soapValueToSystem(
				$attrs[$key]['type'], $parms[$key] );
		}

		$this->storeFiles( $object, $parms );

		$this->setDefaultValues( $object, $attrs, $parms );

		$id = $object->add_parms($parms);

		$it = $object->getExact($id);

        getFactory()->getEventsManager()
            ->executeEventsAfterBusinessTransaction(
                $it->copy(), 'WorklfowMovementEventHandler', $parms
            );

		$result = $this->serializeToSoap( $it );

		return $result;
	}

	// Appends array of new objects
	function addBatch( $token, $classname, $parms )
	{
		global $_REQUEST, $server;
		 
		$this->login( $token );

		$object = getFactory()->getObject($classname);
		
		$attrs = $this->getAttributes( $object );

		if ( !getFactory()->getAccessPolicy()->can_create($object) )
		{
			$server->fault('', $this->logError(text(706)));
			return;
		}

		$result = array();
		$ids = array();

		foreach( $parms as $object_parms )
		{
			foreach( $object_parms as $key => $param )
			{
				$object_parms[$key] = $this->soapValueToSystem(
				$attrs[$key]['type'], $param );
			}
				
			$this->storeFiles( $object, $object_parms );
			$this->setDefaultValues( $object, $attrs, $object_parms );
				
			$id = $object->add_parms($object_parms);
			$it = $object->getExact($id);

			array_push( $result, $this->serializeToSoap( $it ) );
            $ids[] = $id;
		}

        getFactory()->getEventsManager()
            ->executeEventsAfterBusinessTransaction(
                $object->getExact($ids), 'WorklfowMovementEventHandler'
            );

        return $result;
	}

	// Stores object data using the given ID
	function store( $token, $classname, $id, $parms )
	{
		global $model_factory, $server;

		$this->login( $token );

		$object = $model_factory->getObject($classname);

		$it = $object->getExact($id);

		if ( $it->count() < 1 )
		{
			$server->fault('', $this->logError(text(708)));
			return;
		}

		if ( !getFactory()->getAccessPolicy()->can_modify($it) )
		{
			$server->fault('', $this->logError(text(707)));
			return;
		}

		$attrs = $this->getAttributes( $object );

		unset($parms['RecordVersion']);
		unset($parms['RecordCreated']);
		unset($parms['RecordModified']);

		foreach( array_keys($parms) as $key )
		{
			$parms[$key] = $this->soapValueToSystem(
				$attrs[$key]['type'], $parms[$key] );
		}

		$this->storeFiles( $object, $parms );
		
		$this->setDefaultValues( $object, $attrs, $parms );

		$result = $object->modify_parms($id, $parms);
		if ( $result < 1 ) {
		    $server->fault('', $this->logError(str_replace('%1', $it->getDisplayName(), text(1216))));
        }

        getFactory()->getEventsManager()
            ->executeEventsAfterBusinessTransaction(
                $object->getExact($id), 'WorklfowMovementEventHandler', $parms
            );

        return $id;
	}

	// Stores array of objects
	function storeBatch( $token, $classname, $parms )
	{
		global $model_factory, $server;
		 
		$this->login( $token );

		$object = $model_factory->getObject($classname);
		$attrs = $this->getAttributes( $object );
		$ids = array();

		foreach( $parms as $object_parms )
		{
			$it = $object->getExact($object_parms['Id']);
			
			if ( $it->count() < 1 )
			{
				$server->fault('', $this->logError(text(708)));
				return;
			}

			if ( !getFactory()->getAccessPolicy()->can_modify($it) )
			{
				$server->fault('', $this->logError(text(707)));
				return;
			}

			// plugin should process it correctly first
			// $object_parms['WasRecordVersion'] = addslashes($object_parms['RecordVersion']);
			unset($object_parms['RecordVersion']);
			unset($object_parms['RecordCreated']);
			unset($object_parms['RecordModified']);

			foreach( $object_parms as $key => $param ) {
				$object_parms[$key] = $this->soapValueToSystem($attrs[$key]['type'], $param );
			}

			$this->storeFiles( $object, $object_parms );
			$this->setDefaultValues( $object, $attrs, $object_parms );
				
			$result = $object->modify_parms($it->getId(), $object_parms);
			if ( $result < 1 ) {
				$server->fault('', $this->logError(str_replace('%1', $it->getDisplayName(), text(1216))));
			}
            $ids[] = $it->getId();
		}

        getFactory()->getEventsManager()
            ->executeEventsAfterBusinessTransaction(
                $object->getExact($ids), 'WorklfowMovementEventHandler'
            );
    }

	// Deletes an object using the given ID
	function delete( $token, $classname, $id )
	{
		global $model_factory, $server;
		 
		$this->login( $token );

		$object = $model_factory->getObject($classname);
		$it = $object->getExact($id);

		if ( $it->count() < 1 )
		{
			$server->fault('', $this->logError(text(708)));
			return;
		}

		if ( !getFactory()->getAccessPolicy()->can_delete($it) )
		{
			$server->fault('', $this->logError(text(707)));
			return;
		}

		$object->delete($id);
	}

	// Deletes array of objects
	function deleteBatch( $token, $classname, $parms)
	{
		global $model_factory, $server;
		 
		$this->login( $token );

		$object = $model_factory->getObject($classname);

		foreach( $parms as $values )
		{
			$it = $object->getExact($values['Id']);

			if ( $it->count() < 1 )
			{
				$server->fault('', $this->logError(text(708)));
				return;
			}

			if ( !getFactory()->getAccessPolicy()->can_delete($it) )
			{
				$server->fault('', $this->logError(text(707)));
				return;
			}

			$object->delete($values['Id']);
		}
	}

	// Fills default values
	function setDefaultValues( $object, $attrs, & $parms )
	{
		foreach ( $attrs as $key => $attribute )
		{
			if ( $parms[$key] != '' || !$object->IsAttributeRequired( $key ) ) continue;
			$parms[$key] = $object->getDefaultAttributeValue( $key );
		}
	}

	// Returns collection of objects
	function getAll( $token, $classname )
	{
		global $server;
		 
		$this->login( $token );
		$object = getFactory()->getObject($classname);

		if ( !getFactory()->getAccessPolicy()->can_read($object) ) {
			$server->fault('', $this->logError(text(549)));
			return;
		}

		if ( $object instanceof Project ) {
			$object->addFilter( new ProjectAccessibleVpdPredicate() );
		}

		$object->setLimit(100);
		
		$it = $object->getAll();
		
		$result = array();
		 
		while ( !$it->end() )
		{
			array_push( $result, $this->serializeToSoap( $it ) );
			
			$it->moveNext();
		}
		 
		return $result;
	}

	// Returns collection of objects found by given condition
	function find( $token, $classname, $parms )
	{
		global $model_factory, $server;
		 
		$result = array();
		 
		$this->login( $token );

		$object = $model_factory->getObject($classname);

		if ( !getFactory()->getAccessPolicy()->can_read($object) )
		{
			$server->fault('', $this->logError(text(549)));
			return;
		}

		$attrs = $this->getAttributes( $object );
		
		$values = array();

		foreach( array_keys($attrs) as $key )
		{
			$value = $this->soapValueToSystem( $attrs[$key]['type'], $parms[$key] );

			if ( $key == 'ReferenceName' && $value == '0' ) $value = '';
					 
			if ( $value != '' )
			{
				$values[$key] = $value;
			}
			else if ( $parms[$key] == '0' && $key == 'ParentPage' ) // workaround for looking root wiki pages
			{
			    $values[$key] = "NULL";
			}
		}

		if ( $values['Id'] != '' )
		{
			$it = $object->getExact( $values['Id'] );
			array_push( $result, $this->serializeToSoap( $it ) );
		}
		else
		{
			unset($values['Id']);
			unset($values['ClassName']);
			unset($values['Url']);

			$it = $object->getByRefArray( $values );

			while ( !$it->end() )
			{
				array_push( $result, $this->serializeToSoap( $it ) );
				$it->moveNext();
			}
		}
		 
		return $result;
	}

	//
	function getAttributes( $object )
	{
        if ( is_array($this->attributes[get_class($object)]) ) return $this->attributes[get_class($object)];

		$attrs = array();
		$attributes = array_keys($object->getAttributes());
		$system_attributes = $object->getAttributesByGroup('system');

		for( $i = 0; $i < count($attributes); $i++ )
		{
			if ( in_array($attributes[$i], $system_attributes) && !in_array($attributes[$i], array('DaysInWeek','WikiEditorClass','ContentEditor','UserField3','ReferenceName')) ) continue;
			if ( $object->getAttributeDbType($attributes[$i]) == '' ) continue;
            if ( in_array($attributes[$i], array('ArtifactsType')) ) continue;
			if ( $object->IsReference($attributes[$i]) )
			{
				switch ( $attributes[$i] )
				{
					case 'State':
						$type = 'xsd:string';
						break;

					default:
						$type = 'xsd:int';
				}
			}
			else
			{
				switch ( strtolower($object->getAttributeType($attributes[$i])) )
				{
					case 'file':
					case 'image':
						$type = 'xsd:base64Binary';
						break;
						 
					case 'date':
						$type = 'xsd:date';
						break;

					case 'datetime':
						$type = 'xsd:dateTime';
						break;

					case 'integer':
						$type = 'xsd:int';
						break;

					case 'float':
						$type = 'xsd:float';
						break;

					default:
						$type = 'xsd:string';
				}
			}

			$attrs[$attributes[$i]] =
				array (
			 			'name' => $attributes[$i],
			 			'type' => $type
				);
		}

		$attrs['Id'] = array(
			'name' => 'Id', 
			'type' => 'xsd:int' );
			
		$attrs['ClassName'] = array(
			'name' => 'ClassName', 
			'type' => 'xsd:string' );

		$attrs['RecordVersion'] = array(
			'name' => 'RecordVersion', 
			'type' => 'xsd:int' );
		
		$attrs['Url'] = array(
			'name' => 'Url', 
			'type' => 'xsd:string' );
			
		return $this->attributes[get_class($object)] = $attrs;
	}

	//
	function serializeToSoap( $object_it )
	{
		$attributes = array_keys($this->getAttributes($object_it->object));
		$data = array();

		for ( $i = 0 ; $i < count($attributes) ; $i++ )
		{
			if ( $object_it->object->IsReference($attributes[$i]) )
			{
				$items = preg_split('/,/', $object_it->get($attributes[$i]));
				
				if ( $items[0] > 0 )
				{
					$data[$attributes[$i]] = $items[0];
				}
				else
				{
					$data[$attributes[$i]] = "0";
				}
			}
			else
			{
				$data[$attributes[$i]] = $this->systemValueToSoap($object_it, $attributes[$i]);
			}
		}

        $data['Id'] = $object_it->getId();
        $data['ClassName'] = get_class($object_it->object);
        $data['RecordVersion'] = $object_it->get('RecordVersion') == ''
            ? '0' : $object_it->get('RecordVersion');

        $uid = new ObjectUID;
        if ( $uid->HasUid( $object_it ) ) {
            $info = $uid->getUIDInfo($object_it);
            $data['Url'] = $info['url'];
        }
        else {
            $data['Url'] = '';
        }

        return $data;
	}

	function systemValueToSoap( $object_it, $attr )
	{
		switch ( strtolower($object_it->object->getAttributeType($attr)) )
		{
			case 'file':
			case 'image':
				$path = $object_it->getFilePath( $attr );
				
				if ( file_exists($path) && !is_dir($path) )
				{
					$file = fopen($path, 'rb');
						
					$value = base64_encode(
						fread($file, filesize($path))
					);
						
					fclose($file);

				}
				
				return $value;

			case 'integer':
				$value = round($object_it->get($attr), 0);
				if ( $value == '' ) $value = "0";
				return $value;

			case 'float':
			    $value = round($object_it->get($attr), 2);
			    if ( $value == '' ) $value = "0.0";
			    return $value;
				
			case 'date':
				$match = array();
				$value = $object_it->get_native($attr);
				$pattern = '/([0-9]{4})-([0-9]{2})-([0-9]{2})/';
					
				if ( $value == "" )
				{
					return "0001-01-01";
				}
				else if ( preg_match($pattern, $value, $match) )
				{
					if ( $match[1] < 1 || $match[2] < 1 || $match[3] < 1 )
					{
						return "0001-01-01";
					}
					else
					{
						return $match[1].'-'.$match[2].'-'.$match[3];
					}
				}
				else
				{
					return "0001-01-01";
				}

			case 'datetime':
				$match = array();
				$value = $object_it->get_native($attr);
				$pattern = '/([0-9]{4})-([0-9]{2})-([0-9]{2})\s+([0-9]{2}):([0-9]{2}):([0-9]{2})/';
					
				if ( $value == "" )
				{
					return "0001-01-01T00:00:00";
				}
				else if ( preg_match($pattern, $value, $match) )
				{
					if ( $match[1] < 1 || $match[2] < 1 || $match[3] < 1 )
					{
						return "0001-01-01T00:00:00";
					}
					else
					{
						return $match[1].'-'.$match[2].'-'.$match[3].'T'.$match[4].':'.$match[5].':'.$match[6];
					}
				}
				else if ( preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/', $value, $match) )
				{
					if ( $match[1] < 1 || $match[2] < 1 || $match[3] < 1 )
					{
						return "0001-01-01T00:00:00";
					}
					else
					{
						return $match[1].'-'.$match[2].'-'.$match[3]."T00:00:00";
					}
				}
				else
				{
					return "0001-01-01T00:00:00";
				}
					
			default:
                $value = TextUtils::getXmlString($object_it->getHtmlDecoded($attr));

				if ( $object_it->object->IsReference($attr) ) {
					if ( $value == '' ) $value = "0";
				}
				
				return $value;
		}
	}

	function soapValueToSystem( $soap_type, & $value )
	{
		switch ( strtolower($soap_type) )
		{
			case 'xsd:date':
				$match = array();
				if ( preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/', $value, $match) )
				{
					if ( $match[1] < 2 )
					{
						return "";
					}
					else
					{
						return $match[1].'-'.$match[2].'-'.$match[3];
					}
				}
				else
				{
					return "";
				}

			case 'xsd:datetime':
				
			    $match = array();

				if ( preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})/mi', $value, $match) )
				{
					if ( $match[1] < 2 )
					{
						return "";
					}
					else
					{
						return $match[1].'-'.$match[2].'-'.$match[3].' '.$match[4].':'.$match[5].':'.$match[6];
					}
				}
				else
				{
					return $this->soapValueToSystem('xsd:date', $value);
				}
					
			case 'xsd:int':
			case 'xsd:float':
			    if ( $value <= 0 )
				{
					return "";
				}
				else
				{
					return $value;
				}

			case 'xsd:string':
				return $value;

			case 'xsd:base64binary':
				return $value;

			default:
				return $value;
		}
	}

	function exportEntity( $class, $namespace, & $server )
	{
	    $object = getFactory()->getObject($class);
	    
	    $server->wsdl->addComplexType(
	            $class,
	            'complexType',
	            'struct',
	            'sequence',
	            '',
	            $this->getAttributes( $object )
	    );

		if ( $this->getStyle() == 'document' ) {
			$server->wsdl->addComplexType(
					$class.'Array',
					'complexType',
					'array',
					'sequence',
					'',
					array(
                        'item' => array (
                            'minOccurs' => 0,
                            'maxOccurs' => 'unbounded',
                            'type' => $namespace.':'.$class
                        )
					),
					$namespace.':'.$class
			);
		}
		else {
			$server->wsdl->addComplexType(
					$class.'Array',
					'complexType',
					'array',
					'',
					'SOAP-ENC:Array',
					array(),
					array( array('ref'=>'SOAP-ENC:arrayType',
							'wsdl:arrayType'=>$namespace.':'.$class.'[]') ),
					$namespace.':'.$class
			);
		}
	}
	
	function dataService( $classes, $namespace, & $server )
	{
		global $soap;

		foreach ( $classes as $class )
		{
		    $this->exportEntity( $class, $namespace, $server );

			$server->register($class.$this->methodsDelimiter.'RemoteLoad',
			array(
					'token' => 'xsd:string', 
					'id' => 'xsd:int'
					),
					array('return' => $namespace.':'.$class),
					$namespace, $namespace.'#Load'.$class, $soap->getStyle(), $soap->getUse(), 'Load object data using the given ID'
					);

					$server->register($class.$this->methodsDelimiter.'RemoteAdd',
					array(
					'token' => 'xsd:string', 
					'parms' => $namespace.':'.$class
					),
					array('return' => $namespace.':'.$class),
					$namespace, $namespace.'#Add'.$class, $soap->getStyle(), $soap->getUse(), 'Appends new object with the given parms'
					);
						
					$server->register($class.$this->methodsDelimiter.'RemoteAddBatch',
					array(
					'token' => 'xsd:string', 
					'parms' => $namespace.':'.$class.'Array'
					),
					array('return' => $namespace.':'.$class.'Array'),
					$namespace, $namespace.'#AddBatch'.$class, $soap->getStyle(), $soap->getUse(), 'Appends array of given objects'
					);

					$server->register($class.$this->methodsDelimiter.'RemoteStore',
					array('token' => 'xsd:string',
					  'id' => 'xsd:int',
					  'parms' => $namespace.':'.$class ),
					array('return' => 'xsd:int'),
					$namespace, $namespace.'#Store'.$class, $soap->getStyle(), $soap->getUse(), 'Stores modified object with the given id and parms'
					);

					$server->register($class.$this->methodsDelimiter.'RemoteStoreBatch',
					array('token' => 'xsd:string',
					  'parms' => $namespace.':'.$class.'Array' ),
					array(),
					$namespace, $namespace.'#StoreBatch'.$class, $soap->getStyle(), $soap->getUse(), 'Stores array of objects'
					);

					$server->register($class.$this->methodsDelimiter.'RemoteDelete',
					array(
					'token' => 'xsd:string', 
					'id' => 'xsd:int' 
					),
					array(),
					$namespace, $namespace.'#Delete'.$class, $soap->getStyle(), $soap->getUse(), 'Stores modified object with the given id and parms'
					);

					$server->register($class.$this->methodsDelimiter.'RemoteDeleteBatch',
					array(
					'token' => 'xsd:string', 
					'parms' => $namespace.':'.$class.'Array'
					),
					array(),
					$namespace, $namespace.'#DeleteBatch'.$class, $soap->getStyle(), $soap->getUse(), 'Removes array of objects'
					);

					$server->register($class.$this->methodsDelimiter.'RemoteGetAll',
					array(
					'token' => 'xsd:string'
					),
					array('return' => $namespace.':'.$class.'Array'),
					$namespace, $namespace.'#GetAll'.$class, $soap->getStyle(), $soap->getUse(), 'Returns all records of the given type'
					);
						
					$server->register($class.$this->methodsDelimiter.'RemoteFind',
					array('token' => 'xsd:string',
					  'parms' => $namespace.':'.$class),
					array('return' => $namespace.':'.$class.'Array'),
					$namespace, $namespace.'#Find'.$class, $soap->getStyle(), $soap->getUse(), 'Searches for records of the given type using field value'
					);
		}

		$rawPost = EnvironmentSettings::getRawPostData();

        $rawPost = preg_replace('/&#(\d+);/', "", $rawPost ); #decimal notation
        $rawPost = preg_replace('/&#x([a-f0-9]+);/i', "", $rawPost); #hex notation

		$this->logInfo("REQUEST: ".$rawPost);

		ob_start();
		$server->service($rawPost);
		
		$result = ob_get_contents();
		ob_end_clean();
		
		$this->logInfo("RESPONSE: ".$result);
		echo $result;
	}

	function getStyle() {
		return $_REQUEST['style'] == 'rpc' ? 'rpc' : 'document';
	}

	function getUse() {
		return $_REQUEST['use'] == 'encoded' ? 'encoded' : 'literal';
	}

	function setMethodDelimiter( $value ) {
        $this->methodsDelimiter = $value;
    }

}
