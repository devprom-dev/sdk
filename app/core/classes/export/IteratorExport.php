<?php
use Devprom\ProjectBundle\Service\Model\ModelService;

class IteratorExport extends IteratorBase
{
 	var $iterator, $caption, $fields;
 	private $uidService = null;
 	private $table;
    private $options = array('uid');

 	function IteratorExport ( $iterator )
 	{
 		parent::IteratorBase( $iterator->object );

        $this->uidService = new ObjectUID();
 		$this->iterator = $iterator;
 		$this->fields = array();

        \FeatureTouch::Instance()->touch(strtolower(get_class($this)));
 	}

    function setOptions( $options ) {
        $this->options = $options;
    }

    function getOptions() {
        return $this->options;
    }

    function getUidService() {
 	    return $this->uidService;
    }

    function setName( $caption )
 	{
 		$this->caption = $caption;
 	}
 	
 	/*
 	 * Display name of the iterator
 	 */
 	function getName()
 	{
 		return $this->caption;
 	}
 	
 	public function setTable( & $table )
 	{
 		$this->table = $table;
 	}
 	
 	public function getTable()
 	{
 		return $this->table;
 	}
 	
 	function setFields( $fields )
 	{
 		$this->fields = $fields;
 	}
 	
 	/*
 	 * Returns collection {fieldName, fieldCaption}
 	 */
 	function getFields()
 	{
 		if ( count($this->fields) > 0 )
 		{
 			return $this->fields;
 		}
 		
 		$result = array();

 		if ( $this->uidService->hasUidObject($this->object) ) {
 			$result['UID'] = translate('UID');
 		}

 		$attrs = $this->object->getAttributes();

 		$fields = array_keys($attrs);
 		
 		for( $i = 0; $i < count($fields); $i++ )
 		{
 			if ( $fields[$i] == 'OrderNum' || $fields[$i] == 'RecordCreated' || $fields[$i] == 'RecordModified' )
 			{
 				continue;
 			}

 			$result[$fields[$i]] = translate($this->object->getAttributeUserName( $fields[$i] ));
 		}
 		
 		return $result;
 	}
 	
 	/*
 	 * Returns the value of the field
 	 */
 	function get( $fieldName )
 	{
 	    if ( $this->iterator->get('VPD') != '' ) {
            $this->iterator->object->setVpdContext($this->iterator->get('VPD'));
        }

 		switch ( $fieldName )
 		{
 			case 'State':
 			    if ( $this->iterator instanceof StatableIterator ) {
                    $state_it = $this->iterator->getStateIt();
                    return $state_it->getDisplayName();
                }
                else {
                    return $this->iterator->get($fieldName);
                }

            case 'StateDuration':
            case 'LeadTime':
                return getSession()->getLanguage()->getDurationWording($this->iterator->get( $fieldName ));

			default:
			    if( $this->iterator->object->IsReference($fieldName) )
				{
					$entity_it = $this->iterator->getRef($fieldName);
					$names = array();
					while( !$entity_it->end() ) {
                        $info = $this->uidService->getUidInfo($entity_it);
                        $title = $entity_it->getDisplayNameSearch($info['uid'] != '' ? '['.$info['uid'].'] ' : '');
                        if ( $info['state_name'] != '' ) $title .= ' ('.$info['state_name'].')';
                        $names[] = $title;
						$entity_it->moveNext();
					}
					return $names;
				}
				else
				{
		 			$attribute_type = $this->iterator->object->getAttributeDbType( $fieldName );
		 			
					switch ( strtolower($attribute_type) )
					{
						case 'date':
							return $this->iterator->getDateFormatted($fieldName);
							
						case 'datetime':
							return $this->iterator->getDateTimeFormat($fieldName);

                        case 'wysiwyg':
                            return html_entity_decode(
                                TextUtils::stripAnyTags($this->iterator->getHtmlDecoded( $fieldName ))
                            );

                        case 'float':
                            return str_replace(',', '.',
                                round($this->iterator->get($fieldName), \EnvironmentSettings::getFloatPrecision()));

                        case 'integer':
                            return str_replace(',', '.', round($this->iterator->get($fieldName), 0));

						default:
                            if ( in_array('computed', $this->iterator->object->getAttributeGroups($fieldName)) ) {
                                $result = ModelService::computeFormula(
                                    $this->iterator,
                                    $this->iterator->object->getDefaultAttributeValue($fieldName)
                                );

                                $lines = array();
                                foreach ($result as $computedItem) {
                                    if (!is_object($computedItem)) {
                                        $lines[] = TextUtils::stripAnyTags($computedItem);
                                    } else {
                                        $lines[] = TextUtils::stripAnyTags($this->uidService->getUidWithCaption(
                                            $computedItem, 15, '',
                                            $computedItem->get('VPD') != getSession()->getProjectIt()->get('VPD')
                                        ));
                                    }
                                }
                                return join(', ', $lines);
                            }
		 					return $this->iterator->getHtmlDecoded( $fieldName );
					}
				}
 		}
 	}
 	
 	function get_native( $attr )
 	{
 		return $this->iterator->get_native( $attr );
 	}
 	
 	/*
 	 *  Returns comment of a field
 	 */
 	function comment( $fieldName )
 	{
 	}
 	 
 	/*
 	 * Returns number of records in a source iterator
 	 */
 	function count() {
 		return $this->iterator->count();
 	}
 	
 	function moveFirst() {
 		$this->iterator->moveFirst();
 	}
 	
 	function moveToPos( $offset ) {
 		$this->iterator->moveToPos( $offset );
 	}
 	
 	function moveNext() {
 		$this->iterator->moveNext();
 	} 	

 	function idsToArray() {
 		return $this->iterator->idsToArray();
 	} 	
 	
 	/*
 	 * Returns the url to download exported file
 	 */
 	function getUrl( $parms )
 	{
 		$parms = array_merge($parms,
 			array( 'entity' => $this->object->getClassName() ) );
 			
 		$parms_keys = array_keys($parms);
 		$query_items = array();
 		
 		for($i = 0; $i < count($parms_keys); $i++) {
 			array_push($query_items, $parms_keys[$i].'='.$parms[$parms_keys[$i]]);
 		}
 		
		$query_string = '?'.join('&', $query_items);

 		return $query_string;
 	}
 	
 	function getIterator()
 	{
 		return $this->iterator;
 	}
 	
 	function export()
 	{
 	}
}