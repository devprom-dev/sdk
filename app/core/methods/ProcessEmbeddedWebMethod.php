<?php
include_once "WebMethod.php";

class ProcessEmbeddedWebMethod extends WebMethod
{
     function decode( $value ) {
		 return EnvironmentSettings::getBrowserPostUnicode() ? IteratorBase::utf8towin($value) : $value;
     }
     
 	function execute_request()
 	{
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
		 	
			 $temp_file = getFactory()->getObject('cms_TempFile');
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
		 		$anchor = getFactory()->getObject($object_class);
		 		$anchor_it = $anchor->getExact($object_id);
	 		}

	 		$object = getFactory()->getObject($classname);
		 	$it = $object->createIterator();
	 			
	 		$fields = array_filter(preg_split('/,/', $_REQUEST['embeddedFields'.$i]), function($value) use($prefix) {
	 			return array_key_exists($prefix.$value, $_REQUEST); 
	 		});

	 		foreach ( $fields as $field ) {
	 		    $attrs[$field] = $this->decode($_REQUEST[$prefix.$field]);
	 		}

            $anchor_field = $_REQUEST['embeddedAnchor'.$i];
		 	$result = array();

            if ( is_object($anchor_it) )
	 		{
                try {
                    $attrs[$anchor_field] = $anchor_it->getId();
                    $object->setAttributeType($anchor_field, 'REF_'.get_class($anchor_it->object).'Id');

                    $it = getFactory()->createEntity($object, $attrs);
                    $result['id'] = $it->getId();
                }
                catch( \Exception $e ) {
                    echo \JsonWrapper::encode(array(
                        'error' => $e->getMessage()
                    ));
                    return;
                }
	 		}
	 		else
	 		{
                try {
                    $object->setAttributeRequired($anchor_field, false);
                    getFactory()->transformEntityData($object, $attrs);
                }
                catch( \Exception $e ) {
                    echo \JsonWrapper::encode(array(
                        'error' => $e->getMessage()
                    ));
                    return;
                }

                foreach( $attrs as $field => $data ) {
                    $attrs[$field] = htmlentities($data);
                    $attrs[$field] = $attrs[$field] == 'NULL' ? '' : $attrs[$field];
                }
	 		    $it->setData( $attrs );

	 			$result['id'] = 0; 
	 		}

	 		switch ( $object->getEntityRefName() )
	 		{
	 			case 'WikiPageFile':
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
	 			    $result['file'] = $it->get_native('ContentExt');
		 			$result['name'] = $it->get_native('ContentExt');
			 		$result['caption'] = $it->get_native('ContentExt').' ('.$it->getFileSizeKb('Content').' Kb)';
					$result['url'] = $it->getFileUrl();
					break;

	 			case 'pm_Attachment':
		 			$result['file'] = $it->get_native('FileExt');
		 			$result['name'] = $it->get_native('FileExt');
			 		$result['caption'] = $it->get_native('FileExt').' ('.$it->getFileSizeKb('File').' Kb)';
					$result['url'] = $it->getFileUrl();
					break;
	 		}

			echo $_REQUEST['callback'].JsonWrapper::encode($result);

	 		break;
		 }
 	}
}
