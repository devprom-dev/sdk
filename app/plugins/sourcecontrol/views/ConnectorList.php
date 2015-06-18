<?php

class ConnectorList extends PMPageList
{
    function IsNeedToDisplay( $attr )
    {
        switch($attr)
        {
            case 'ConnectorClass':
            case 'Project':
            case 'LoginName':
            case 'SVNPassword':
                return false;

            default:
                return parent::IsNeedToDisplay( $attr );
        }
    }

    function getGroupFields()
    {
        return array();
    }
    
    function getItemActions( $column, $object_it )
    {
        global $model_factory;
        
        $actions = parent::getItemActions( $column, $object_it );
        
        $session = getSession();
        
        if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
        
        $menu = $model_factory->getObject('Module')->getExact('sourcecontrol/connection')
            ->buildMenuItem('?connection='.$object_it->getId());
        
        $actions[] = array (
            'name' => translate('Отладка'),
            'url' => $menu['url']  
        );
        
        return $actions;
    }
    
    function getNoItemsMessage()
    {
        global $model_factory;
        
        return str_replace('%1', $model_factory->getObject('pm_Subversion')->getPageName(), text('sourcecontrol29'));
    }
}
