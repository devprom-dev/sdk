<?php

class CheckpointUpdatesAvailable extends CheckpointEntryDynamic
{
    private $url = 'http://devprom.ru/download?json';
    
    function execute()
    {
        $data = $this->getAllUpdates();

        $new_only = count($data) > 0 ? $this->getNewUpdatesOnly($data) : array();

        $info_path = DOCUMENT_ROOT.'conf/new-updates.json';

        $file = fopen( $info_path, 'w+' );
        
        if ( $file === false )
        {
            $this->debug('Unable to write the file: '.$info_path);
        }
        else
        {
            fwrite( $file, JsonWrapper::encode($new_only) );
            
            fclose( $file );
        }
        
        $this->setValue( count($new_only) > 0 ? '0' : '1' );
    }
    
    function getAllUpdates()
    {
        $this->debug('Download updates json: '.$this->url);
        
        $curl = curl_init();
        
        $url = $this->url.'&version='.$_SERVER['APP_VERSION'].'&iid='.INSTALLATION_UID;
        
        curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_HTTPGET, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$result = curl_exec($curl);
		
		if ( $result === false )
		{
		    $this->debug( curl_error($curl) );
		}
		
		$data = JsonWrapper::decode($result);
		
		curl_close($curl);
		
		$this->debug('Updates found: '.count($data));
		
		return $data;
    }
    
    function getNewUpdatesOnly( $data )
    {
		$this->debug('Current version: '.$_SERVER['APP_VERSION']);
		
		$current = $this->transformUpdateVersion($_SERVER['APP_VERSION']);
		
		$new_only = array();
		
		foreach( $data as $update_info )
		{
		    if ( $this->transformUpdateVersion($update_info['version']) > $current )
		    {
		        $new_only[] = $update_info;
		    }
		}
		
		$this->debug('New updates found: '.count($new_only));
		
		return $new_only;
    }

    function transformUpdateVersion( $version )
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
}
