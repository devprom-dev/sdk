<?php

class StateList extends PMPageList
{
    function getColumns()
    {
        $this->object->addAttribute('Transitions', '', translate('Переходы'), true);
        $this->object->setAttributeVisible('IsTerminal', false);

        return parent::getColumns();
    }

    function drawCell( $object_it, $attr )
    {
        $view = $this->getRenderView();

        switch ( $attr )
        {
            case 'Transitions':
                $transition_it = $object_it->getTransitionIt();

                while ( !$transition_it->end() )
                {
                	$actions = array();
                	
                    $object_it->object->setVpdContext($object_it);
                    
			        $method = new ObjectModifyWebMethod($transition_it);
                    $method->setRedirectUrl('donothing');
                    $actions[] = array (
                        'url' => $method->getJSCall(),
                        'name' => $method->getCaption()
                    );

                	$method = new DeleteObjectWebMethod($transition_it);
					if ( $method->hasAccess() )
					{
						$method->setRedirectUrl('donothing');
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

					if ( $transition_it->get('Actions') != '' ) {
					    $actionIt = $transition_it->getRef('Actions');
					    echo '<div class="well well-small" style="margin-left:13px;">';
                            while( !$actionIt->end() ) {
                                echo $actionIt->getDisplayName();
                                echo '<br/>';
                                $actionIt->moveNext();
                            }
					    echo '</div>';
                    }
                    
                    echo '<div class="clear-fix"></div>';
                    
                    $transition_it->moveNext();
                }

                break;
                
            default:
                parent::drawCell( $object_it, $attr );
        }
    }

    function getItemActions( $column_name, $object_it )
    {
        $actions = parent::getItemActions($column_name, $object_it);

        $object = getFactory()->getObject('Transition');
        $object->setVpdContext($object_it);

        $method = new ObjectCreateNewWebMethod($object);
        if ( $method->hasAccess() )
        {
			$method->setRedirectUrl('donothing');

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
    	$fields = parent::getColumnFields();
    	
    	if ( !$this->getObject()->IsAttributeVisible('QueueLength') )
		{
			unset($fields[array_search('QueueLength', $fields)]);
		}

    	return $fields;
    }
    
	function getGroupDefault()
	{
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