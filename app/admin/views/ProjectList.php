<?php

class ProjectList extends PageList
{
    function extendModel()
    {
        $this->getObject()->addAttribute('Coordinators', 'REF_cms_UserId', translate('Координаторы'), true);
        $this->getObject()->addPersister( new ProjectLeadsPersister() );

        $visible = array( 'Caption', 'CodeName', 'Coordinators', 'IsClosed' );
        foreach( array_keys($this->getObject()->getAttributes()) as $attribute ) {
            if ( in_array($attribute, $visible) ) continue;
            $this->getObject()->setAttributeVisible($attribute, false);
        }

        $this->getObject()->setAttributeVisible('UID', true);

        parent::extendModel();
    }

	function IsNeedToModify( $object_it ) {
	    return false; 
	}

	function getItemActions( $column_name, $object_it )
	{
		$actions = parent::getItemActions( $column_name, $object_it );

		$method = new ModifyAttributeWebMethod($object_it, 'IsClosed', $object_it->get('IsClosed') == 'Y' ? 'N' : 'Y');
		if ( $method->HasAccess() ) {
			if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
		    $actions[] = array(
    		    'url' => $method->getJSCall(),
		    	'name' => $object_it->get('IsClosed') == 'Y' ? text(1320) : text(1319)
		    );
		}

		return $actions;
	}

	function drawCell( $object_it, $attr )
	{
		switch( $attr )
		{
		    case 'IsClosed':
		        echo $object_it->get('IsClosed') == 'Y' ? translate('Закрыт') : translate('Открыт');
		        break;
		        
		    default:
		        parent::drawCell( $object_it, $attr );
		}
	}

	function getGroupDefault()
	{
		return '';
	}
}

