<?php
 
include_once SERVER_ROOT_PATH."pm/methods/c_request_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/c_state_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/DuplicateIssuesWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/c_wiki_methods.php";
include_once SERVER_ROOT_PATH."cms/c_form_embedded.php";
include_once SERVER_ROOT_PATH.'core/methods/BulkDeleteWebMethod.php';
include_once SERVER_ROOT_PATH.'pm/classes/workflow/WorkflowModelBuilder.php';

 ////////////////////////////////////////////////////////////////////////////////////////////////////
 class BulkComplete extends CommandForm
 {
 	private $object_it = null;
 	
 	function getObjectIt()
 	{
 		if ( is_object($this->object_it) ) return $this->object_it;
 		
 		getSession()->addBuilder( new WorkflowModelBuilder() );
 		
		$object = getFactory()->getObject( $_REQUEST['object'] );
		
		if ( !is_a($object, 'Metaobject') ) $this->replyError( text(1061) );
		
		$this->object_it = $object->getExact( preg_split('/-/', trim($_REQUEST['ids'], '-')) );
		
		return $this->object_it;
 	}
 	
 	function validate()
 	{
 	    global $model_factory;
 	    
		$this->checkRequired( array('ids', 'object', 'operation') );
		
		$object_it = $this->getObjectIt();

		if ( !getFactory()->getAccessPolicy()->can_modify($object_it) ) $this->replyError( text(1062) );
				
		$data = $this->getOperationData( $object_it );
		
		if ( $data['operation'] == '' ) throw new Exception('Unknown operation type on bulk update'); 
		
		if ( $data['operation'] == 'Transition' )
		{
			foreach( $data['attributes'] as $attribute => $value )
			{
			    if ( $value == '' ) $this->replyError(text(2).': '.$object_it->object->getAttributeUserName($attribute));
			}
		}
		
		return true;
 	}
 	
 	function getOperationData( $object_it )
 	{
 	    global $model_factory;
 	    
 	    $data = array();
 	    
 	    $attributes = array();
 	    
		if ( preg_match('/Attribute(.+)/mi', $_REQUEST['operation'], $attributes) )
		{
		    $data['operation'] = 'Attribute';
		    
		    $data['attributes'] = array (
		            $attributes[1] => IteratorBase::utf8towin($_REQUEST[$attributes[1]])
		    );
		}

		if ( preg_match('/Transition(.+)/mi', $_REQUEST['operation'], $ids) )
		{
		    $data['operation'] = 'Transition';
		    
		    $data['parameter'] = $ids[1];
		    
		    $data['attributes'] = array();
		    
		    $object = $model_factory->getObject( $_REQUEST['object'] );
		    
		    foreach( $_REQUEST as $key => $value )
			{
			    if ( $object->getAttributeType($key) == '' ) continue;
			    
			    $data['attributes'][$key] = IteratorBase::utf8towin($value);
			}
			
			$transition_it = getFactory()->getObject('Transition')->getExact( trim($data['parameter']) );
			
			if ( $transition_it->get('IsReasonRequired') == 'Y' )
			{
				$data['attributes']['TransitionComment'] = IteratorBase::utf8towin($_REQUEST['TransitionComment']);
   			}
		}
		
		if ( preg_match('/Method:(.+)/mi', $_REQUEST['operation'], $attributes) )
		{ 
		    $data['operation'] = 'Method';
		    
		    $data['parameter'] = $attributes[1];
		}		
		
		return $data;
 	}
 	
 	function create()
	{
		global $_REQUEST, $_SERVER, $model_factory;
		
		$except_items = array();

		$object_it = $this->getObjectIt();
		$object_it->object->removeNotificator( 'EmailNotificator' );
		
		$data = $this->getOperationData( $object_it );
		
		switch ( $data['operation'] )
		{
		    case 'Attribute':
		        $key = array();
				while ( !$object_it->end() )
    			{
    				try { 
	    		        $this->processEmbeddedForms( $object_it, $key );
	    			    $object_it->object->modify_parms($object_it->getId(), $data['attributes']); 
    				}
    				catch( Exception $e ) {
	   					$except_items[] = array (
	   							'it' => $object_it->copy(),
	   							'ex' => $e
	   					); 
    				}
    				$object_it->moveNext();
    			}
		        
		        break;
		        
		    case 'Transition':
		    	$transition_it = getFactory()->getObject('Transition')->getExact($data['parameter']);
		    	$target_state = $transition_it->getRef('TargetState')->get('ReferenceName');

				while ( !$object_it->end() )
    			{
    				try {
	    				$method = new TransitionStateMethod($transition_it, $object_it);
	    				
	    				ob_start();
	
						$method->execute( 
								$transition_it->getId(), 
								$object_it->getId(), 
								get_class($object_it->object), 
								$data['attributes']
						);
	    
	    				ob_end_clean();
    				}
    				catch( Exception $e ) {
    					$except_items[] = array (
    							'it' => $object_it->copy(),
    							'ex' => $e
    					); 
    				}
    				$object_it->moveNext();
    			}
    			
			    getFactory()->getEventsManager()->
			    		executeEventsAfterBusinessTransaction(
			    				$object_it->object->getRegistry()->Query(
			    						array (
			    								new FilterInPredicate($object_it->idsToArray())
			    						)
			    				), 'WorklfowMovementEventHandler'
    					);
		        
			    break;
			    
		    case 'Method':

				$parms = preg_split('/:/', $data['parameter']);
			
    			$class_name = $parms[0];
    			 
    			array_shift($parms);
    			
    			$attrs = array();
    			
    			if ( count($parms) > 0 )
    			{
    				foreach( $parms as $parm )
    				{
    					$pair = preg_split('/=/', $parm);
    					
    					$attrs[$pair[0]] = $pair[1] != '' ? $pair[1] : $_REQUEST[$pair[0]];
    				}
    			}
    			
    			$_REQUEST = array_merge( $_REQUEST, $attrs );

    			try {
	    			$method = new $class_name( $object_it );
	    				
	   				// as standalone the method may to echo some text 
	   				ob_start();
	    				
	   				$method->execute_request();
	   				if ( strpos($method->getRedirectUrl(), '/') !== false )
	   				{
	   					$_REQUEST['redirect'] = $method->getRedirectUrl();
	   				}
	   				ob_end_clean();
    			}
				catch( Exception $e ) {
   					$except_items[] = array (
   							'it' => $object_it->copy(),
   							'ex' => $e
   					); 
    			}
    			break;
		}
		
		if ( false && count($except_items) == $object_it->count() )
		{
			$reasons = array();
			foreach( $except_items as $item )
			{
				$reasons[] = $item['ex']->getMessage();
			}
			$this->replyError( 
					preg_replace('/%1/',join('<br/>', array_unique($reasons)),text(1926))
			);
		}
		else if ( count($except_items) > 0 )
		{
			$uid = new ObjectUID;
			$items = array();
			$reasons = array();
			foreach( $except_items as $item )
			{
				$items[] = $uid->getUidWithCaption($item['it']);
				$reasons[] = $item['ex']->getMessage();
			}
			$this->replyError( 
					preg_replace('/%2/', join('<br/>', array_unique($reasons)),
							preg_replace('/%1/',join('<br/>', $items),text(1925))
						) 
			);
		}
		
		if ( $_REQUEST['redirect'] != '' )
		{
			$this->replyRedirect( $_REQUEST['redirect'], text(496) );
		}
		else
		{
			$this->replySuccess( text(496) );
		}
	}
	
	function processEmbeddedForms( $object_it, & $key )
	{
	    $embedded = new FormEmbedded();
	    
        $embedded->process( $object_it, function( $object, $field_id, $anchor_field, $prefix, $id ) use ($object_it, &$key) 
        {
            global $model_factory;
            
            // this is used for massive deletion/modification of the binds 
            if ( $_REQUEST[$field_id] != '' )
            {
                if ( count($key) < 1 )
                {
                    $embedded_it = $object->getExact($_REQUEST[$field_id]);
                    
                    // get alternative key of the binded object
                    $key = $embedded_it->getAlternativeKey();
                }
                else
                {
                    // update anchor field in the alternative key;
                    $key[$anchor_field] = $object_it->getId();
    
                    // get binded object for the given object_it
                    $ref_embedded = $model_factory->getObject(get_class($object));
                             
                    $embedded_it = $ref_embedded->getByRefArray( $key );
    
                    // reset key field of the binded object to the new one
                    $_REQUEST[$field_id] = $embedded_it->getId();
                }
            }
        });
	}
}
