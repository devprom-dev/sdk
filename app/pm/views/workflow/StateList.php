<?php

class StateList extends PMPageList
{
    function extendModel()
    {
        $this->getObject()->setAttributeVisible('IsTerminal', false);
        $this->getObject()->setAttributeOrderNum('Transitions', 99999);
        parent::extendModel();
    }

    function drawRefCell( $entity_it, $object_it, $attr )
    {
        $view = $this->getRenderView();

        switch ( $attr )
        {
            case 'Transitions':
                while ( !$entity_it->end() )
                {
                	$actions = array();
                	$transition_it = $entity_it->copy();

                    $object_it->object->setVpdContext($object_it);

			        $method = new ObjectModifyWebMethod($transition_it);
                    $method->setRedirectUrl('function() {window.location.reload();}');
                    $actions[] = array (
                        'url' => $method->getJSCall(),
                        'name' => $method->getCaption()
                    );

                	$method = new DeleteObjectWebMethod($transition_it);
                    $method->setRedirectUrl('function() {window.location.reload();}');
					if ( $method->hasAccess() ) {
						$actions[] = array();
					    $actions[] = array(
						    'name' => $method->getCaption(),
					    	'url' => $method->getJSCall()
					    );
					}

                    echo $view->render('core/TextMenu.php', array (
						'title' => $transition_it->getFullName(),
						'items' => array_merge( array(), $actions ),
						'random' => $transition_it->getId()
                    ));

					$specificSettings = $transition_it->get('Actions').
                        $transition_it->get('Predicates').
                        $transition_it->get('ProjectRoles');

					if ( $specificSettings != '' ) {
                        echo '<div class="well well-small" style="margin-left:13px;">';
                            $needSeparator = false;
                            $roleIt = $transition_it->getRef('ProjectRoles');
                            while( !$roleIt->end() ) {
                                echo $roleIt->getDisplayName();
                                echo '<br/>';
                                $needSeparator = true;
                                $roleIt->moveNext();
                            }
                            $predicateIt = $transition_it->getRef('Predicates');
                            if ( $predicateIt->count() > 0 && $needSeparator ) {
                                echo '<hr/>';
                                $needSeparator = false;
                            }
                            while( !$predicateIt->end() ) {
                                echo $predicateIt->getDisplayName();
                                echo '<br/>';
                                $needSeparator = true;
                                $predicateIt->moveNext();
                            }
					        $actionIt = $transition_it->getRef('Actions');
                            if ( $actionIt->count() > 0 && $needSeparator ) {
                                echo '<hr/>';
                            }
                            while( !$actionIt->end() ) {
                                echo $actionIt->getDisplayName();
                                echo '<br/>';
                                $actionIt->moveNext();
                            }
					    echo '</div>';
                    }

                    echo '<div class="clear-fix"></div>';

                    $entity_it->moveNext();
                }
                break;
                
            default:
                parent::drawRefCell( $entity_it, $object_it, $attr );
        }
    }

    function getItemActions( $column_name, $object_it )
    {
        $actions = parent::getItemActions($column_name, $object_it);

        $object = getFactory()->getObject('Transition');
        $object->setVpdContext($object_it);

        $method = new ObjectCreateNewWebMethod($object);
        $method->setRedirectUrl('function() {window.location.reload();}');
        if ( $method->hasAccess() ) {
			if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
        	$actions[] = array (
        			'name' => text(891),
        			'url' => $method->getJSCall(
        							array (
        									'SourceState' => $object_it->getId() 
        							)
        					)
        	);
        }

        return $actions;
    }

    function getColumnFields()
    {
    	$fields = array_merge(
    	    parent::getColumnFields(),
            array(
                'ReferenceName'
            )
        );

    	if ( !$this->getObject()->IsAttributeVisible('QueueLength') ) {
			unset($fields[array_search('QueueLength', $fields)]);
		}

    	return $fields;
    }
    
	function getGroupDefault() {
		return 'none';
	}
	
	function getColumnWidth( $attribute )
	{
		switch( $attribute )
		{
		    case 'Actions':
		    	return '20%';
		    	
		    default:
		    	return parent::getColumnWidth( $attribute );
		}
	}
}