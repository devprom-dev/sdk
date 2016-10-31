<?php

include_once "FormTaskEmbedded.php";

class FormRequestTasksEmbedded extends FormTaskEmbedded
{
	const MAX_VISIBLE_TASKS = 5;
	
	private $terminal_states = array();
	private $hidden_tasks = 0;
    private $releaseId = '';
	
 	protected function extendModel()
 	{
 		$this->terminal_states = $this->getObject()->getTerminalStates();
 	}

 	function setRelease( $value ) {
 	    $this->releaseId = $value;
    }

	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'Release':
 				return true;

 			default:
 				return parent::IsAttributeVisible( $attribute ); 
 		}
 	}
 	
 	function getItemDisplayName( $object_it )
 	{
 		$uid = new ObjectUID;
		$text = $uid->getUidIcon( $object_it );
		$text .= ' '.$object_it->getWordsOnlyValue($object_it->getDisplayNameNative(), 15);
		if ( $object_it->get('StateName') != '' ) $text .= ' ('.$object_it->get('StateName').')';
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
                if ( $value != '' ) return $value;
                return getFactory()->getObject('IterationActual')->getRegistry()->Query(
                        array (
                            new FilterAttributePredicate('Version', $this->releaseId),
                            new FilterVpdPredicate()
                        )
                    )->getId();
            default:
                return $value;
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
				'name' => translate('Изменить'),
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
			return text(1014).' '.str_replace('%1', $this->hidden_tasks, text(1935));
		} else {
			return parent::getListItemsTitle();
		}
	}
}
