<?php

class CheckpointBase
{
    private $entries = array();
    
    private $settings;
    
    private $logger;

    function __construct()
    {
    }

    function getUid()
    {
        return strtolower(get_class($this));
    }

    function getSettingsFileName()
    {
        return DOCUMENT_ROOT.'conf/'.$this->getUid().'.ini';
    }

    function registerEntry( $entry )
    {
        array_push( $this->entries, $entry );
    }

    function registerEntries( $entries )
    {
        $this->entries = array_merge( $this->entries, $entries );
    }

    function getEntries()
    {
    	$this->entries = array();
    	
    	foreach( getSession()->getBuilders('CheckpointRegistryBuilder') as $builder )
    	{
    		$builder->build($this);
    	}
    	
        $this->loadEntries( $this->entries );
    	
        return $this->entries;
    }

    function setEntries( $entries )
    {
        $this->entries = $entries;
        
        $this->storeEntries();
    }

    function storeEntries()
    {
        foreach( $this->entries as $entry )
        {
            $parms = array (
                    'enabled' => $entry->enabled() ? '1' : '0'
            );

            if ( is_a($entry, 'CheckpointEntryDynamic') )
            {
                $parms['value'] = $entry->getValue();
            }

            $this->settings[$entry->getUid()] = $parms;
        }

        $this->write_ini_file( $this->getSettingsFileName(), $this->settings );
    }

    protected function loadEntries( array & $entries )
    {
        $file_name = $this->getSettingsFileName();
        if ( !file_exists($file_name) ) return;

        $this->settings = parse_ini_file( $file_name, true );

        foreach ( $entries as $entry )
        {
            $uid = $entry->getUid();

            if ( !array_key_exists($uid, $this->settings) ) continue;
            if ( $this->settings[$uid]['enabled'] < 1 ) $entry->disable();

            if ( is_a($entry, 'CheckpointEntryDynamic') )
            {
                $entry->setValue( $this->settings[$uid]['value'] );
            }
        }
    }

    function check()
    {
        foreach( $this->getEntries() as $entry )
        {
            if ( $entry->enabled() && !$entry->check() )
            {
            	$this->error('Checkpoint failed: '.$entry->getTitle());
            		
            	return false;
            }
        }

        return true;
    }

    function checkDetails()
    {
    	$details = array();
    	
        foreach( $this->getEntries() as $entry )
        {
            if ( $entry->enabled() && $entry->notificationRequired() && !$entry->check() )
            {
            	$details[] = $entry->getTitle();
            }
        }

        return $details;
    }
    
    function checkRequired( & $failed_entries )
    {
        $failed_entries = array();
        
        foreach( $this->getEntries() as $entry )
        {
            if ( $entry->enabled() && $entry->getRequired() && !$entry->check() ) $failed_entries[] = $entry;
        }

        return count($failed_entries) < 1;
    }
    
    function executeDynamicOnly( $filter_items = array() )
    {
    	$entries = $this->getEntries();
    	
        foreach ( $entries as $entry )
        {
            if ( !is_a( $entry, 'CheckpointEntryDynamic' ) ) continue;
            
            if ( count($filter_items) > 0 )
            {
                foreach( $filter_items as $item ) if ( is_a( $entry, $item ) ) $entry->execute();
            }
            else
            {
                $entry->execute();
            }
        }

        $this->setEntries( $entries );
    }

    function checkOnly( $filter_items = array() )
    {
        if ( count($filter_items) < 1 ) return;
        
        $entries = $this->getEntries();
        
        foreach ( $entries as $entry )
        {
            foreach( $filter_items as $item )
            {
                if ( is_a( $entry, $item ) )
                {
                    $entry->execute();
                }
            }
        }

        $this->setEntries( $entries );
    }
    
    function getLogger()
	{
 		try 
 		{
 			if ( !is_object($this->logger) )
 			{
 				$this->logger = Logger::getLogger('System');
 			}
 			
 			return $this->logger;
 		}
 		catch( Exception $e)
 		{
 			error_log('Unable initialize logger: '.$e->getMessage());
 		}
	}
	
	function error( $message )
	{
		$log = $this->getLogger();
		
		if ( !is_object($log) ) return;
		
		$log->error( $message );
	}
	
	function debug( $message )
	{
		$log = $this->getLogger();
		
		if ( !is_object($log) ) return;
		
		$log->debug( $message );
	}
	
	function info( $message )
	{
		$log = $this->getLogger();
		
		if ( !is_object($log) ) return;
		
		$log->info( $message );
	}
	    
    function write_ini_file($path, $assoc_arr)
    {
        $content = "";

        foreach ($assoc_arr as $key=>$elem) {
            if (is_array($elem)) {
                if ($key != '') {
                    $content .= "[".$key."]\r\n";
                }

                foreach ($elem as $key2=>$elem2) {
                    if ($this->beginsWith($key2,'Comment_') == 1 && $this->beginsWith($elem2,';')) {
                        $content .= $elem2."\r\n";
                    }
                    else if ($this->beginsWith($key2,'Newline_') == 1 && ($elem2 == '')) {
                        $content .= $elem2."\r\n";
                    }
                    else {
                        $content .= $key2." = ".$elem2."\r\n";
                    }
                }
            }
            else {
                $content .= $key." = ".$elem."\r\n";
            }
        }

        if (!$handle = fopen($path, 'w')) {
            return -2;
        }
        if (!fwrite($handle, $content)) {
            return -2;
        }
        fclose($handle);
        return 1;
    }

    function beginsWith( $str, $sub )
    {
        return ( substr( $str, 0, strlen( $sub ) ) === $sub );
    }
}