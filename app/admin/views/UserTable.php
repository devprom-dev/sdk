<?php

include ('UserList.php');

class UserTable extends PageTable
{
    function getSection()
    {
        return 'admin';
    }
    
	function getList()
	{
		return new UserList( $this->object );
	}

	function getFilters()
	{
		return array_merge(
		    parent::getFilters(),
		    array (
                new UserFilterRoleWebMethod(),
                new UserFilterStateWebMethod()
		    )
        );
	}

	function getFilterPredicates($values)
    {
        return array_merge(
            parent::getFilterPredicates($values),
            array(
                new UserAccessPredicate( $values['state'] ),
                new UserSystemRolePredicate( $values['role'] )
            )
        );
    }

    function IsNeedToDelete()
	{
	    return false;
	}

 	function getDefaultRowsOnPage()
	{
		return 60;
	}
	
	function getNewActions()
	{
		$actions = array();

		if( !$this->IsNeedToAdd() ) return $actions;

		$method = new ObjectCreateNewWebMethod($this->getObject());
		$uid = strtolower('new-'.get_class($this->getObject()));
		
		$actions[$uid] = array ( 
				'name' => translate('Добавить'),
				'uid' => $uid,
				'url' => $method->getJSCall(
								array( 
										'area' => $this->getPage()->getArea()
								)
						 ) 
		);

		$method = new ObjectCreateNewWebMethod(getFactory()->getObject('Invitation'));
		if ( $method->hasAccess() ) {
			$actions['invite-by-email'] = array (
			        'name' => text(1861),
					'url' => $method->getJSCall(array(), text(2001)),
					'uid' => 'invite-email'
		    );
		}
		
		return $actions;
	}

    function getExportActions()
    {
        $actions = array();

        $method = new ExcelExportWebMethod();
        $actions[] = array(
            'uid' => 'export-excel',
            'name' => $method->getCaption(),
            'url' => $method->url( $this->getCaption() )
        );

        return $actions;
    }

    function getCaption()
    {
        return translate('Список пользователей');
    }
}
