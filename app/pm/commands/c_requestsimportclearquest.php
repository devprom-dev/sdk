<?php

 include ('c_requestsimport.php');
 
 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
 class RequestsImportClearQuest extends RequestsImport
 { 
 	function getObject()
 	{
 		return $this->request;
 	}
 	
 	function validate()
 	{
		global $_FILES, $_REQUEST, $model_factory, $part_it;

		$this->request = $model_factory->getObject('pm_ChangeRequest');

		// check authorization was successfull
		if ( $part_it->getId() == GUEST_UID )
		{
			return false;
		}
		
		// proceeds with validation
		if( !is_uploaded_file($_FILES['Defects']['tmp_name']) )
		{ 
			$this->replyError( $this->getResultDescription( 1 ) );
		}

		if( !is_uploaded_file($_FILES['Users']['tmp_name']) )
		{ 
			$this->replyError( $this->getResultDescription( 1 ) );
		}
		
		if ( $_REQUEST['ClearQuest'] == 'CQ6' )
		{
			if( !is_uploaded_file($_FILES['AttachmentsDesc']['tmp_name']) )
			{ 
				$this->replyError( $this->getResultDescription( 1 ) );
			}
	
			if( !is_uploaded_file($_FILES['History']['tmp_name']) )
			{ 
				$this->replyError( $this->getResultDescription( 1 ) );
			}
		}

		return true;
 	}

 	function getFields()
 	{
 		$fields = parent::getFields();
 		
 		unset($fields[2]);
 		$fields = array_values($fields);

 		array_push($fields, 'ObjectChangeLog');
 		array_push($fields, 'Comments');
		array_push($fields, 'Owner');

 		array_push($fields, 'Attachments');

 		return 	$fields;
 	}
 	
	function getCaptions()
	{
		$captions = parent::getCaptions();

 		unset($captions[2]);
 		$captions = array_values($captions);

		array_push($captions, translate('История изменений'));
		array_push($captions, translate('Комментарии'));
		array_push($captions, translate('Ответственный'));

		array_push($captions, translate('Приложения'));

		return $captions;
	}

	function createDependencies( $request_id, $parms )
	{
		global $model_factory, $_FILES;
		
		$participant = $model_factory->getObject('pm_Participant');
		$participant_it = $participant->getExact($parms['Author']);
		
		$request = $model_factory->getObject('pm_ChangeRequest');
		$request_it = $request->getExact($request_id);

		if ( $request_it->count() < 1 )
		{
			return;
		}
		
		if ( $parms['Comments'] != '' )
		{
			// select a recent date of changes to use it as comments date
			$created_date = '';
			
			foreach ( $parms['ObjectChangeLog'] as $change )
			{
				if ( $change['Date'] > $created_date )
				{
					$created_date = $change['Date']; 
				}
			}

			$comment_parms = array(
				'AuthorId' => $participant_it->get('SystemUser'),
			  	'ObjectId' => $request_it->getId(),
	 			'ObjectClass' => get_class($request_it->object),
	 			'Caption' => $parms['Comments']
			);
			
			if ( $created_date != '' )
			{
				$comment_parms = array_merge($comment_parms,
					array( 'RecordCreated' => $created_date,
						   'RecordModified' => $created_date )
					);
			}
			
			$comment = $model_factory->getObject('Comment');
			if ( !$this->useNotification() )
			{
				$comment->removeNotificator( 'EmailNotificator' );
			}
			
			$comment->add_parms( $comment_parms );
		}
		
		if ( count($parms['ObjectChangeLog']) > 0 )
		{
			$changelog = $model_factory->getObject('ObjectChangeLog');
			if ( !$this->useNotification() )
			{
				$changelog->removeNotificator( 'EmailNotificator' );
			}

			foreach ( $parms['ObjectChangeLog'] as $change )
			{
				$participant_it = $participant->getByRef('Caption',
					$change['User']);
				
				$changelog->add_parms(
					array ( 'Caption' => $request_it->getDisplayNameHtmlDecoded(),
							'ObjectId' => $request_id,
							'ClassName' => strtolower(get_class($request_it->object)),
							'EntityName' => $request_it->object->getDisplayName(),
							'ChangeKind' => 'modify',
							'Author' => $participant_it->getId(),
							'Content' => $change['Content'],
							'VisibilityLevel' => 1,
							'SystemUser' => $participant_it->get('SystemUser'),
							'RecordCreated' => $change['Date'],
							'RecordModified' => $change['Date'] ) );
			}
		}
		
		if ( count($parms['Attachments']) > 0 )
		{
			$attachment = $model_factory->getObject('pm_Attachment');
			if ( !$this->useNotification() )
			{
				$attachment->removeNotificator( 'EmailNotificator' );
			}

			foreach ( $parms['Attachments'] as $attachment_item )
			{
				$_FILES['File']['tmp_name'] = $this->files_path.$attachment_item['File'];
				$_FILES['File']['name'] = basename($attachment_item['File']);
				
				$info = pathinfo($attachment_item['File']);
				switch ( strtolower($info['extension']) )
				{
					case 'jpg':
					case 'jpeg':
						$mime = 'image/jpeg';
						break;
					case 'png':
						$mime = 'image/png';
						break;
					case 'gif':
						$mime = 'image/gif';
						break;
					case 'bmp':
						$mime = 'image/bmp';
						break;
					default:
						$mime = '';
				}
				
				$_FILES['File']['type'] = $mime;
				
				$attachment->add_parms(
					array ( 'ObjectId' => $request_it->getId(),
						    'ObjectClass' => $request_it->object->getClassName() ) );
			}
		}
	}

	function parseState( $value, $parms )
	{
		global $project_it;
		
		$parms = array_merge($parms,
			array( 'PlannedRelease' => $this->release_it->getId() ) );
			
		$parms = array_merge($parms,
			array( 'SubmittedVersion' => $this->submitted_version ) );

		switch ( trim($value) )
		{
			case translate('Закрыто'):

				$parms = array_merge($parms,
					array( 'State' => 'resolved' ) );

				break;

			case translate('Решено'):
				
				$iteration_it = $this->release_it->getRecentIterationIt();

				$parms = array_merge($parms,
					array( 'State' => 'resolved' ) );

				if ( $iteration_it->count() > 0 )
				{
					$parms = array_merge($parms,
						array( 'Release' => $iteration_it->getId() ) );
				}

				break;
		}
		
		return $parms;
	}

 	function getLines()
	{
		global $_REQUEST, $_FILES, $model_factory, $project_it;

		// determine release and submitted version
		$release = $model_factory->getObject('pm_Version');
		$this->release_it = $release->getExact($_REQUEST['Release']);
		
		if ( $this->release_it->count() < 1 )
		{
			$this->replyError( $this->getResultDescription( 1 ) );
		}
		
		$this->submitted_version = $this->release_it->getDisplayName();
		
		// determine attachments path
		$this->files_path = $_REQUEST['Attachments'];
		
		// determine delimiters
		if ( $_REQUEST['Delimiter'] == '44' )
		{
			$fields_delimiter = '","';
		}
		else
		{
			$fields_delimiter = chr($_REQUEST['Delimiter']);
		}
		$line_delimiter = chr(13).chr(10);

		// parse User entity
		$file_name = $_FILES['Users']['tmp_name'];
		$file = fopen($file_name, 'r');

		$content = fread( $file, filesize($file_name) );
		unlink($file_name);
		
		$result = $this->parseEntityValues( $content, $line_delimiter, 
			$fields_delimiter, array() );
		
		$entity_values = $result[0];
		$fields_keys = $result[1];
		
		$this->users_map = array();
		$user = $model_factory->getObject('cms_User');

		foreach ( $entity_values as $values )
		{
			$user_it = $user->getByRef('Email', $values[$fields_keys['email']]);
			
			if ( $user_it->count() > 0 )
			{
				$this->users_map[$values[$fields_keys['login_name']]] = 
					$user_it->getDisplayName();
			}
		}
		
		// CQ6 specific parsing
		if ( $_REQUEST['ClearQuest'] == 'CQ6' )
		{
			// parse history records
			$this->changes = array();

			$file_name = $_FILES['History']['tmp_name'];
			$file = fopen($file_name, 'r');
	
			$content = fread( $file, filesize($file_name) );
			unlink($file_name);
			
			$result = $this->parseEntityValues( $content, $line_delimiter, 
				$fields_delimiter, array() );
			
			$entity_values = $result[0];
			$fields_keys = $result[1];
			
			foreach ( $entity_values as $line )
			{
				if ( !is_array($this->changes[$line[$fields_keys['display_name']]]) )
				{
					$this->changes[$line[$fields_keys['display_name']]] = array();
				}
				
				$date_parts = array();
				preg_match('/([a-zA-Z]{3})\s+([\d]+)\s+([\d]{4})\s+([\d]+):([\d]+)([PAM]{2})/i',
					$line[$fields_keys['action_timestamp']], $date_parts );
				
				switch ( $date_parts[1] )
				{
					case 'Jan':
						$date_parts[1] = 1;
						break;
					case 'Feb':
						$date_parts[1] = 2;
						break;
					case 'Mar':
						$date_parts[1] = 3;
						break;
					case 'Apr':
						$date_parts[1] = 4;
						break;
					case 'May':
						$date_parts[1] = 5;
						break;
					case 'Jun':
						$date_parts[1] = 6;
						break;
					case 'Jul':
						$date_parts[1] = 7;
						break;
					case 'Aug':
						$date_parts[1] = 8;
						break;
					case 'Sep':
						$date_parts[1] = 9;
						break;
					case 'Oct':
						$date_parts[1] = 10;
						break;
					case 'Nov':
						$date_parts[1] = 11;
						break;
					case 'Dec':
						$date_parts[1] = 12;
						break;
				}
				
				if ( $date_parts[6] == 'PM' && $date_parts[4] < 12 )
				{
					$date_parts[4] += 12;
				}
					
				array_push( $this->changes[$line[$fields_keys['display_name']]],
					array( 'Date' => $date_parts[3].'-'.$date_parts[1].'-'.
										$date_parts[2].' '.$date_parts[4].':'.$date_parts[5],
						   'User' => $line[$fields_keys['user_name']],
						   'Content' => translate('Действие').': '.$line[$fields_keys['action_name']].chr(10).
						   		translate('Предыдущее состояние').': '.$line[$fields_keys['old_state']].chr(10).
						   		translate('Новое состояние').': '.$line[$fields_keys['new_state']]
					)
				);
			} 
			
			// parse attachments records
			$this->attachments = array();

			$file_name = $_FILES['AttachmentsDesc']['tmp_name'];
			$file = fopen($file_name, 'r');
	
			$content = fread( $file, filesize($file_name) );
			unlink($file_name);
			
			$result = $this->parseEntityValues( $content, $line_delimiter, 
				$fields_delimiter, array() );
			
			$entity_values = $result[0];
			$fields_keys = $result[1];
			
			foreach ( $entity_values as $line )
			{
				$this->attachments[$line[$fields_keys['display_name']]] = array();
				$files = preg_split('/,/', $line[$fields_keys['Attachments']]);
				
				foreach ( $files as $file )
				{
					array_push( $this->attachments[$line[$fields_keys['display_name']]],
						array( 'File' => $line[$fields_keys['dbid']].'/'.basename($file)) 
						);
				}
			} 
			
			$substructure_fieldsamount = array();
		}
		else
		{
			$substructure_fieldsamount = array(
				'history' => 5
				);
		}
		
		// parse Defect entity
		$file_name = $_FILES['Defects']['tmp_name'];
		$file = fopen($file_name, 'r');

		$content = fread( $file, filesize($file_name) );
		unlink($file_name);
		
		$result = $this->parseEntityValues( $content, $line_delimiter, 
			$fields_delimiter, $substructure_fieldsamount );
		
		$entity_values = $result[0];
		$fields_keys = $result[1];
		$issue_lines = array( $this->getCaptions() );
		
		foreach ( $entity_values as $values )
		{
			if ( $values[$fields_keys['id']] == '' )
			{
				continue;
			}
			
			// parse history field to transform it to changes log
			$history = $values[$fields_keys['history']];
			
			if ( is_array($this->changes) )
			{
				$changes = $this->changes[$values[$fields_keys['id']]];
			}
			else
			{
				$changes = array();
				$keys = array_keys($history);
				
				foreach ( $keys as $key )
				{
					array_push($changes,
						array( 'Date' => $history[$key][0],
							   'User' => $this->users_map[$history[$key][1]],
							   'Content' => translate('Действие').': '.$history[$key][2].chr(10).
							   		translate('Исходное состояние').': '.$history[$key][3].chr(10).
							   		translate('Целевое состояние').': '.$history[$key][4] ) 
						);
				}
			} 
			
			$values[$fields_keys['history']] = $changes;
			
			// priority conversion
			$priority_parts = preg_split('/\-/', $values[$fields_keys['Priority']]);
			$priority = $priority_parts[0];
			
			// state conversion
			$state_map = array (
				'Submitted' => translate('Добавлено'), 
				'Opened' => translate('Добавлено'), 
				'Assigned' => translate('Добавлено'), 
				'Declined' => translate('Добавлено'), 
				'Resolved' => translate('Решено'), 
				'Closed' => translate('Закрыто')
				);
				
			// parse attachments
			if ( is_array($this->attachments) )
			{
				$attachments = $this->attachments[$values[$fields_keys['id']]];
			}
			else
			{
				$attachments = array();
				$att_lines = preg_split('/'.chr(13).chr(10).'/', $values[$fields_keys['Attachments']]);
				
				foreach ( $att_lines as $line )
				{
					if ( $line != '' )
					{
						$attrs = preg_split('/'.chr(10).'/', $line);
						
						array_push( $attachments,
							array( 'File' => $attrs[1] )
							);
					}
				}
			}

			$data = array( $values[$fields_keys['id']].' '.
							$values[$fields_keys['Headline']],
					   $values[$fields_keys['Description']],
					   $priority,
					   $state_map[$values[$fields_keys['State']]],
					   $this->users_map[$values[$fields_keys['Submitter']]],
					   $values[$fields_keys['history']],
					   $values[$fields_keys['Notes_Log']] );
			
 			array_push($data, $this->users_map[$values[$fields_keys['Owner']]]);
 			array_push($data, $attachments);

			array_push($issue_lines, $data);
		}
				
		return $issue_lines;
	}
	
	function parseEntityValues( &$content, $line_delimiter, 
							    $fields_delimiter, $substructure_fieldsamount )
	{
		$entity_values = array();
		
		$lines[0] = strtok($content, $line_delimiter);
		if ( $lines[0] === false )
		{
			$this->replyError( $this->getResultDescription( 1 ) );
		}

		// determine fields in source file and prepare the mapping
		// of fields to their positions
		//
		$fields_line = $lines[0];
		$fields = preg_split('/'.$fields_delimiter.'/', $fields_line);
		
		for ( $i = 0; $i < count($fields); $i++ )
		{
			$fields[$i] = trim($fields[$i], ' "');
		}

		$fields_keys = array_flip($fields);
		
		// maps structure position on to fields amount
		$keys = array_keys($substructure_fieldsamount);
		foreach ( $keys as $key )
		{
			$substructure_fieldsamount[$fields_keys[$key]] = $substructure_fieldsamount[$key];
		}
		
		$i = 0;
		$lines[$i] = strtok($line_delimiter);
		
		while ( $lines[$i] !== false && $i < 100000 )
		{
			// get values from the source line
			$values = $this->parseValues( $fields_delimiter, 
				$substructure_fieldsamount, $lines[$i] );
			
			// check if the line is full of values
			if ( count($values) == count($fields_keys) )
			{
				// found a line full of values
			}
			else
			{
				// found a partial line, may be content 
				// of a field is delimited by line delimiter
				//
				$partial_line = '';
				
				while ( $lines[$i] !== false )
				{
					$partial_line .= $lines[$i].$line_delimiter;
					
					$values = $this->parseValues( $fields_delimiter, 
						$substructure_fieldsamount, $partial_line );

					if ( count($values) == count($fields_keys) )
					{
						break;
					}

					if ( count($values) > count($fields_keys) )
					{
						break;
					}

					$i++;
					$lines[$i] = strtok($line_delimiter);
				}
			}
			
			if ( count($values) > 0 )
			{
				array_push($entity_values, $values);
			}

			$i++;
			$lines[$i] = strtok($line_delimiter);
		}
		
		return array($entity_values, $fields_keys);
	}
	
	function parseValues( $fields_delimiter, $substructure_fieldsamount, $line )
	{
		if ( $fields_delimiter == '","' )
		{
			$line = preg_replace('/[^,]"",""[^,]/', '`,`', $line);
		}
		
		$values = preg_split('/'.$fields_delimiter.'/', $line);
		
		for ( $i = 0; $i < count($values); $i++ )
		{
			if ( preg_match('/^[\d]{8}$/i', $values[$i]) > 0 )
			{
				// a substructure was found
				$key = $values[$i];
				
				$position = $i;
				$values[$position] = array();
				$values[$position][$key] = array();
				$i++;
				
				$in_substructure = true;

				while ( $in_substructure )
				{
					for ( $j = 0; $j < $substructure_fieldsamount[$position]; $j++, $i++ )
					{
						array_push( $values[$position][$key],
							$values[$i] );

						$values[$i] = 'to_delete';
					}

					$value_parts = preg_split('/'.chr(10).'/', $values[$position][$key][$j - 1]);
					
					if ( count($value_parts) < 2 )
					{
						$in_substructure = false;
					}
					else
					{
						$values[$position][$key][$j - 1] = $value_parts[0];
						
						$key = $value_parts[1];
						$values[$position][$key] = array();
					}
				}
			}
		}
		
		// remove items from values array were marked to be deleted
		$keys = array_keys($values);
		for ( $i = 0; $i < count($keys); $i++ )
		{
			if ( $values[$keys[$i]] == 'to_delete' )
			{
				unset($values[$keys[$i]]);
			}
		}
		
		// trim values
		for ( $i = 0; $i < count($values); $i++ )
		{
			$values[$i] = trim($values[$i], ' "');
			
			if ( $fields_delimiter == '","' )
			{
				$values[$i] = str_replace('`,`', '","', $values[$i]);
				$values[$i] = str_replace('""', '"', $values[$i]);
			}
		}
		
		return array_values($values);
	}

	function useNotification()
	{
		return false;
	}
 }
 
?>