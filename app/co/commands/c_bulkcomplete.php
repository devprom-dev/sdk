<?php
include_once SERVER_ROOT_PATH."cms/c_form.php";
include_once SERVER_ROOT_PATH.'core/methods/BulkDeleteWebMethod.php';

class BulkComplete extends CommandForm
{
 	private $object_it = null;
 	
 	function getObjectIt()
 	{
 		if ( is_object($this->object_it) ) return $this->object_it;
		return $this->object_it = $this->buildObject();
 	}

 	function buildObject() {
        $object = getFactory()->getObject( $_REQUEST['object'] );
        if ( !is_a($object, 'Metaobject') ) $this->replyError( text(1061) );
        return $object->getExact(\TextUtils::parseIds($_REQUEST['ids']));
    }
 	
 	function validate()
 	{
		$this->checkRequired( array('ids', 'object', 'operation') );
		
		$object_it = $this->getObjectIt();
		$data = $this->getOperationData( $object_it );
		
		if ( $data['operation'] == '' ) throw new Exception('Unknown operation type on bulk update'); 

		return true;
 	}
 	
 	function getOperationData( $object_it )
 	{
 	    $data = array();
 	    $attributes = array();
 	    
		if ( preg_match('/^Attribute(.+)$/mi', $_REQUEST['operation'], $attributes) ) {
		    $data['operation'] = 'Attribute';
			$attributes = preg_split('/:/', $attributes[1]);
			$attribute = array_shift($attributes);
		    
		    $data['attributes'] = array (
				$attribute => $_REQUEST[$attribute]
		    );
		}

		if ( preg_match('/^Transition(.+)$/mi', $_REQUEST['operation'], $ids) )
		{
		    $data['operation'] = 'Transition';
		    $data['parameter'] = $ids[1];
		    $data['attributes'] = array();

            $transition_it = getFactory()->getObject('Transition')->getExact( trim($data['parameter']) );
            $object = getFactory()->getObject( $_REQUEST['object'] );

            $model_builder = new WorkflowTransitionAttributesModelBuilder($transition_it);
            $model_builder->build($object);
		    
		    foreach( $_REQUEST as $key => $value )
			{
			    if ( $object->getAttributeType($key) == '' ) continue;
                if ( $value == '' && !$object->IsAttributeRequired($key) ) continue;
			    $data['attributes'][$key] = $value;
			}

			$specifyTransition =
                $transition_it->get('IsReasonRequired') == TransitionReasonTypeRegistry::Required
                || $transition_it->get('IsReasonRequired') == TransitionReasonTypeRegistry::Visible && $_REQUEST['TransitionComment'] != '';

			if ( $specifyTransition ) {
				$data['attributes']['TransitionComment'] = $_REQUEST['TransitionComment'];
   			}
		}
		
		if ( preg_match('/^Method:(.+)$/mi', $_REQUEST['operation'], $attributes) ) {
		    $data['operation'] = 'Method';
		    $data['parameter'] = $attributes[1];
		}		
		
		return $data;
 	}
 	
 	function create()
	{
		global $_REQUEST, $_SERVER;
		
		$except_items = array();
        DAL::Instance()->Query("SET autocommit=0");

		$object_it = $this->getObjectIt();
		$object_it->object->removeNotificator( 'EmailNotificator' );
		
		$data = $this->getOperationData( $object_it );
		
		switch ( $data['operation'] )
		{
		    case 'Attribute':
                if ( !getFactory()->getAccessPolicy()->can_modify($object_it) ) $this->replyError( text(1062) );

				$attribute = array_pop(array_keys($data['attributes']));
                $processedIds = array();

				if ( $attribute == 'Project' && $object_it->object instanceof WikiPage ) {
					$object_it = $object_it->object->getRegistry()->useImportantPersistersOnly()->Query(
						array (
                            join(',',array_unique($object_it->fieldToArray('ParentPage'))) != ''
                                ? new ParentTransitiveFilter($object_it->idsToArray())
                                : new SortDocumentClause(),
                            new FilterAttributePredicate('DocumentId', $object_it->fieldToArray('DocumentId')),
                            new SortDocumentClause()
                        )
					);

                    try {
                        getFactory()->getEventsManager()->delayNotifications();

                        while ( !$object_it->end() ) {
                            $storedIt = $this->processItemAttributes($object_it, $data);
                            getFactory()->getEventsManager()->notify_object_add($storedIt, $data);
                            $processedIds[] = $storedIt->getId();
                            $object_it->moveNext();
                        }

                        getFactory()->getEventsManager()->releaseNotifications();
                        DAL::Instance()->Query("COMMIT");
                    }
                    catch( \Exception $e ) {
                        DAL::Instance()->Query("ROLLBACK");
                        $except_items[] = array (
                            'it' => $object_it->copy(),
                            'ex' => $e
                        );
                        \Logger::getLogger('System')->error($e->getMessage());
                        \Logger::getLogger('System')->error($e->getTraceAsString());
                    }
				}
				else {
                    while ( !$object_it->end() ) {
                        try {
                            $this->processItemAttributes($object_it, $data);
                            $processedIds[] = $object_it->getId();
                            DAL::Instance()->Query("COMMIT");
                       }
                        catch( \Exception $e ) {
                            DAL::Instance()->Query("ROLLBACK");
                            $except_items[] = array (
                                'it' => $object_it->copy(),
                                'ex' => $e
                            );
                            \Logger::getLogger('System')->error($e->getMessage());
                            \Logger::getLogger('System')->error($e->getTraceAsString());
                        }
                        $object_it->moveNext();
                    }
                }

                if ( count($processedIds) > 0 ) {
					$processedIt = $object_it->object->getExact($processedIds);
					if ( $_REQUEST['OpenList'] != '' && $processedIt->count() > 0 ) {
						if ( $processedIt->count() == 1 || ($attribute == 'Project' && $object_it->object instanceof WikiPage) ) {
							$_REQUEST['redirect'] = $processedIt->getUidUrl();
						}
						else {
                            $url = WidgetUrlBuilder::Instance()->buildWidgetUrlIt($processedIt);
                            if ( $url != '' ) {
                                $_REQUEST['redirect'] = $url;
                            }
						}
					}
				}
		        break;
		        
		    case 'Transition':
		    	$transition_it = getFactory()->getObject('Transition')->getExact($data['parameter']);
		    	foreach( array('IsPrivate', 'TransitionNotification', 'TransitionNotificationOnForm') as $attribute ) {
                    $data['attributes'][$attribute] = $_REQUEST[$attribute];
                }

                $emptyOnlyAttributes = array();
                foreach( $data['attributes'] as $attribute => $value ) {
                    $actualData = array_unique($object_it->fieldToArray($attribute));
                    if ( count($actualData) > 1 ) {
                        if ( in_array('', $actualData) ) {
                            $emptyOnlyAttributes[$attribute] = $value;
                        }
                        unset($data['attributes'][$attribute]);
                    }
                }

                getFactory()->transformEntityData($object_it->object, $data['attributes']);

                $object_it->moveFirst();
				while ( !$object_it->end() )
    			{
    				try {
    				    $attributes = $data['attributes'];
    				    foreach( $emptyOnlyAttributes as $attribute => $value ) {
    				        if ( $object_it->get($attribute) == '' ) {
                                $attributes[$attribute] = $value;
                            }
                        }

                        $key = array();
    					$this->processEmbeddedForms( $object_it, $key );

	    				ob_start();
	    				$method = new TransitionStateMethod($transition_it, $object_it);
                        if ( !$method->hasAccess() ) {
                            throw new \Exception($method->getReasonHasNoAccess());
                        }
	    				$method->execute(
                            $transition_it->getId(),
                            $object_it->getId(),
                            get_class($object_it->object),
                            $attributes,
                            false
						);
	    				ob_end_clean();
                        DAL::Instance()->Query("COMMIT");
                    }
    				catch( Exception $e ) {
                        DAL::Instance()->Query("ROLLBACK");
    					$except_items[] = array (
    							'it' => $object_it->copy(),
    							'ex' => $e
    					);
                        \Logger::getLogger('System')->error($e->getMessage());
                        \Logger::getLogger('System')->error($e->getTraceAsString());
    				}
                    \ZipSystem::sendResponse();
    				$object_it->moveNext();
    			}
                break;
			    
		    case 'Method':
				$parms = preg_split('/:/', $data['parameter']);
			
    			$class_name = $parms[0];
    			array_shift($parms);
    			
    			$attrs = array();
    			if ( count($parms) > 0 ) {
    				foreach( $parms as $parm ) {
    					$pair = preg_split('/=/', $parm);
    					$attrs[$pair[0]] = $pair[1] != '' ? $pair[1] : $_REQUEST[$pair[0]];
    				}
    			}
    			
    			$_REQUEST = array_merge( $_REQUEST, $attrs );
    			try {
	    			$method = new $class_name( $object_it );
                    if ( !$method->hasAccess() ) throw new Exception(text(1062));
                    \FeatureTouch::Instance()->touch(strtolower(get_class($method)));

					if ( $method instanceof BulkDeleteWebMethod ) {
                        ob_start();
						$method->execute_request();
                        $methodResponse = ob_get_contents();
                        ob_end_clean();
					}
					else {
						// as standalone the method may to echo some text
						ob_start();
						$method->execute_request();
                        DAL::Instance()->Query("COMMIT");
						$methodResponse = ob_get_contents();
                        ob_end_clean();

						if ( strpos($method->getRedirectUrl(), '/') !== false ) {
							$_REQUEST['redirect'] = $method->getRedirectUrl();
						}
					}

                    if ( count(JsonWrapper::decode($methodResponse)) > 0 ) {
                        echo $methodResponse;
                        exit();
                    }
                }
				catch( Exception $e ) {
                    DAL::Instance()->Query("ROLLBACK");
    			    while( !$object_it->end() ) {
                        $except_items[] = array(
                            'it' => $object_it->copy(),
                            'ex' => $e
                        );
                        $object_it->moveNext();
                    }
                    \Logger::getLogger('System')->error($e->getMessage());
                    \Logger::getLogger('System')->error($e->getTraceAsString());
    			}
    			break;
		}

		if ( count($except_items) > 0 )
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

        DAL::Instance()->Query("COMMIT");
        DAL::Instance()->Query("SET autocommit=1");

        $lock = new LockFileSystem(get_class($object_it->object));
        $lock->Release();
		
		if ( $_REQUEST['redirect'] != '' )
		{
			$this->replyRedirect( $_REQUEST['redirect'] );
		}
		else
		{
			$this->replySuccess();
		}
	}
	
	function processEmbeddedForms( $object_it, & $key )
	{
        $form = new Form($object_it->object);
        $form->processEmbeddedForms($object_it, function( $object, $field_id, $anchor_field, $prefix, $id ) use ($object_it, &$key)
        {
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
                    $ref_embedded = getFactory()->getObject(get_class($object));

                    $embedded_it = $ref_embedded->getByRefArray( $key );

                    // reset key field of the binded object to the new one
                    $_REQUEST[$field_id] = $embedded_it->getId();
                }
            }
        });
	}

	function processItemAttributes( $object_it, $data )
    {
        if ( $object_it->object instanceof MetaobjectStatable ) {
            $stateIt = $object_it->getStateIt();
            if ( $stateIt->getId() != '' ) {
                $model_builder = new WorkflowStateAttributesModelBuilder(
                    $stateIt, array_keys($data['attributes'])
                );
                $tempObject = getFactory()->getObject(get_class($object_it->object));
                $model_builder->build($tempObject);
                foreach( $data['attributes'] as $key => $value ) {
                    if ( !$tempObject->getAttributeEditable($key) ) {
                        throw new Exception(sprintf(text(3016), $tempObject->getAttributeUserName($key)));
                    }
                }
            }
        }

        $key = array();
        $this->processEmbeddedForms( $object_it, $key );
        $objectIt = getFactory()->modifyEntity($object_it, $data['attributes']);

        \ZipSystem::sendResponse();
        return $objectIt;
    }
}
