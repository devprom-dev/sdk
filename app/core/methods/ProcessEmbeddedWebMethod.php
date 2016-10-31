<?php

include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMapper.php";

include_once "WebMethod.php";

class ProcessEmbeddedWebMethod extends WebMethod
{
     function decode( $value )
     {
		 return EnvironmentSettings::getBrowserPostUnicode() ? IteratorBase::utf8towin($value) : $value;
     }
     
 	function execute_request()
 	{
 		 global $_REQUEST, $_FILES, $model_factory;

 		 $indexes = array();
 		 
         foreach( array_keys($_REQUEST) as $key )
         {
            if( preg_match('/embedded([\d]+)/', $key, $matches) )
            {
                $indexes[] = $matches[1];
            }
         }
 		 
		 foreach( $indexes as $form_index )
		 {
			 $class_name = strtolower($_REQUEST['embedded'.$form_index]);

			 if ( $class_name == '' ) continue;

			 switch ( $class_name )
			 {
				case 'wikipagefile':
				case 'blogpostfile':
					$file_field = 'Content';
					break;

				default:
					$file_field = 'File';
					break;
			 }
		 }

 		 $fields = array_keys($_FILES);
 		 
		 for ( $i = 0; $i < count($fields); $i++ )
		 {
			 if ( is_uploaded_file($_FILES[$fields[$i]]['tmp_name']) ) 
			 {
				$_FILES[$file_field] = $_FILES[$fields[$i]];
				
				break;
			 }
		 }
		 
		 if ( count($_FILES) > 0 )
		 {
		     $_FILES[$file_field]['name'] = $this->decode($_FILES[$file_field]['name']);
		 }
		 
		 $hasobject = false;
		 
		 foreach( $indexes as $form_index )
		 {
			$object_id = $_REQUEST['anchorObject'.$form_index];
			
			if ( $object_id != '' )
			{
				$hasobject = true;
				break;
			}
		 }

		 if ( !$hasobject && is_array($_FILES[$file_field]) )
		 {
		 	 $_FILES['File'] = $_FILES[$file_field];
		 	
			 $temp_file = $model_factory->getObject('cms_TempFile');
			 $tmp_pathinfo = pathinfo($_FILES[$file_field]['tmp_name']);
			 
			 $file_title = $_FILES[$file_field]['name'];
			
			 $file_uid = md5(microtime().$tmp_pathinfo['basename']);
				
			 $file_id = $temp_file->add_parms(
				array('Caption' => $file_uid,
					  'FileName' => $file_title,
					  'MimeType' => $_FILES[$file_field]['type']) 
				);
				
			 $file_it = $temp_file->getExact( $file_id );
			 
			 echo $_REQUEST['callback'].
				'{"file":"'.$file_uid.'",' .
				 '"name":"'.$file_title.'",' .
				 '"caption":"'.$file_title.' ('.$file_it->getFileSizeKb('File').' Kb)'.'",' .
				 '"url":"'.$file_it->getFileUrl().'",'.
				 '"id":"0"}';
			
			 return;
		 }
		  
		 foreach( $indexes as $i )
		 {
			$classname = $_REQUEST['embedded'.$i];
			$prefix = $_REQUEST['embeddedPrefix'.$i];
			
		 	if ( $classname == '' ) continue;

		 	$object_id = $_REQUEST['anchorObject'.$i];
			$object_class = $_REQUEST['anchorClass'.$i];
			
	 		if ( $object_class != '' && $object_id != '' )
	 		{
		 		$anchor = $model_factory->getObject($object_class);
		 		
		 		$anchor_it = $anchor->getExact($object_id);
	 		}

	 		$object = $model_factory->getObject($classname);
	 		 
		 	$it = $object->createIterator();
	 			
	 		$fields = array_filter(preg_split('/,/', $_REQUEST['embeddedFields'.$i]), function($value) use($prefix)
	 		{
	 			return array_key_exists($prefix.$value, $_REQUEST); 
	 		});

	 		foreach ( $fields as $field )
	 		{
	 		    $attrs[$field] = $this->decode($_REQUEST[$prefix.$field]);
	 		}

	 		if ( is_object($anchor_it) )
	 		{
				$anchor_field = $_REQUEST['embeddedAnchor'.$i];  
		 		
				$attrs[$anchor_field] = $anchor_it->getId();
				
				$object->setAttributeType($anchor_field, 'REF_'.get_class($anchor_it->object).'Id');
	 		}

		 	$result = array();

	 		if ( is_object($anchor_it) )
	 		{
	 			$mapper = new ModelDataTypeMapper();
	 			
	 			$mapper->map( $object, $attrs );

	 			$id = $object->add_parms( $attrs );

	 			$it = $object->getExact($id);
	 			
	 			$result['id'] = $id;
	 		}
	 		else
	 		{
	 		    foreach( $attrs as $field => $data )
	 		    {
	 			    $attrs[$field] = html_entity_decode($data, ENT_COMPAT | ENT_HTML401, APP_ENCODING);

	 			    $attrs[$field] = $attrs[$field] == 'NULL' ? '' : $attrs[$field];
	 		    }

	 			$mapper = new ModelDataTypeMapper();
	 			
	 			$mapper->map( $object, $attrs );

	 		    $it->setData( $attrs );

	 			$result['id'] = 0; 
	 		}

	 		switch ( $object->getEntityRefName() )
	 		{
	 			case 'WikiPageFile':
	 			case 'BlogPostFile':
	 			    break;

	 			case 'pm_Attachment':
					break;

	 			default:
	 			    
	 			    $uid = new ObjectUID;

	 				$result['caption'] = $it->getId() > 0
                        ? $uid->getUidWithCaption($it) : $it->getDisplayName();
	 		}
	 		
	 		switch ( $object->getEntityRefName() )
	 		{
	 			case 'WikiPageFile':
	 			case 'BlogPostFile':
	 			    $result['file'] = IteratorBase::wintoutf8($it->get_native('ContentExt'));
		 			$result['name'] = IteratorBase::wintoutf8($it->get_native('ContentExt'));
			 		$result['caption'] = IteratorBase::wintoutf8($it->get_native('ContentExt').' ('.$it->getFileSizeKb('Content').' Kb)');
					$result['url'] = IteratorBase::wintoutf8($it->getFileUrl());
					break;

	 			case 'pm_Attachment':
		 			$result['file'] = IteratorBase::wintoutf8($it->get_native('FileExt'));
		 			$result['name'] = IteratorBase::wintoutf8($it->get_native('FileExt'));
			 		$result['caption'] = IteratorBase::wintoutf8($it->get_native('FileExt').' ('.$it->getFileSizeKb('File').' Kb)');
					$result['url'] = IteratorBase::wintoutf8($it->getFileUrl());
					break;
	 		}

			echo $_REQUEST['callback'].JsonWrapper::encode($result);

	 		break;
		 }
 	}
}
