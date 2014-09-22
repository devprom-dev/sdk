<?php

include_once SERVER_ROOT_PATH."core/classes/user/User.php";

class ProjectUser extends User
{
    function resetFilters()
    {
        global $model_factory;

        parent::resetFilters();
        
        $participant = $model_factory->getObject('pm_Participant');
        
        $participant_it = $participant->getAll();

        $this->addFilter( new FilterInPredicate($participant_it->fieldToArray('SystemUser')) );
    }
}