<?php
include_once "WebMethod.php";
include_once SERVER_ROOT_PATH."pm/classes/project/predicates/ProjectAccessibleActiveVpdPredicate.php";

class AutocompleteWebMethod extends WebMethod
{
 	var $method_name, $object, $title;
 	
 	function AutocompleteWebMethod ( $object = null, $title = '' )
 	{
 		$this->method_name = md5(get_class($object).$this->getMethodName());
 		$this->object = $object;
		
		$this->title = $title == '' ? ( is_object($this->object) ? $this->object->getDisplayName() : '') : $title;
 		
 		parent::WebMethod();
 	}

 	function hasAccess()
 	{
 		return true;
 	}

	function getMethodName()
	{
		return $this->method_name;
	}
	
	function getTitle()
	{
		return $this->title;
	}
	
	function getCaption()
	{
		return $this->getTitle();
	}
	
 	function exportHeaders()
 	{
		header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header('Content-type: text/html; charset=utf-8');
 	}
	
 	function getResult( $object_it, $attributes )
 	{
		$object_uid = new ObjectUid;
 	    
		$result = array();
		
 	    while( !$object_it->end() )
	    {
	    	$caption = $object_uid->getUidTitle($object_it);
	    	
	        if ( $object_uid->hasUid($object_it) )
 			{
 			    $info = $object_uid->getUIDInfo($object_it);
 			    $completed = $info['completed'];
 			}
 			else
 			{
 				$completed = false;
 			}
	    	
 			 $result_item = array (
 			    'id' => html_entity_decode($object_it->getId(), ENT_COMPAT | ENT_HTML401, 'utf-8'),
 			    'label' => html_entity_decode($caption, ENT_COMPAT | ENT_HTML401, 'utf-8'),
 			    'completed' => $completed
 			);
 			
 			foreach( preg_split('/,/', $attributes) as $attribute )
 			{
 				if ( $object_it->get($attribute) == '' && $object_it->object->getAttributeType($attribute) == '' ) continue;
 				
 				$result_item[$attribute] = $object_it->get($attribute);
 			}
 			
 			$result[] = $result_item;
	        
	        $object_it->moveNext();
	    }
	    
	    return $result;
 	}
 	
 	function execute_request()
 	{
		$object_uid = new ObjectUid;
		
		$attributes = preg_split('/,/', $_REQUEST['attributes']);
		
		if ( $_REQUEST['attributes'] == '' || count($attributes) < 1 ) $attributes = array('Caption');

 		$object = getFactory()->getObject($_REQUEST['class']);

		if ( $object->getVpdValue() != '' ) {
			$queryParms = array(
				in_array('cross', $attributes) ? new \ProjectAccessibleActiveVpdPredicate() : new FilterVpdPredicate()
			);
		}
 		if ( is_a($object, 'MetaobjectStatable') ) {
			$queryParms[] = new SortMetaStateClause();
 		}
		if ( in_array('cross', $attributes) ) {
			$queryParms[] = new SortProjectSelfFirstClause();
			$queryParms[] = new SortProjectImportanceClause();
		}
		$registry = $object->getRegistry();
 		
 		$key = 'term';
     	
     	$_REQUEST[$key] = trim(array_pop(
     	    array_filter(
     	        preg_split('/[,;:]/', $_REQUEST[$key]),
                function($value) {
     	            return $value != "";
                }
            )
        ));

 		if ( $_REQUEST[$key] == '' )
 		{
 		    $record_count = $registry->Count($queryParms);
			if ( $record_count > 60 ) $registry->setLimit(60);
 		    $result = $this->getResult($registry->Query($queryParms), $_REQUEST['additional']);
 		}
 		else
 		{
			if ( $object_uid->isValidUid($_REQUEST[$key]) ) {
                $object_it = $object_uid->getObjectIt($_REQUEST[$key]);
                if ( $object_it->getId() != '' ) {
                    $_REQUEST[$key] = $object_it->getId();
                }
			}
			if ( is_numeric($_REQUEST[$key]) ) {
				$result_it = $registry->Query(
					array_merge(
						$queryParms,
						array (
							new FilterInPredicate($_REQUEST[$key])
						)
					)
				);
			}
			if ( !is_object($result_it) || is_object($result_it) && $result_it->getId() == '' ) {
				$queryParms[] = new FilterSearchAttributesPredicate(
					$_REQUEST[$key],
					array_unique(
					    array_merge(
                            array_intersect(
                                array_keys($object->getAttributes()),
                                array_merge(
                                    $attributes,
                                    preg_split('/,/', $_REQUEST['additional']),
                                    array (
                                        'Caption'
                                    )
                                )
    						),
                            array(
                                $object->getIdAttribute(),
                                'UID'
                            )
                        )
					)
				);
				$result_it = $registry->Query($queryParms);
			}
     		$result = $this->getResult( $result_it, $_REQUEST['additional'] );
 		}

 		echo JsonWrapper::encode($result);
 	}
}
