<?php
include_once SERVER_ROOT_PATH . 'core/classes/model/validation/ModelValidator.php';

class RequestsImportBase extends CommandForm
{
     private $fileName = '';

 	function getObject()
 	{
 	}

 	function setFileName( $fileName ) {
        $this->fileName = $fileName;
    }

    function getFileName() {
        return $this->fileName;
    }
 	
 	function getLines()
 	{
		$lines = preg_split('/'.Chr(10).'/', $this->sanitizeData($_REQUEST['Excel']));
		for ( $i = 0; $i < count($lines); $i++ )
		{
			if ( $lines[$i] == '' ) {
				unset($lines[$i]);
			}
			else {
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
                    array_diff(
                        $object->getAttributesByGroup('system'),
                        array (
                            'SectionNumber'
                        )
                    ),
					$object->getAttributesByGroup('trace'),
                    array(
                        'OrderNum'
                    )
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
        $fileName = $this->getFileName();

		$fields = array();
		$object = $this->getObject();

		$captions = $this->getCaptions();
		array_walk( $captions,
			function(&$value, $key) {
				$value = md5(trim($value));
			}
		);
		$captions = array_flip($captions);

        $titles = array_shift($lines);
		foreach( $titles as $cellName => $title ) {
			$titleHash = md5(trim($title));
			if ( $captions[$titleHash] != '' ) {
				$fields = array_merge($fields, array( $captions[$titleHash] => $cellName) );
			}
		}

		$refs = array();

		$state_it = $this->buildStateIterator($object);
		
		$field_names = array_keys($fields);
		$result = array();
				
		foreach( $lines as $cellName => $line )
		{
			$parms = array();

			for ( $j = 0; $j < count($field_names); $j++ )
			{
                $fieldName = $field_names[$j];
                $cellName = $fields[$fieldName];
				$value = $line[$cellName];
				
				if ( $object->IsReference($fieldName) )
				{
					switch ( $fieldName )
					{
						default:
							$id = '';
							
							if ( !array_key_exists($fieldName, $refs) )
							{
								$ref = $object->getAttributeObject($fieldName);
								$refs[$fieldName] = $ref->getAll();
							}
							
							if ( $fieldName == 'ParentPage' )
							{
								$new_value_row = array_filter( $result, function($result_value) use ($value) {
										return $result_value['Caption'] == trim($value); 
								});
								if ( count($new_value_row) > 0 ) $id = 'Undefined:'.$this->getAltKey(array_shift($new_value_row));
							}

							if ( $id == '' )
							{
								$id = $this->getId(
										$refs[$fieldName],
										trim($value), 
										'Undefined:'.trim($value)
								);
							}
								
							$parms = array_merge($parms, array( $fieldName => $id ) );
					}
				}
				else
				{
				    switch ( $fieldName )
				    {
				        case 'State':
				            $state_it->moveTo('Caption', trim($value));
				            $value = $state_it->get('ReferenceName');
				            break;

                        case 'SectionNumber':
                            $parentNumber = join('.',array_slice(preg_split('/\./', trim($value)), 0, -1));
                            $new_value_row = array_filter( $result, function($result_value) use ($parentNumber, $fieldName) {
                                return $result_value[$fieldName] == $parentNumber;
                            });
                            if ( count($new_value_row) > 0 ) {
                                $parentPageValue = 'Undefined:'.$this->getAltKey(array_shift($new_value_row));
                            }
                            else {
                                if ( $parentNumber == '' ) {
                                    // search for root
                                    $new_value_row = array_filter( $lines, function($result_value) use ($cellName) {
                                        return $result_value[$cellName] == '';
                                    });
                                    if ( count($new_value_row) < 1 ) {
                                        $data = array_map(function() { return ''; }, array_flip($field_names));
                                        $data['Caption'] = trim($fileName);
                                        $data['ParentPage'] = '';
                                        $data['Id'] = $this->getAltKey($data);
                                        array_unshift($result, $data);
                                    }
                                    else {
                                        $parentRow = array_shift($new_value_row);
                                        $fileName = $parentRow[$fields['Caption']];
                                    }
                                }
                                $parentPageValue = 'Undefined:'.$this->getAltKey(array('Caption'=>trim($fileName)));
                            }
                            $parms = array_merge($parms, array( 'ParentPage' => $parentPageValue ) );
                            break;
				    }
				    switch( $object->getAttributeType($fieldName) )
                    {
                        case 'date':
                        case 'datetime':
                            if ( $value != '' && is_numeric($value) ) {
                                $value = getLanguage()->getPhpDateTime(PHPExcel_Shared_Date::ExcelToPHP($value));
                            }
                            break;

                        case 'wysiwyg':
                            $value = htmlentities($value);
                            break;
                    }
					$parms = array_merge($parms, array( $fieldName => $value ) );
				}
			}
            $parms = array_merge($parms, array( 'Id' => $this->getAltKey($parms)  ) );

			array_push($result, $parms);
		}
		return $result;
	}

	function getAltKey( $parms ) {
        return md5($parms['SectionNumber'].$parms['ParentPage'].$parms['Caption']);
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
        $undefined = array();
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

						if ( preg_match('/^Undefined:(.+)$/si', $value, $match) )
						{
                            if ( $object instanceof $this->request ) {
						        // hierarchy case
                                $parentId = trim($match[1]);
                                if ( array_key_exists($parentId, $undefined) ) {
                                    $id = $undefined[$parentId];
                                }
                                else {
                                    $parentRow = array_shift(
                                        array_filter($result, function($item) use($parentId) {
                                            return $item['Id'] == $parentId || $item['Caption'] == $parentId;
                                        })
                                    );
                                    $id = $undefined[$parentRow['Id']];
                                    if ( $id == '' ) $id = $this->getId($object->getAll(), $parentRow, '');
                                    if ( $id == '' && in_array($field, array('Author')) ) $id = $parentId;
                                }
                                $result[$i][$field] = $id;
                            }
                            else {
						        // other references
                                $self = $this;
                                $objectIt = $object->getAll();
                                $valueObject = $object instanceof Tag || $object instanceof Watcher || $object instanceof User;

                                $result[$i][$field] = join(',', array_filter(
                                    array_map(
                                        function($value) use ($self, $objectIt, $valueObject) {
                                            return $self->getId($objectIt, $value, $valueObject ? $value : '');
                                        },
                                        preg_split("/[\r\n,]+/mi", $match[1])
                                    ),
                                    function($value) {
                                        return $value != '';
                                    }
                                ));
                            }
						}

						if ( $result[$i][$field] > 0 && $object instanceof Project ) {
							$result[$i]['VPD'] = $object->getExact($result[$i][$field])->get('VPD');
						}
					}
				}

				$parms = $result[$i];
                $mapper = new ModelDataTypeMapper();
                $mapper->map( $this->request, $parms );
                $validator = new ModelValidator();
                $validator->validate($this->request, $parms);

				$request_id = $this->request->add_parms( $parms );
				if ( $request_id > 0 )
				{
					$imported++;
                    $undefined[$result[$i]['Id']] = $request_id;
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

                    $parts = preg_split('/:/', $result[$i][$fields[$j]]);
                    if ( $parts[0] == 'Undefined' ) {
                        $parentId = array_pop($parts);
                        $foundRow = array_shift(
                            array_filter($result, function($item) use($parentId) {
                                return $item['Id'] == $parentId;
                            })
                        );
                        $value = is_array($foundRow) ? htmlentities($foundRow['Caption']) : $parentId;
                    }
                    else {
                        $value = $result[$i][$fields[$j]];
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
							$value = nl2br(htmlentities($value));
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
