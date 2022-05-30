<?php
include_once SERVER_ROOT_PATH."cms/classes/ChangeLogNotificator.php";

class AdminChangeLogNotificator extends ChangeLogNotificator
{
    function getEntities()
    {
        return array (
            'cms_User',
            'cms_SystemSettings',
            'pm_ProjectRole',
            'pm_TaskType',
            'Priority',
            'pm_Severity',
            'co_ScheduledJob',
            'co_RemoteMailbox',
            'pm_Project',
			'cms_Backup',
			'cms_Update',
			'cms_BlackList',
            'cms_ExternalUser',
            'co_Company',
            'Invitation'
        );
    }
    
	function is_active( $object_it )
	{
	    $entities = $this->getEntities();
	    
	    if ( in_array($object_it->object->getClassName(), $entities) ) return true;
	    
		return parent::is_active( $object_it );
	}

	function isAttributeVisible( $attribute_name, $object_it, $action )
	{
        switch( $attribute_name )
        {
            case 'RecordCreated':
            case 'RecordModified':
                return false;
        }

        switch ( $object_it->object->getClassName() )
        {
            case 'cms_User':
                switch ( $attribute_name )
                {
                    case 'Password':
                    case 'Rating':
                    case 'OrderNum':
                        return false;
                }
                break;
            case 'co_ScheduledJob':
                switch ( $attribute_name )
                {
                    case 'RecentLog':
                    case 'ProcessedTotal':
                    case 'LeftToProcess':
                    case 'StatusText':
                        return false;
                }
                break;
        }
		return parent::isAttributeVisible( $attribute_name, $object_it, $action );
	}
}
