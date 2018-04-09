<?php

include_once "FormTaskEmbedded.php";

class FormRequestTasksEmbedded extends FormTaskEmbedded
{
	const MAX_VISIBLE_TASKS = 5;
	
	private $terminal_states = array();
	private $hidden_tasks = 0;
    private $releaseId = '';
	
 	public function extendModel()
 	{
        parent::extendModel();
 		$this->terminal_states = $this->getObject()->getTerminalStates();
 	}

 	function setRelease( $value ) {
 	    $this->releaseId = $value;
    }

 	function getItemDisplayName( $object_it )
 	{
 		$uid = new ObjectUID;
		$text = $uid->getUidIcon( $object_it );
		$text .= ' '.$object_it->getDisplayNameNative();
 		return $text;
 	}
 	
 	function getItemVisibility( $object_it )
 	{
 		if ( $object_it->getPos() >= self::MAX_VISIBLE_TASKS && in_array($object_it->get('State'), $this->terminal_states) )
 		{
 			$this->hidden_tasks++;
 			return false;
 		}
 		return parent::getItemVisibility( $object_it );
 	}

    function getFieldValue( $attr )
    {
        $value = parent::getFieldValue( $attr );
        switch( $attr ) {
            case 'Release':
                if ( $value != '' || $this->releaseId == '' ) return $value;
                return getFactory()->getObject('IterationActual')->getRegistry()->Query(
                        array (
                            new FilterAttributePredicate('Version', $this->releaseId),
                            new FilterVpdPredicate()
                        )
                    )->getId();
            case 'Priority':
                if ( $value != '' ) return $value;
                $object_it = $this->getObjectIt();
                if ( is_object($object_it) && $object_it->getId() > 0 ) {
                    return $object_it->get('Priority');
                }
                return parent::getFieldValue( $attr );
            default:
                return $value;
        }
    }

    function IsAttributeObject( $attr ) {
        switch ($attr) {
            case 'Planned':
                return true;
            default:
                return parent::IsAttributeObject( $attr );
        }
    }

 	function createField( $attr )
 	{
 	    switch ( $attr )
 	    {
 	        case 'Release':
 	            $object = getFactory()->getObject('IterationActual');
                $object->addFilter( new FilterAttributePredicate('Version', $this->releaseId) );
				return new FieldDictionary( $object );

            case 'Planned':
                return new FieldHours();

            default:
 	            return parent::createField( $attr );
 	    }
 	} 
 	
 	function getActions( $object_it, $item )
 	{
 		return array_merge( $this->getTaskActions($object_it), parent::getActions( $object_it, $item ));
 	}
 	
	function getTaskActions( $object_it )
	{
		$actions = array();

        if ( $_REQUEST['formonly'] != '' ) {
            $actions[] = array (
                'name' => translate('Открыть'),
                'url' => $object_it->getViewUrl(),
                'target' => defined('SKIP_TARGET_BLANK') && SKIP_TARGET_BLANK ? '' : '_blank'
            );
            $actions[] = array();
            return $actions;
        }

		$todo = ($_REQUEST['formonly'] != '' ? 'donothing' : 'function() { window.location.reload(); }');

		$method = new ObjectModifyWebMethod($object_it);
		$method->setRedirectUrl($todo);
		$actions[] = array (
				'name' => $method->getCaption(),
				'url' => $method->getJSCall()
		);
		
		$state_it = $object_it->getStateIt();
		$transition_it = $state_it->getTransitionIt();

		$need_separator = true;
		while ( !$transition_it->end() )
		{
			$method = new TransitionStateMethod( $transition_it, $object_it );
			
			if ( $method->hasAccess() )
			{
				if ( $need_separator )
				{
					$actions[] = array();
					
					$need_separator = false;
				}

				$method->setRedirectUrl($todo);
				
				$actions[] = array( 
					'url' => $method->getJSCall(),
					'name' => $method->getCaption()
				);
			}
			
			$transition_it->moveNext();
		}
		
		$actions[] = array();
		
		return $actions;
	}

	function getListItemsTitle() {
		if ( $this->hidden_tasks > 0 ) {
			return str_replace('%1', $this->hidden_tasks, text(2028));
		} else {
			return parent::getListItemsTitle();
		}
	}

    function drawAddButton( $view, $tabindex )
    {
        parent::drawAddButton( $view, $tabindex );

        if( $this->getIteratorRef()->count() > 0 ) {
            $boardIt = getFactory()->getObject('Module')->getExact('tasks-board');
            if ( $boardIt->getId() != '' ) {
                echo '<a class="dashed embedded-add-button" style="margin-left:20px;" target="_blank" href="'.$boardIt->getUrl().'" tabindex="-1">';
                echo mb_strtolower($boardIt->getDisplayName());
                echo '</a>';
            }
        }
    }
}
