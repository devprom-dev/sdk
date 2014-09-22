<?php

 require_once (SERVER_ROOT_PATH.'ext/xml/xml2Array.php');
 include ('c_requestsimport.php');
 
 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
 class RequestsImportXml extends RequestsImport
 { 
 	function validate()
 	{
		global $_FILES, $_REQUEST, $model_factory;

		$this->checkRequired( array('object') );
		$this->request = $model_factory->getObject($_REQUEST['object']);

		// proceeds with validation
		if( !is_uploaded_file($_FILES['Excel']['tmp_name']) )
		{ 
			$this->replyError( $this->getResultDescription( 1 ) );
		}

		return true;
 	}
 	
 	function getObject()
 	{
 		return $this->request;
 	}

 	function getLines()
	{
		global $_FILES, $model_factory, $project_it;

		$xml2Array = new xml2Array();
				
		$file_name = $_FILES['Excel']['tmp_name'];
		$file = fopen($file_name, 'r');
		
		$items = $xml2Array->xmlParse( 
			fread($file, filesize($file_name)) );

		unlink($file_name);
		
		$lines = array();
		
		foreach ( $items['children'] as $children )
		{
			if ( $children['name'] == 'WORKSHEET' || $children['name'] == 'SS:WORKSHEET' )
			{
				foreach ( $children['children'] as $worksheet )
				{
					if ( $worksheet['name'] == 'TABLE' )
					{
						foreach ( $worksheet['children'] as $row )
						{
							if ( $row['name'] == 'ROW' )
							{
								$line = array();
								
								foreach ( $row['children'] as $cell )
								{
									if ( $cell['attrs']['SS:INDEX'] > 0 )
									{
										$line[$cell['attrs']['SS:INDEX'] - 1] = 
											$project_it->Utf8ToWin($cell['children'][0]['tagData']);
									}
									else
									{
										array_push( $line, 
											$project_it->Utf8ToWin($cell['children'][0]['tagData']) );
									}
								}

								array_push( $lines, $line );								
							}
						}
					}
				}
				
				break;
			}
		}

		return $lines;
	}
 }
