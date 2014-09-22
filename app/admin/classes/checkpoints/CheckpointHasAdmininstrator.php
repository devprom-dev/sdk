<?php

class CheckpointHasAdmininstrator extends CheckpointEntryDynamic
{
    function execute()
    {
        global $model_factory;

        $user = $model_factory->getObject('cms_User');
        
        $count = $user->getByRefArrayCount( array( 'IsAdmin' => 'Y' ) );

        $this->setValue( $count > 0 ? '1' : '0' );
    }

    function getTitle()
    {
        return text(1159);
    }

    function getDescription()
    {
        return text(1160);
    }
}
