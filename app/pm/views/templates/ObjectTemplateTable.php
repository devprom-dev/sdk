<?php
include "ObjectTemplateList.php";

class ObjectTemplateTable extends SettingsTableBase
{
	function getList() {
		return new ObjectTemplateList( $this->object );
	}

    function getNewActions()
    {
        $actions = array();

        $method = new ObjectCreateNewWebMethod($this->object);
        if ( $method->hasAccess() ) {
            $actions[] = array(
                'name' => translate('Добавить'),
                'url' => $method->getJSCall(array())
            );
        }

        return $actions;
    }
} 
