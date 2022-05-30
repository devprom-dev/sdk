<?php

class RequestsImportBase extends CommandForm
{
     private $fileName = '';
     private $uidService = null;

 	function getObject()
 	{
 	}

 	function setFileName( $fileName ) {
        $this->fileName = $fileName;
    }

    function getFileName() {
        return $this->fileName;
    }

    function getUidService() {
         if ( is_object($this->uidService) ) return $this->uidService;
         return $this->uidService = new \ObjectUID;
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
 		$fields = array_diff(
					array_keys($object->getAttributes()),
                    array_diff(
                        $object->getAttributesByGroup('system'),
                        array (
                            'SectionNumber'
                        )
                    ),
					$object->getAttributesByGroup('trace'),
                    $object->getAttributesReadonly(),
                    array(
                        'OrderNum'
                    )
				);
        if ( $object->hasAttribute('ContentEditor') ) {
            $fields[] = 'ContentEditor';
        }
        if ( $this->getUidService()->hasUidObject($object) ) {
            $fields[] = 'UID';
        }
        return array_values($fields);
 	}
 	
	function getCaptions()
	{
		$captions = array();
		$object = $this->getObject();
		
		foreach( $this->getFields() as $key => $attr ) {
            $fieldTitle = translate($this->sanitizeData($object->getAttributeUserName($attr)));
            if ( $fieldTitle == '' ) $fieldTitle = $attr;
            if ( in_array($fieldTitle, $captions) ) continue;
			$captions[$attr] = $fieldTitle;
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
                if ( $value == '' ) continue;
				
				if ( $object->IsReference($fieldName) )
				{
					switch ( $fieldName )
					{
						default:
							$id = '';
							
							if ( $fieldName == 'ParentPage' ) {
								$new_value_row = array_filter( $result, function($result_value) use ($value) {
                                        return $result_value['Caption'] == trim($value);
                                    });
								if ( count($new_value_row) > 0 ) $id = 'Undefined:'.$this->getAltKey(array_shift($new_value_row));
							}

							if ( $id == '' ) {
                                $ref = $object->getAttributeObject($fieldName);
                                if ( $this->getUidService()->hasUidObject($ref) ) {
                                    $nameParts = explode(' ', $value);
                                    $objectIt = $this->getUidService()->getObjectIt(trim($nameParts[0], ' []'));
                                    if ( $objectIt->getId() != '' ) $id = $objectIt->getId();

                                }

                                if ( $id == '' ) {
                                    if (!array_key_exists($fieldName, $refs)) {
                                        $refs[$fieldName] = $this->getObjectIt($ref);
                                    }
                                    $id = $this->getId(
                                        $refs[$fieldName],
                                        trim($value),
                                        'Undefined:' . trim($value)
                                    );
                                }
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
                                $value = getLanguage()->getPhpDateTime(
                                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value));
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

    function getObjectIt( $object )
    {
        return count($object->getVpds()) > 0
            ? $object->getRegistry()->Query(
                array(
                    new FilterBaseVpdPredicate()
                )
            )
            : $object->getAll();
    }

	function getId( $object_it, $value, $default )
	{
	    $ids = array();
		$object_it->moveFirst();
		while ( !$object_it->end() ) {
		    foreach( explode(',',$value) as $valueItem) {
                if ( $object_it->getHtmlDecoded('Caption') == $valueItem ) {
                    $ids[] = $object_it->getId();
                }
            }
			$object_it->moveNext();
		}
		if ( count($ids) > 0 ) return join(',', $ids);
		return $value == '' ? $value : $default;
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
		$this->request->removeNotificator( 'EmailNotificator' );

		$result = $this->parse();
		$comments = array();
        $undefined = array();
		$imported = 0;
		$errors = array();

		for ( $i = 0; $i < count($result); $i++ )
		{
            foreach( $result[$i] as $field => $value )
            {
                $match = array();
                if ( $this->request->IsReference($field) )
                {
                    $object = $this->request->getAttributeObject($field);
                    $object->removeNotificator('EmailNotificator');
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
                                        return $item['Id'] == $parentId || mb_stripos($parentId, $item['Caption']) !== false;
                                    })
                                );
                                $id = $undefined[$parentRow['Id']];
                                if ( $id == '' ) $id = $this->getId($this->getObjectIt($object), $parentRow, '');
                                if ( $id == '' && in_array($field, array('Author')) ) $id = $parentId;
                            }
                            $result[$i][$field] = $id;
                        }
                        else {
                            // other references
                            $self = $this;
                            $objectIt = $this->getObjectIt($object);
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
                    if ( $result[$i][$field] > 0 ) {
                        if ( $object instanceof Project ) {
                            $result[$i]['VPD'] = $object->getExact($result[$i][$field])->get('VPD');
                        }
                    }
                    elseif ( $result[$i][$field] != '' ) {
                        $resultValue = array();
                        foreach( explode(',', $result[$i][$field]) as $resultItem ) {
                            $refId = $object->add_parms(
                                array(
                                    'Caption' => $resultItem
                                )
                            );
                            if ( $refId > 0 ) {
                                $resultValue[] = $refId;
                            }
                        }
                        if ( count($resultValue) > 0 ) {
                            $result[$i][$field] = join(',',$resultValue);
                        }
                        else {
                            unset($result[$i][$field]);
                        }
                    }
                }
                else {
                    switch( $field ) {
                        case 'RecentComment':
                            $comments = preg_split('/[\r\n]/', $result[$i][$field]);
                            break;
                        default:
                            $fieldType = $this->request->getAttributeType($field);
                            switch( $fieldType ) {
                                case 'wysiwyg':
                                    $result[$i][$field] = nl2br($result[$i][$field]);
                                    break;
                            }
                    }
                }
            }

            try {
                $parms = $result[$i];

                $request_id = getFactory()->mergeEntity($this->request, $parms)->getId();
                if ( $request_id > 0 ) {
                    $undefined[$result[$i]['Id']] = $request_id;
                    $imported++;
                }

                $commentObject = getFactory()->getObject('Comment');
                foreach( array_reverse($comments) as $key => $comment ) {
                    if ( $comment == '' ) continue;
                    getFactory()->mergeEntity($commentObject, array(
                        'ObjectId' => $request_id,
                        'ObjectClass' => get_class($this->request),
                        'AuthorId' => getSession()->getUserIt()->getId(),
                        'Caption' => html_entity_decode($comment),
                        'OrderNum' => $key + 1
                    ));
                }
            }
            catch( \Exception $e ) {
                $errors[] = sprintf(text(3021), $i, $e->getMessage());
            }
		}

		if ( count($errors) ) $this->replyError(join('<br/>', $errors));
		$this->replySuccess( str_replace('%1', $imported, text(1723)) );
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
		
		foreach ( $fields as $field )
		{
			if ( $rows > 0 && !array_key_exists($field, $result[0]) ) continue;
            $title = translate($object->getAttributeUserName($field));
            if ( $title == '' ) $title = $field;
			$xml .= '<th>'.$title.'</th>';
		}
		$xml .= '</tr>';

		$state_it = $this->buildStateIterator($object);

		for ( $i = 0; $i < min(count($result), $max_issues_todisplay); $i++ )
		{
			$xml .= '<tr>';
			$xml .= '<td>'.($i+1).'</td>';

			foreach ( $fields as $field )
			{
				if ( $rows > 0 && !array_key_exists($field, $result[0]) ) continue;
				
				if ( $object->IsReference($field) )
				{
					$ref = $object->getAttributeObject($field);

                    $parts = preg_split('/:/', $result[$i][$field]);
                    if ( $parts[0] == 'Undefined' ) {
                        $parentIds = explode(',',array_pop($parts));
                        $foundRows = array_filter($result, function($item) use($parentIds) {
                            return in_array($item['Id'], $parentIds);
                        });
                        $value = count($foundRows) > 0
                            ? join(',',
                                array_map(function($item) {
                                        return htmlentities($item['Caption']);
                                    }, $foundRows ))
                            : join(',',$parentIds);
                    }
                    else {
                        $value = $result[$i][$field];
                        $default_value = $object->getDefaultAttributeValue($field);

                        if ( $value == '' ) $value = $default_value;
                        if ( $value != '' ) {
                            if ( $ref instanceof \MetaobjectCacheable ) {
                                $ref_it = $ref->getExact($value);
                            }
                            else {
                                $ref_it = $ref->getRegistryBase()->Query(array(
                                    new FilterInPredicate(explode(',',$value)),
                                    new FilterBaseVpdPredicate()
                                ));
                                if ( $ref_it->count() < 1 && $ref->getAttributeType('Caption') != '' ) {
                                    $ref_it = $ref->getRegistryBase()->Query(array(
                                        new FilterAttributePredicate('Caption', explode(',',$value)),
                                        new FilterBaseVpdPredicate()
                                    ));
                                }
                            }
                        }
                        else {
                            $ref_it = $ref->getEmptyIterator();
                        }

                        if ( $ref_it->getId() < 1 ) {
                            $ref_it = $ref->getExact( $default_value );
                        }

                        $valueItems = array();
                        while( !$ref_it->end() ) {
                            $valueItems[] = $ref_it->getDisplayName();
                            $ref_it->moveNext();
                        }
                        $value = join(', ', $valueItems);
                    }
				}
				else
				{
					switch ( $field )
					{
						case 'ObjectChangeLog':
							$changes = $result[$i][$field];
							$value = '';
	
							foreach ( $changes as $change )
							{						
								$value .= $change['Date'].', '.$change['User'].'<br/>'.
									nl2br($change['Content']).'<br/><br/>';
							}
							break;
	
						case 'Attachments':
							$attachments = $result[$i][$field];
							$value = '';
	
							foreach ( $attachments as $attachment )
							{						
								$value .= $attachment['File'].'<br/>';
							}
							break;
	
						case 'State':
						    if ( is_object($state_it) ) {
						        $state_it->moveTo('ReferenceName', $result[$i][$field]);
						        $value = $state_it->get('Caption');
						    }
						    break;
						    
						default:
							$value = $result[$i][$field];
							if ( $value == '' ) {
								$value = $object->getDefaultAttributeValue($field);
							}
							$value = nl2br(htmlentities($value));
					}
				}
				
				$xml .= '<td id="'.$field.'">'.$value.'</td>';
			}
			$xml .= '</tr>';
		}

		$xml .= '</table>';

		$xml .= translate('Всего строк').': '.$rows.'<br/><br/>';
		
		$this->replyResultBinary( false, $xml);
	}
}
