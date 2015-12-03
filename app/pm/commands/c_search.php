<?php
include SERVER_ROOT_PATH."pm/classes/common/predicates/CustomAttributeSearchPredicate.php";
include_once SERVER_ROOT_PATH."ext/locale/LinguaStemRu.php";

class Search extends CommandForm
{
	private $length_constraint = 2;

    function validate()
    {
        $this->checkRequired( array('searchrequest') );

        return true;
    }
    
 	function preview()
	{
		$searchparms = preg_split('/,/', $_REQUEST['parms']);
		$search = $_REQUEST['searchrequest'];

		//
	    $this->searchByUid($search);
		//
	    $results = $this->searchByAttributes($search, $searchparms);

		$items_found = 0;
		foreach( $results as $result ) {
			$items_found += $result['object']->count();
		}

		if ( $items_found == 1 && $searchparms['select'] != 'true' )
		{
			$values = array_shift($results);
			$url = $values['object']->getViewUrl();

			if ( $url[0] == '/' || strstr($url, 'http') ) {
				$this->replyRedirect($url, text(1309));
			}
			else {
				$this->replyRedirect(getSession()->getApplicationUrl().$url, text(1309));
			}
		}
		else
		{
			if ( count($results) < 1 ) {
				if ( strlen($search) < $this->length_constraint + 1 ) {
					$this->replyError(text(1252));
				}
				else {
					$this->replyError(text(1253));
				}
			}
			else {
				$this->replyResults( $results, $search );
			}
		}
	}
	
	function searchByAttributes( $search, $paramters )
	{
		$results = array();

		$searchable = getFactory()->getObject('SearchableObjectSet');
		$searchable_it = $searchable->getAll();
		
		while( !$searchable_it->end() )
		{
			$object = getFactory()->getObject($searchable_it->get('ReferenceName'));
			if (!getFactory()->getAccessPolicy()->can_read($object)) {
				$searchable_it->moveNext();
				continue;
			}

			if ( count($paramters) > 0 && !in_array(get_class($object), $paramters) ) {
				$searchable_it->moveNext();
				continue;
			}

			if ($object instanceof WikiPage) {
				$object->setRegistry(new WikiPageRegistryContent($this));
			}
			$registry = $object->getRegistry();

			if ( is_numeric($search) ) {
				$object_it = $registry->Query(
						array(
								new FilterInPredicate($search),
								new FilterVpdPredicate(),
								new SortRecentClause() 
						)
				);
				if ( $object_it->getId() != '' ) {
					$results[$searchable_it->getId()] = array (
						'object' => $object->createCachedIterator($object_it->getRowset())
					);
				}
			}

			if ( strlen($search) > $this->length_constraint ) {
				$object_it = $registry->Query(
						array(
								new FilterSearchAttributesPredicate($search, $searchable_it->get('attributes')),
								new FilterVpdPredicate(),
								new SortRecentClause()
						)
				);
				if ( $object_it->count() > 0 ) {
					$results[$searchable_it->getId()] = array(
						'object' => $object->createCachedIterator($object_it->getRowset()),
						'attributes' => $searchable_it->get('attributes')
					);
				}
				$object_it = $registry->Query(
					array(
						new CustomAttributeSearchPredicate($search, $searchable_it->get('attributes')),
						new FilterVpdPredicate(),
						new SortRecentClause()
					)
				);
				if ( $object_it->count() > 0 ) {
					$results[$searchable_it->getId()] = array(
						'object' => $object->createCachedIterator($object_it->getRowset()),
						'attributes' => array_filter(array_keys($object->getAttributes()), function($key) use ($object) {
							return $object->getAttributeOrigin($key) == ORIGIN_CUSTOM;
						})
					);
				}
			}
			
			$searchable_it->moveNext();
		}

		return $results;
	}
	
	function searchByUid( $uid )
	{
	    $object_uid = new ObjectUid;
		 
		if ( $object_uid->isValidUid($uid) ) 
		{
		    $object_it = $object_uid->getObjectIt($uid);
		}
		 
		if ( !is_object($object_it) ) return;
		 
		if ( $object_it->count() < 1 ) return;

	 	$url = $object_it->getViewUrl();
	 	
	 	if ( strpos($url, '/pm/') === false ) 
	 	{
	 		$url = getSession()->getApplicationUrl().$url; 
	 	}
	 	
	 	$this->replyRedirect( $url, text(1309) ); 
	}
	
	function replyResults( $results, $search )
	{
	    $uid = new ObjectUID;
	    
	    $size = 180;
		$html = '';

        $stem = new Stem\LinguaStemRu();
        $search_items = array_map(
            function($word) use($stem) {
                return $stem->stem_word($word);
            },
            array_filter(
                preg_split('/\s+/', $search),
                function( $value ) {
                    return trim($value) != '';
                }
            )
        );

		$report = getFactory()->getObject('PMReport');
	    $searchable = getFactory()->getObject('SearchableObjectSet');
		$searchable_it = $searchable->getAll();
		
		foreach ( $results as $entity => $result )
		{
			$object = getFactory()->getObject($entity);

	        $html .= '<table class="table"><thead>';
	        $html .= '<tr><th>'.$object->getDisplayName().'</th><th width="60%">'.translate('Найдено').'</th></tr>';
	        $html .= '</thead><tbody>';

	        $object_it = $result['object'];
	        
	        while ( !$object_it->end() )
	        {
	            $html .= '<tr><td>'.$uid->getUidWithCaption($object_it).'</td>'; 

	            foreach ( $result['attributes'] as $attribute )
	            {
    	            $text = new html2text( $object_it->getHtmlDecoded($attribute) );
    	            $text = str_replace(chr(10), ' ', $text->get_text());
    	            $text = str_replace(chr(13), ' ', $text);

                    $text = preg_replace(
                        array_map(
                            function($value) {
                                return '#'.$value.'#iu';
                            },
                            $search_items
                        ),
                        '<span class="label">\\0</span>',
                        $text
                    );

                    $parts = explode('<span', $text);
                    if ( count($parts) > 1 ) {
                        $text = join('<span', array_slice($parts, 0, 2));
						$html .= '<td>' . translate($object_it->object->getAttributeUserName($attribute)) . ': ' . $text . '</td>';
						break;
                    }
	            }
	            
                $html .= '</tr>';
	            
	            $object_it->moveNext();
	        }

		    $searchable_it->moveToId( get_class($object) );
		    
		    if ( $searchable_it->get('Report') != '' )
		    {
		        $report_it = $report->getExact($searchable_it->get('Report'));
		        
		        $menu = $report_it->buildMenuItem(strtolower(get_class($object)).'='.join(',',$object_it->idsToArray()));
		         
		        $html .= '<tr>';
	                $html .= '<td colspan="2"><a href="'.$menu['url'].'">'.text(1014).'</td>';
	            $html .= '</tr>';
		    }
	        
			$html .= '</tbody></table>';
		}
		
	    $this->replyResultBinary( false, $html );
	}
}
