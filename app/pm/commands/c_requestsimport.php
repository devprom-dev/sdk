<?php

 /////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
 class RequestsImportBase extends CommandForm
 {
 	function getObject()
 	{
 	}
 	
 	function getLines()
 	{
		$lines = preg_split('/'.Chr(10).'/', $this->sanitizeData($_REQUEST['Excel']));
		
		for ( $i = 0; $i < count($lines); $i++ )
		{
			if ( $lines[$i] == '' )
			{
				unset($lines[$i]);
			}
			else
			{
				$lines[$i] = preg_split('/'.Chr(9).'/', $lines[$i]);
			}
		}
		
		return $lines;
 	}

	 function sanitizeData( $data ) {
		 $data = htmlentities($data, null, APP_ENCODING);
		 $data = str_replace("&nbsp;", " ", $data);
		 return html_entity_decode($data, ENT_COMPAT | ENT_HTML401, APP_ENCODING);
	 }
 	
 	function getFields()
 	{
 		$object = $this->getObject();
 		return array_merge(
				array_diff(
					array_keys($object->getAttributes()),
					$object->getAttributesByGroup('system'),
					$object->getAttributesByGroup('trace')
				),
				array (
					'ContentEditor'
				)
		);
 	}
 	
	function getCaptions()
	{
		$captions = array();
		$object = $this->getObject();
		
		foreach( $this->getFields() as $key => $attr ) {
			$captions[$attr] = translate($this->sanitizeData($object->getAttributeUserName($attr)));
		}
		return $captions;
	}
	
 	function parse()
	{
		$lines = $this->getLines();

		$fields = array();
		$object = $this->getObject();

		$captions = $this->getCaptions();
		array_walk( $captions,
			function(&$value, $key) {
				$value = md5(trim($value));
			}
		);
		$captions = array_flip($captions);

		for ( $i = 0; $i < count($lines[0]); $i++ ) {
			$titleHash = md5(trim($lines[0][$i]));
			if ( $captions[$titleHash] != '' ) {
				$fields = array_merge($fields, array( $captions[$titleHash] => $i) );
			}
		}

		$refs = array();

		$state_it = $this->buildStateIterator($object);
		
		$field_names = array_keys($fields);
		$result = array();
				
		for ( $i = 1; $i < count($lines); $i++ )
		{
			$parms = array();

			for ( $j = 0; $j < count($field_names); $j++ )
			{
				$value = $lines[$i][$fields[$field_names[$j]]];
				
				if ( $object->IsReference($field_names[$j]) )
				{
					switch ( $field_names[$j] )
					{
						default:
							$id = '';
							
							if ( !array_key_exists($field_names[$j], $refs) )
							{
								$ref = $object->getAttributeObject($field_names[$j]);
								$refs[$field_names[$j]] = $ref->getAll();
							}
							
							if ( $field_names[$j] == 'ParentPage' )
							{
								$new_value_row = array_filter( $result, function($result_value) use ($value) {
										return $result_value['Caption'] == trim($value); 
								});
								
								if ( count($new_value_row) > 0 ) $id = 'Undefined:'.trim($value); 
							}
							
							if ( $id == '' )
							{
								$id = $this->getId(
										$refs[$field_names[$j]], 
										trim($value), 
										'Undefined:'.trim($value)
								);
							}
								
							$parms = array_merge($parms, array( $field_names[$j] => $id ) );
					}
				}
				else
				{
				    switch ( $field_names[$j] )
				    {
				        case 'State':
				            $state_it->moveTo('Caption', trim($value));

				            $value = $state_it->get('ReferenceName');
				            
				            break;
				    }

					$parms = array_merge($parms,
						array( $field_names[$j] => $value ) );
				}
			}
			
			array_push($result, $parms);
		}

		return $result;
	}

	function getId( $object_it, $value, $default )
	{
		$object_it->moveFirst();
		while ( !$object_it->end() ) {
			if ( mb_strtoupper($object_it->getHtmlDecoded('Caption')) == mb_strtoupper(trim($value)) ) {
				return $object_it->getId();
			}
			$object_it->moveNext();
		}
		return $value == '' ? $value : $default;
	}
	
	function useNotification()
	{
		return true;
	}
	
 	function buildStateIterator( $object )
	{
		global $model_factory;
		
		if ( !is_a($object, 'MetaobjectStatable') ) return $model_factory->getObject('StateBase')->getEmptyIterator();
		if ( $object->getStateClassName() == '' ) return $model_factory->getObject('StateBase')->getEmptyIterator();
		
		$state = $model_factory->getObject($object->getStateClassName());
		
		$state_it = $state->getAll();
		
		$state_it->buildPositionHash( array('Caption') );
		
		return $state_it;
	}
 }

 /////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
 class RequestsImport extends RequestsImportBase
 {
 	function create()
	{
		$this->request = $this->getObject();

		if ( !$this->useNotification() ) {
			$this->request->removeNotificator( 'EmailNotificator' );
		}

		$result = $this->parse();
		$imported = 0;
		
		for ( $i = 0; $i < count($result); $i++ )
		{
			if ( $result[$i]['Caption'] != '' )
			{
				foreach( $result[$i] as $field => $value )
				{
					$match = array();
					if ( $this->request->IsReference($field) )
					{
						$object = $this->request->getAttributeObject($field);
						getFactory()->resetCachedIterator( $object );

						if ( preg_match('/Undefined:(.+)/', $value, $match) ) {
							$result[$i][$field] = $this->getId($object->getAll(), trim($match[1]), 'NULL');
						}

						if ( $result[$i][$field] > 0 && $object instanceof Project ) {
							$result[$i]['VPD'] = $object->getExact($result[$i][$field])->get('VPD');
						}
					}
				}

				$parms = $result[$i];
                $mapper = new ModelDataTypeMapper();
                $mapper->map( $this->request, $parms );

				$request_id = $this->request->add_parms( $parms );
				if ( $request_id > 0 )
				{
					$imported++;
					
					$this->createDependencies($request_id, $parms);
				}
			}
		}

		$this->replySuccess( str_replace('%1', $imported, text(1723)) );
	}
	
	function createDependencies( $request_id, $parms )
	{
	}

 	function preview()
	{
		global $_REQUEST, $model_factory, $project_it;

		// parse source content
		$result = $this->parse();
		$object = $this->getObject();

		$rows = count($result);
		$max_issues_todisplay = 20;
		
		$xml = '';

		if ( count($result) > $max_issues_todisplay )
		{
			$xml .= translate('Показаны первые').': '.$max_issues_todisplay.'<br/><br/>';
		}
		
		$fields = $this->getFields();

		$xml .= '<table class="table" width="100%">';
		
		$xml .= '<tr>';
		$xml .= '<th>'.translate('№').'</th>';
		
		for ( $i = 0; $i < count($fields); $i++ )
		{
			if ( $rows > 0 && !array_key_exists($fields[$i], $result[0]) ) continue;
			
			$xml .= '<th>'.translate($object->getAttributeUserName($fields[$i])).'</th>';
		}
		$xml .= '</tr>';

		$state_it = $this->buildStateIterator($object);

		for ( $i = 0; $i < min(count($result), $max_issues_todisplay); $i++ )
		{
			$xml .= '<tr>';
			$xml .= '<td>'.($i+1).'</td>';

			for ( $j = 0; $j < count($fields); $j++ )
			{
				if ( $rows > 0 && !array_key_exists($fields[$j], $result[0]) ) continue;
				
				if ( $object->IsReference($fields[$j]) )
				{
					$ref = $object->getAttributeObject($fields[$j]);
					
					$value = array_pop(preg_split('/:/', $result[$i][$fields[$j]]));
					$default_value = $object->getDefaultAttributeValue($fields[$j]);
					
					if ( $value == '' ) $value = $default_value;

					$ref_it = is_numeric($value)
							? $ref->getExact( $value )
							: ($ref->getAttributeType('Caption') != ''
									? $ref->getByRef('Caption', $value)
									: $ref->getEmptyIterator());

					if ( $ref_it->getId() < 1 ) $ref_it = $ref->getExact( $default_value );

					$value = $ref_it->getId() > 0
							? $ref_it->getDisplayName() 
							: '';
				}
				else
				{
					switch ( $fields[$j] )
					{
						case 'ObjectChangeLog':
							$changes = $result[$i][$fields[$j]];
							$value = '';
	
							foreach ( $changes as $change )
							{						
								$value .= $change['Date'].', '.$change['User'].'<br/>'.
									nl2br($change['Content']).'<br/><br/>';
							}
							break;
	
						case 'Attachments':
							$attachments = $result[$i][$fields[$j]];
							$value = '';
	
							foreach ( $attachments as $attachment )
							{						
								$value .= $attachment['File'].'<br/>';
							}
							break;
	
						case 'State':
						    if ( is_object($state_it) ) {
						        $state_it->moveTo('ReferenceName', $result[$i][$fields[$j]]);
						        $value = $state_it->get('Caption');
						    }
						    break;
						    
						default:
							$value = $result[$i][$fields[$j]];
							if ( $value == '' ) {
								$value = $object->getDefaultAttributeValue($fields[$j]);
							}
							$value = nl2br($value);
					}
				}
				
				$xml .= '<td id="'.$fields[$j].'">'.$value.'</td>';
			}
			$xml .= '</tr>';
		}

		$xml .= '</table>';

		$xml .= translate('Всего строк').': '.$rows.'<br/><br/>';
		
		$this->replyResultBinary( false, $xml);
	}
}
