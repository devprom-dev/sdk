<?php

include "AccessRightTable.php";
include "UserAccessRightTable.php";
include "AccessObjectTable.php";
include "AccessRightPageSettingsBuilder.php";

class AccessRightPage extends PMPage
{
    function AccessRightPage()
    {
        parent::PMPage();
        
        getSession()->addBuilder( new AccessRightPageSettingsBuilder() );
    }

    function getObject()
    {
        global $model_factory;
        return $model_factory->getObject('CommonAccessRight');
    }

    function getTable() 
    {
        if ( $_REQUEST['user'] > 0 )
        {
            return new UserAccessRightTable($this->getObject());
        }
        
        if ( $_REQUEST['class'] != '' )
        {
            return new AccessObjectTable();
        }
        
        return new AccessRightTable( $this->getObject() );
    }

    function getForm() 
    {
        return null;
    }
    
    function getTitle()
    {
        return translate('Права доступа');
    }

 	function hasAccess()
 	{
        if ( $_REQUEST['user'] > 0 )
        {
 			return getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('pm_AccessRight'));
        }
        
        return parent::hasAccess();
 	}
}