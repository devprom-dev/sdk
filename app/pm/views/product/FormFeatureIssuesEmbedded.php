<?php

class FormFeatureIssuesEmbedded extends PMFormEmbedded
{
	const MAX_VISIBLE_TASKS = 20;
	
	private $terminal_states = array();
	private $hidden_tasks = 0;
	
 	public function extendModel()
 	{
 		$this->terminal_states = $this->getObject()->getTerminalStates();

 		$visible = array(
            'Caption', 'Description'
        );
		$object = $this->getObject();
		foreach( array_keys($object->getAttributes()) as $attribute )
		{
			if ( $object->IsAttributeRequired($attribute) ) continue;
			if ( in_array($attribute, $visible) ) continue;
			$object->setAttributeVisible( $attribute, false );
		}
		$object->setAttributeVisible( 'OrderNum', false );
 	}
	
 	function getItemDisplayName( $object_it )
 	{
 		$uid = new ObjectUID;
 		return $uid->getUidWithCaption( $object_it );
 	}
 	
 	function getItemVisibility( $object_it )
 	{
 		if ( $object_it->getPos() >= self::MAX_VISIBLE_TASKS && in_array($object_it->get('State'), $this->terminal_states) ) {
 			$this->hidden_tasks++;
 			return false;
 		}
 		return parent::getItemVisibility( $object_it );
 	}

 	function createField( $attr )
 	{
 	    switch ( $attr )
 	    {
 	        default:
 	            return parent::createField( $attr );
 	    }
 	} 

	function getListItemsTitle() {
		if ( $this->hidden_tasks > 0 ) {
			return str_replace('%1', $this->hidden_tasks, text(2028));
		} else {
			return parent::getListItemsTitle();
		}
	}

	function getActions($object_it, $item)
    {
        $actions = array();

        $method = new ObjectModifyWebMethod($object_it);
        if ( $method->hasAccess() ) {
            $actions[] = array(
                'name' => translate('Редактировать'),
                'url' => $method->getJSCall()
            );
            $actions[] = array();
        }

        $method = new ModifyAttributeWebMethod($object_it, 'Function');
        if ( $method->hasAccess() ) {
            $actions[] = array(
                'name' => translate('Отвязать'),
                'url' => $method->getJSCall()
            );
            $actions[] = array();
        }

        return array_merge(
            $actions,
            parent::getActions($object_it, $item)
        );
    }

    function drawAddButton( $view, $tabindex )
    {
        parent::drawAddButton( $view, $tabindex );

        $objectIt = $this->getObjectIt();
        if ( is_object($objectIt) ) {
            $method = new ObjectModifyWebMethod($objectIt);
            if ( $method->hasAccess() ) {
                $url = $method->getJSCall(
                    array(
                        'object_id' => $objectIt->getId() .'&BindIssue=true',
                        'can_delete' => 'false'
                    )
                );
                echo '<a class="dashed embedded-add-button" style="margin-left:20px;" href="'.$url.'" tabindex="-1">';
                    echo translate('связать');
                echo '</a>';
            }
        }
    }
}
