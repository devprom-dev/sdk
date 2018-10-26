<?php
include "IntegrationList.php";

class IntegrationTable extends SettingsTableBase
{
    function getList() {
        return new IntegrationList( $this->getObject() );
    }

    function getNewActions()
    {
        $actions = array();

        $method = new ObjectCreateNewWebMethod($this->getObject());
        if ( !$method->hasAccess() ) return $actions;
        $method->setRedirectUrl('donothing');

        $app_it = getFactory()->getObject('IntegrationApplication')->getAll();
        while( !$app_it->end() )
        {
            $uid = strtolower('new-'.$app_it->getId());
            $actions[$uid] = array (
                'name' => $app_it->getDisplayName(),
                'uid' => $uid,
                'url' => $method->getJSCall(
                    array(
                        'Caption' => $app_it->getId(),
                        'area' => $this->getPage()->getArea()
                    ),
                    $this->getObject()->getDisplayName().': '.$app_it->getDisplayName()
                )
            );
            $app_it->moveNext();
        }
        return $actions;
    }
}
