<?php

include "UserUsageList.php";

class UserUsageTable extends ResourceTable
{
    function getList()
    {
        $method = new ResourceFilterScaleWebMethod();
        
        $method->setFilter( $this->getFiltersName() );

        $scale = $method->getValue();
        
        if ( $scale == '' )
        {
            $scale = 'month';
        }

        return new UserUsageList( $this->getObject(), $scale );
    }

    function getFilters()
    {
        global $model_factory;
        
        $participant = $model_factory->getObject('pm_Participant');
        
        $participant_it = $participant->getAll();
        
        $user = $model_factory->getObject('cms_User');
        
        $user->addFilter( new FilterInPredicate($participant_it->fieldToArray('SystemUser')));
        
        $filters = array(
                new FilterObjectMethod($user, '', 'user'),
        );

        return array_merge($filters, parent::getFilters());
    }
}
