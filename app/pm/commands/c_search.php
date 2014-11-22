<?php

include_once SERVER_ROOT_PATH."pm/classes/common/ObjectSearchRegistry.php";

class Search extends CommandForm
{
    function validate()
    {
        $this->checkRequired( array('searchrequest') );

        return true;
    }
    
 	function preview()
	{
	    $this->searchByUid( $_REQUEST['searchrequest'] );
	    
	    $this->searchByAttributes( $_REQUEST['searchrequest'], $_REQUEST['parms'] );
	}
	
	function searchByAttributes( $search, $paramters )
	{
	 	global $model_factory;
 		
 		$length_constraint = 2;
 		
 		$search = IteratorBase::Utf8ToWin($search);

 		$searchparms = preg_split('/,/', $paramters);
 		
		$results = array();
		$this->items = 0;
		$last_url = '';
		
		$searchable = $model_factory->getObject('SearchableObjectSet');
		
		$searchable_it = $searchable->getAll();
		
		while( !$searchable_it->end() )
		{
			$object = getFactory()->getObject($searchable_it->get('ReferenceName'));
			$object->setRegistry( new ObjectSearchRegistry() );
			
			if( !getFactory()->getAccessPolicy()->can_read($object) ) 
			{
				$searchable_it->moveNext();
				continue;
			}

			if ( count($searchparms) > 0 && !in_array(get_class($object), $searchparms) )
			{
				$searchable_it->moveNext();
			    continue;
			}
			
			if ( is_numeric($search) )
			{
				$exact_object_it = $object->getRegistry()->Query(
						array(
								new FilterInPredicate($search),
								new FilterVpdPredicate(),
								new SortRecentClause() 
						)
				);
			}

			if ( strlen($search) > $length_constraint )
			{
				$object_it = $object->getRegistry()->Query(
						array(
								new FilterSearchAttributesPredicate($search, $searchable_it->get('attributes')),
								new FilterVpdPredicate(),
								new SortRecentClause()
						)
				);
				
				if ( is_a($object, 'WikiPage') )
				{
					$oldset = $object_it->getRowset();
					
					$newset = array();
					
					$ref_name = $object->getReferenceName();
					
					foreach( $oldset as $key => $row )
					{
						if ( $row['ReferenceName'] == $ref_name ) $newset[] = $row; 
					}
					
					$object_it = $object->createCachedIterator($newset);
				}
			}
			
			$exact_found = is_object($exact_object_it) && $exact_object_it->count() > 0
				&& is_a($exact_object_it->object, get_class($object));

			if ( $exact_found )
			{
				$results[$searchable_it->getId()] = array (
				    'object' => $object->createCachedIterator($exact_object_it->getRowset())
				); 
					
				$this->items += 1;
			}
			
			if ( is_object($object_it) && $object_it->count() > 0 )
			{
				$attributes = $searchable_it->get('attributes');
				
				$results[$searchable_it->getId()] = array (
					'object' => $object->createCachedIterator($object_it->getRowset()),
					'attributes' => $attributes
				);
					
				$this->items += $object_it->count();
			}
			
			$searchable_it->moveNext();
		}

		if ( $this->items == 1 && $parms['select'] != 'true' )
		{
			$values = array_values($results);
			
			$url = $values[0]['object']->getViewUrl();
			
			if ( $url[0] == '/' || strstr($url, 'http') )
			{
				$this->replyRedirect($url, text(1309));
			}
			else
			{
			    $session = getSession();
			    
				$this->replyRedirect($session->getApplicationUrl().$url, text(1309));
			}
		}
		else
		{
			if ( $this->items < 1 )
			{
		 		if ( strlen($search) < $length_constraint + 1 )
		 		{
					$this->replyError(text(1252));
		 		}
		 		else
		 		{
					$this->replyError(text(1253));
		 		}
			}
			else
			{ 	
			    $this->replyResults( $results, $search );
			}
		}
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
	    global $model_factory;
	    
	    $uid = new ObjectUID;
	    
	    $size = 180;
	    
		$report = $model_factory->getObject('PMReport');
		
	    $searchable = $model_factory->getObject('SearchableObjectSet');
	    
		$searchable_it = $searchable->getAll();
		
		foreach ( $results as $entity => $result )
		{
			$object = $model_factory->getObject($entity);

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
	                
    	            $position = strpos($text, $search); 
	                
	                if ( $position === false ) continue;
	                
	                $text = str_replace($search, '<strong>'.$search.'</strong>', 
	                    substr($text, max(0, $position - $size), strlen($search) + $size));
                    
    	            $html .= '<td>'.translate($object_it->object->getAttributeUserName($attribute)).': '.$text.'</td>';

    	            break;
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
