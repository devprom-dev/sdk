<?php

 define ('SHADOW_PASS', '************');

define( 'ORIGIN_METADATA', 'metadata' );
define( 'ORIGIN_COMPUTED', 'computed' );
 
include('c_iterator.php'); 
 include('c_iterator_union.php');
 
 include_once SERVER_ROOT_PATH."cms/classes/model/StoredObjectDB.php";

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class Object
 {
 	var $attributes = array();
	var $classname;
	var $defaultsort;
	var $model_factory;
	
	function createIterator()
    {
		return null;
	}
	
	function getExcelIterator( $parms ) 
	{
		return null;
	}
	
	function hasAttribute( $name )
	{
		return array_key_exists($name, $this->attributes);
	}
	
 	function addAttribute( $ref_name, $type, $caption, $b_visible, $b_stored = false, $description = '', $ordernum = 999 ) 
	{
		if ( $ordernum == 999 ) $ordernum = $this->getLatestOrderNum() + 10;
		
		$this->attributes[$ref_name] = array( 
				'caption' 		=> $caption,
				'visible' 		=> $b_visible, 
				'stored'  		=> $b_stored, 
				'description' 	=> $description, 
				'ordernum' 		=> $ordernum,
				'origin' 		=> ORIGIN_METADATA
		);
		
		$this->setAttributeType($ref_name, $type);
	}
	
	function removeAttribute( $attr )
	{
		unset($this->attributes[$attr]);
	}
	
	function getIdAttribute()
	{
		return $this->getEntityRefName().'Id';
	}
	
 	function getAttributeType( $name ) 
 	{
 		switch ( strtolower($name) )
 		{
 			case strtolower($this->getClassName().'Id'):
 				return 'integer';
 				
 			default:
				return strtolower($this->attributes[$name]['type']);
 		}
	}

 	function getAttributeDbType( $name ) 
 	{
		return $this->attributes[$name]['dbtype'];
	}

 	function getAttributeDbName( $name ) {
		return $name;
	}

	function getAttributeVisualType( $name ) 
	{
		return $this->getAttributeType( $name );
	}
	
	function getAttributeTypeName( $name ) 
	{
		switch ( $this->getAttributeType( $name ) )
		{
			case 'integer':
				return translate('число');

			case 'float':
				return translate('число с запятой');

			case 'date':
			case 'datetime':
				return translate('дата');
				
			case 'char':
				return translate('булевое значение');

			case 'text':
			case 'varchar':
				return translate('текст');
				
			default:
				return '';
		}
	}

 	function getAttributeUserName( $name ) 
 	{
		return preg_replace_callback ( '/text\(([a-zA-Z\d]+)\)/i', iterator_text_callback,
			html_entity_decode($this->attributes[$name]['caption'], ENT_COMPAT | ENT_HTML401, APP_ENCODING)
		);
	}
	
 	function getAttributeOrderNum( $name ) 
 	{
		return $this->attributes[$name]['ordernum'];
	}
	
 	function getAttributeDescription( $name ) 
 	{
		return preg_replace_callback (
			'/text\(([a-zA-Z\d]+)\)/i', iterator_text_callback, $this->attributes[$name]['description'] 
		);
	}
	
	function getAttributeGroups( $name )
	{
		return $this->attributes[$name]['groups'];
	}
	
	function getAttributesByGroup( $group )
	{
		$attributes = array_filter( $this->attributes, function($value) use ($group) 
		{
			return is_array($value['groups']) && in_array($group, $value['groups']);
		});
		
		return array_keys($attributes);
	}

	 function getAttributesByOrigin( $origin ) {
		 $attributes = array_filter( $this->attributes, function($value) use ($origin) {
			 return $value['origin'] == $origin;
		 });
		 return array_keys($attributes);
	 }

	 function getAttributeByCaption( $caption ) {
		 $attributes = array_filter( $this->attributes, function($value) use ($caption) {
			 return $value['caption'] == $caption;
		 });
		 return array_shift(array_keys($attributes));
	 }

	 function getAttributesByType( $type ) {
		 $attributes = array_filter( $this->attributes, function($value) use ($type) {
			 return strcasecmp($value['type'], $type) === 0;
		 });
		 return array_keys($attributes);
	 }

 	//----------------------------------------------------------------------------------------------------------
	function getAttributeOrigin( $name ) 
	{
		return $this->attributes[$name]['origin'];
	}
	
	//----------------------------------------------------------------------------------------------------------
	function attributesHasOrigin( $origin ) 
	{
		foreach( $this->attributes as $key => $value )
		{
			if ( $value['origin'] == $origin ) return true;
		}

		return false;
	}
	
 	function IsAttributeRequired( $name ) 
	{
		return $this->attributes[$name]['required'];
	}
	
 	function getDefaultAttributeValue( $name ) 
	{
		return $this->attributes[$name]['default'];
	}
	
  	function setAttributeOrigin( $ref_name, $value )
	{
	    if ( !array_key_exists($ref_name, $this->attributes) ) return;
		
	    $this->attributes[$ref_name]['origin'] = $value;
	}
	
	function setAttributeDefault( $ref_name, $value )
	{
	    if ( !array_key_exists($ref_name, $this->attributes) ) return;
		
	    $this->attributes[$ref_name]['default'] = $value;
	}
	
	function setAttributeRequired( $ref_name, $value )
	{
	    if ( !array_key_exists($ref_name, $this->attributes) ) return;
		
	    $this->attributes[$ref_name]['required'] = $value;
	}
	
	function setAttributeGroups( $name, array $groups )
	{
	    if ( !array_key_exists($name, $this->attributes) ) return;
		
	    $this->attributes[$name]['groups'] = $groups;
	}
	
	function addAttributeGroup( $name, $group )
	{ 
	    if ( !array_key_exists($name, $this->attributes) ) return;
		
		if ( !isset($this->attributes[$name]['groups']) )
		{
			$this->attributes[$name]['groups'] = array();
		}
		
		$this->attributes[$name]['groups'][] = $group;
	}
	
	function setAttributeCaption( $ref_name, $caption )
	{
	    if ( !array_key_exists($ref_name, $this->attributes) ) return;
	    
		$this->attributes[$ref_name]['caption'] = $caption;
	}
	
	function setAttributeDescription( $ref_name, $text )
	{
	    if ( !array_key_exists($ref_name, $this->attributes) ) return;
	    
	    $this->attributes[$ref_name]['description'] = $text;
	}
	
	function setAttributeVisible( $ref_name, $visible )
	{
	    if ( !array_key_exists($ref_name, $this->attributes) ) return;
	    
	    $this->attributes[$ref_name]['visible'] = $visible;
	}
	
	function setAttributeOrderNum( $ref_name, $value )
	{
	    if ( !array_key_exists($ref_name, $this->attributes) ) return;
	    
	    $this->attributes[$ref_name]['ordernum'] = $value;
	}
	
 	function setAttributeStored( $ref_name, $value )
	{
	    if ( !array_key_exists($ref_name, $this->attributes) ) return;
	    
	    $this->attributes[$ref_name]['stored'] = $value;
	}
	
  	function setAttributeType( $ref_name, $value )
	{
	    if ( !array_key_exists($ref_name, $this->attributes) ) return;
	    
		$datatype = $value;
		
		if ($datatype == 'RICHTEXT') $datatype = 'TEXT';
		if ($datatype == 'LARGETEXT') $datatype = 'TEXT';
		
	    
	    $this->attributes[$ref_name]['dbtype'] = $value;
	    $this->attributes[$ref_name]['type'] = $datatype;
	}
	
	function isAttributeVisible( $name ) 
	{
		return $this->attributes[$name]['visible'];
	}

	function IsAttributeStored( $name ) 
	{
		return $this->attributes[$name]['stored'];
	}

	function getAttributes() 
	{
		return $this->attributes;
	}

	 function getAttributesVisible()
	 {
		 return array_keys(
			 array_map(
				 function($attribute) {
					 return $attribute['visible'];
			 	 },
				 $this->attributes
			 )
		 );
	 }

 	function setAttributes( $attributes )
	{
		$this->attributes = $attributes;
	}
	
	function getAttributesSorted()
	{
		$result = $this->getAttributes();
		
 		uasort( $result, "attribute_sort_ordernum" );
 		
 		return $result;
	}
	
	
	function getLatestOrderNum()
	{
		$latest = 0;
		
		foreach( $this->attributes as $attribute )
		{
			if ( $attribute['ordernum'] > $latest ) $latest = $attribute['ordernum'];  
		}
		
		return $latest;
	}
	
	function getClassName() {
		return strtolower(get_class($this));
	}

 	function getDisplayName() {
		return strtolower(get_class($this));
	}
	
	function getPageName() {
		global $_REQUEST;
		$offset = $_REQUEST['offset1'];
		return 'object.php?class='.$this->getClassName().(isset($offset) ? '&offset='.$offset : '');
	}

	function getPageNameObject( $object_id = '' ) {
		return $this->getPageName().'&'.$this->getEntityRefName().'Id='.$object_id;
	}

	function getPageNameEditMode( $objectid ) {
		return $this->getPageNameObject( $objectid ).'&'.$this->getEntityRefName().'action=show';
	}

	function getPageNameViewMode( $objectid ) {
		return $this->getPageNameObject( $objectid ).'&'.$this->getEntityRefName().'action=view';
	}

	function getPageNameCreateLike( $objectid ) {
		return $this->getPageNameObject( $objectid ).'&'.$this->getEntityRefName().'action=createlike';
	}
	
	function isOrdered() {
		return false;
	}
	
	function createDefaultView() {
		return new ViewBasic( $this );
	}
	
	function createForm() {
		return new Form( $this );
	}

	function createListForm() {
		return new ListForm( $this );
	}

	 // TODO: obsolete
	function Utf8ToWin($fcontents) 
	{
		return $fcontents;
	}
 }

 //////////////////////////////////////////////////////////////////////////////////////////////
 function attribute_sort_ordernum( $left, $right )
 {
 	return $left['ordernum'] > $right['ordernum'] ? 1 : -1;
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 //-----------------------------------------------------------------------------------------------------//
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class AggregatedObjectDB extends StoredObjectDB
 {
 	var $container_it;
	
	//----------------------------------------------------------------------------------------------------------
	function __construct( $container_it, ObjectRegistrySQL $registry = null )
	{
		$this->container_it = $container_it;
		
		parent::__construct( $registry );
	}
	
	public function & getContainer()
	{
		return $this->container_it;
	}
	
	//----------------------------------------------------------------------------------------------------------
	function getAll() 
	{
		return $this->getByRef( $this->container_it->object->getEntityRefName().'Id', $this->container_it->getId() );
	}

	//----------------------------------------------------------------------------------------------------------
	function getPageName() {
		$page = $this->container_it->object->getPageName();
		return $page.(strpos($page, '?') > 0 ? '&' : '?').$this->container_it->object->getClassName().'Id='.
					$this->container_it->getId().'&'.$this->container_it->object->getClassName().'action=show';
	}
 }

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class SingletonDB extends StoredObjectDB
 {
 	function Install()
	{
		parent::Install();
		parent::add();
	}
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class FileStoring
 {
 	var $object;
 	function FileStoring( $object ) {
		$this->object = $object;
	}
	function getDataDefinition( $name ) {}
	function readFile( $name, $it ) {}
	function getCheckSum( $name, $it ) {}
	function getSizeKb( $name, $it ) {}
	function getSizeMb( $name, $it ) {}
 	function storeFile( $name, $it ) {}
	function removeFile( $name, $it ) {}
 	function copyFile( $name, $source_it, $dest_it ) {}
	function getFilePath( $name, $it ) {}
	function createFilePath( $name, $it ) {}
	function getFileName( $name, $it ) {
		return $name.$it->getId();
	}
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class FileStoringFS extends FileStoring
 {
	//----------------------------------------------------------------------------------------------------------
 	function getDataDefinition( $name ) {
		return $name."Mime TEXT, ".$name."Path TEXT, ".$name."Ext VARCHAR(32)";
	}

	//----------------------------------------------------------------------------------------------------------
	function readFile( $name, $it )
	{
		$filepath = $this->getFilePath($name, $it);
		$file = fopen( $filepath, "r" );
		echo fread( $file, filesize($filepath));
	}

	//----------------------------------------------------------------------------------------------------------
	function getCheckSum( $name, $it )
	{
		$filepath = $this->getFilePath($name, $it);
		$file = fopen( $filepath, "r" );
		return abs(crc32(fread( $file, filesize($filepath))));
	}

	//----------------------------------------------------------------------------------------------------------
	function getSizeKb( $name, $it )
	{
		$filepath = $this->getFilePath($name, $it);
		return round(filesize($filepath) / 1024, 1);
	}

	//----------------------------------------------------------------------------------------------------------
	function getSizeMb( $name, $it )
	{
		return round($this->getSizeKb( $name, $it ) / 1024, 1);
	}
	
	//----------------------------------------------------------------------------------------------------------
 	function storeFile( $name, $it ) 
	{
		global $_FILES, $_REQUEST, $model_factory;

		if( $_REQUEST[$name.'ToDelete'] == 'Y') {
			$this->removeFile( $name, $it );
		}
		elseif(is_uploaded_file($_FILES[$name]['tmp_name']) || file_exists($_FILES[$name]['tmp_name']) ) 
		{
			if ( $_FILES[$name]['name'] == '' )
			{
				$file_name = 'unnamed';
			}
			else
			{
				$file_name = $_FILES[$name]['name'];
				
				$file_name = preg_replace('/\[/', '(', $file_name);
				$file_name = preg_replace('/\]/', ')', $file_name);
			}
			
			// каждый файл размещается в подкаталоге с именем класса
			$filepath = $this->createFilePath($name, $it);

			// копируем файл в подкаталог
			copy( $_FILES[$name]['tmp_name'], $filepath);
			
    		$sql = "UPDATE ".$this->object->getClassName()." SET ".$name."Path = '".
    			DAL::Instance()->Escape(addslashes($filepath))."', 
    			".$name."Mime = '".DAL::Instance()->Escape(addslashes($_FILES[$name]['type']))."',
				".$name."Ext = '".DAL::Instance()->Escape(addslashes($file_name)).
				"' WHERE ".$this->object->getClassName()."Id = ".$it->getId();

    		$r2 = DAL::Instance()->Query($sql);

			getFactory()->resetCachedIterator($it->object);

			unlink($_FILES[$name]['tmp_name']);
		}
	}
	
	//----------------------------------------------------------------------------------------------------------
	function removeFile( $name, $it )
	{
		$filepath = $this->getFilePath($name, $it);
		if ( $filepath != '' && file_exists($filepath) ) unlink($filepath);
	}

	//----------------------------------------------------------------------------------------------------------
 	function copyFile( $name, $source_it, $dest_it )
	{
		// формируем новое имя файла
		$filepath = $this->createFilePath($name, $dest_it);
		// определим путь к копируемому файлу
		$src_filepath = $this->getFilePath($name, $source_it);

		copy( $src_filepath, $filepath );

		$sql = "UPDATE ".$this->object->getClassName()." SET ".$name."Path = '".$filepath."' WHERE ".$this->object->getClassName()."Id = ".$dest_it->getId();
		
		$r2 = DAL::Instance()->Query($sql);
	}

	//----------------------------------------------------------------------------------------------------------
 	function copyFileOnPath( $name, $it, $dest_path )
	{
		// определим путь к копируемому файлу
		$src_filepath = $this->getFilePath($name, $it);
		// копируем файл
		copy( $src_filepath, $dest_path );
	}

	//----------------------------------------------------------------------------------------------------------
 	function copyFileExt( $name, $src_file_path, $dest_objectid )
	{
		// формируем новое имя файла
		$filepath = $this->createFilePath($name, $dest_objectid);

		// определим путь к копируемому файлу
		copy( $src_file_path, $filepath );

		$pathinfo = pathinfo($src_file_path);			
		switch(strtolower($pathinfo['extension'])) {
			case 'jpg':
				$mime = 'image/jpeg';
				break;
			case 'gif':
				$mime = 'image/gif';
				break;
			case 'pdf':
				$mime = 'application/pdf';
				break;
			case 'doc':
				$mime = 'application/msword';
				break;
			default:
				$mime = 'image/jpeg';
		}
		
		$sql = "UPDATE ".$this->object->getClassName()." SET ".$name."Path = '".$filepath."', 
					   ".$name."Mime = '".$mime."',
		   			   ".$name."Ext = '".$pathinfo['basename']."' WHERE ".$this->object->getClassName()."Id = ".$dest_objectid;
		
		$r2 = DAL::Instance()->Query($sql);
	}
 
	//----------------------------------------------------------------------------------------------------------
	function createFilePath ($name, $it )
	{
		if(!file_exists(SERVER_FILES_PATH)) mkdir(SERVER_FILES_PATH);
		
		$filepath = SERVER_FILES_PATH.$this->object->getClassName();
		if(!file_exists($filepath)) mkdir($filepath);
		
		$filepath .= '/'.$name.$it->getId();
		
		return $filepath;
	}
 
	//----------------------------------------------------------------------------------------------------------
	function getFilePath ($name, $it )
	{
		if ( $it->getId() < 1 ) return '';
		if ( $it->get($name.'Path') == '' ) return '';
		
		if ( file_exists($it->get($name.'Path')) ) return $it->get($name.'Path'); 
		return SERVER_FILES_PATH.$this->object->getClassName().'/'.basename($it->get($name.'Path'));
	}

	//----------------------------------------------------------------------------------------------------------
	function getFileName( $name, $it ) 
	{
		return $it->get($name.'Ext');
	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////

 

 //////////////////////////////////////////////////////////////////////////////////////////////
 
 //////////////////////////////////////////////////////////////////////////////////////////////


 //////////////////////////////////////////////////////////////////////////////////////////////
 
 //////////////////////////////////////////////////////////////////////////////////////////////

 
 //////////////////////////////////////////////////////////////////////////////////////////////

 //////////////////////////////////////////////////////////////////////////////////////////////

 //////////////////////////////////////////////////////////////////////////////////////////////

 //////////////////////////////////////////////////////////////////////////////////////////////

 //////////////////////////////////////////////////////////////////////////////////////////////

 //////////////////////////////////////////////////////////////////////////////////////////////
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 
 //////////////////////////////////////////////////////////////////////////////////////////////

 
 //////////////////////////////////////////////////////////////////////////////////////////////

 //////////////////////////////////////////////////////////////////////////////////////////////
 
 //////////////////////////////////////////////////////////////////////////////////////////////

 //////////////////////////////////////////////////////////////////////////////////////////////
 
 //////////////////////////////////////////////////////////////////////////////////////////////

 //////////////////////////////////////////////////////////////////////////////////////////////
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class AggregateBase
 {
 	var $object, $alias, $attribute, $agg_attribute, $aggregate, $group_function;
 	
 	function AggregateBase( $attribute, $agg_attribute = '1', $aggregate = 'COUNT' )
 	{
 		$this->alias = 't';
 		
 		$this->attribute = DAL::Instance()->Escape($attribute);
 		$this->aggregate = DAL::Instance()->Escape($aggregate);
 		$this->agg_attribute = DAL::Instance()->Escape($agg_attribute);
 	}
 	
 	function setObject( $object )
 	{
 		$this->object = $object;
 		
 	 	if ( in_array($this->object->getAttributeType($this->attribute), array('date', 'datetime')) && $this->group_function == '' )
 		{
 			$this->setGroupFunction('UNIX_TIMESTAMP');
 		}
 	}
 	
 	function getObject()
 	{
 		return $this->object;
 	}

 	function setAlias( $alias )
 	{
 		$this->alias = $alias;
 	}

 	function setGroupFunction( $function )
 	{
 		$function = strtolower(DAL::Instance()->Escape($function));
 		
 		switch ( $function )
 		{
 			case 'to_days':
 			case 'week':
 			case 'month':
 			case 'quarter':
 			case 'year':
                $this->group_function = $function.'(';
                break;

 			case 'unix_timestamp':
 				$this->group_function = '(SELECT @row_num:=@row_num+1) + '.$function.'(';
				break;

 			default:
 			    if ( preg_match('/extract/', $function) )
 			    {
 			        $this->group_function = $function;
 			    }
 			    else
 			    {
 			        return '';
 			    }
 		}
 	}
 	
 	function getAlias()
 	{
 		return $this->alias;
 	}

	function getAttribute()
	{ 	
		return $this->attribute;
	}
	
	function getAggregate()
	{ 	
		return $this->aggregate;
	}

	function getAggregatedAttribute()
	{ 	
		return $this->agg_attribute != '1'
			? $this->agg_attribute : '';
	}

	function getAggregatedInnerColumn()
	{ 	
		return $this->getAggregatedAttribute() == '' 
			? '' : ($this->getAlias() != '' ? $this->getAlias().'.'.$this->getAggregatedAttribute() : $this->getAggregatedAttribute());
	}
	
	function getAggregateAlias()
	{ 	
		return 'column'.md5($this->attribute.$this->aggregate.$this->agg_attribute);
	}

 	function getColumn()
 	{
 		return $this->getAlias() != '' ? $this->getAlias().'.'.$this->attribute : $this->attribute;
 	}

 	function getInnerColumn()
 	{
 		$computed = $this->object->getAttributeDbType($this->getAttribute()) != '' && !$this->object->IsAttributeStored($this->getAttribute());

 	 	if ( $computed )
 		{
 			return $this->attribute; 
 		}
 		else
 		{
	 		return $this->group_function != '' 
	 			? $this->group_function.$this->getColumn().') '.$this->attribute : $this->getColumn(); 
 		}
 	}
 	
 	function getAggregateColumn()
 	{
 		switch ( strtolower($this->aggregate) )
 		{
 			case 'none':
 				return $this->agg_attribute != '1' 
 					? $this->agg_attribute.' '.$this->getAggregateAlias() : '';
 				
 			case 'count':
 			case 'sum':
 			case 'max':
 			case 'min':
 			case 'avg':
 			case 'group_concat':
 				return $this->aggregate.'('.$this->agg_attribute.') '.$this->getAggregateAlias();
 				
 			default:
 				return '1';
 		}
 	}
 }
 