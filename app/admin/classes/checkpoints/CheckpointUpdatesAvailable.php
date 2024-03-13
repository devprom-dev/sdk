<?php

class CheckpointUpdatesAvailable extends CheckpointEntryDynamic
{
    function getUrl()
    {
        return '/admin/updates.php';
    }
    
    /**
     * Checks for updates
     *
     * set $this->value to
     *  0 if updates are found
     *  1 if updates aren't found
     *
     * @return void
     */
    function execute()
    {
        if ( \EnvironmentSettings::getAutoUpdate() ) {
            $this->setValue(1);
            return;
        }

        $data = JsonWrapper::decode(
            @file_get_contents(DOCUMENT_ROOT.CheckpointSupportPayed::UPDATES_FILE)
        );

        $new_only = count($data) > 0
            ? $this->getNewUpdatesOnly($data)
            : array();

        $this->debug('Current version is '.$_SERVER['APP_VERSION']);
        $this->debug('New updates found '.count($new_only));

        $license_it = getFactory()->getObject('LicenseInstalled')->getAll();
        if ( method_exists($license_it, 'getSupportIncluded') && !$license_it->getSupportIncluded() ) {
            if ( CheckpointSupportPayed::getPayedDays() <= 0 ) {
                $this->setValue(1);
                return;
            }
        }

        $this->setValue( count($new_only) > 0 ? '0' : '1' );
    }
    
    static function getNewUpdatesOnly( $data )
    {
		$current = self::transformUpdateVersion($_SERVER['APP_VERSION']);
		
		$new_only = array();
		foreach( $data as $update_info ) {
		    if ( self::transformUpdateVersion($update_info['version']) > $current ) {
		        $new_only[] = $update_info;
		    }
		}
		return $new_only;
    }

    static function transformUpdateVersion( $version )
    {
        $parts = preg_split('/\./', $version);
        
        if ( count($parts) < 3 )
        {
            $parts[] = 0; $parts[] = 0;
        }
        
        if ( count($parts) < 4 )
        {
            $parts[] = 0;
        }
        
        $value = 0;
        
        $offset = 0;
        
        foreach( array_reverse($parts) as $part )
        { 
            $value += $part * pow(10000, $offset);
            
            $offset++;
        }
        
        return $value;
    }
    
    function getTitle()
    {
        return text(1381);
    }

    function getDescription()
    {
        return text(1382);
    }

    function getWarning()
    {
        return text(2253);
    }
}
