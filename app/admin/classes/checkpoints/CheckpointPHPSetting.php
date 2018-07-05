<?php

class CheckpointPHPSetting extends CheckpointEntryDynamic
{
    function getTitle()
    {
        return text(1127);
    }

	function execute()
    {
    	$check_result = "1";
    	
        foreach( $this->buildSettings() as $data )
    	{
    			$callback = $data['check'];
    			
		    	array_walk( $data['items'], 
		    			function( $value, $setting ) use (&$check_result, $callback) {
		    					if ( !$callback($setting, $value) ) $check_result = "0";
		    			}
				);
    	}

    	$this->setValue($check_result);
    }

    function getDescription()
    {
    	$text = '';
    	
    	foreach( $this->buildSettings() as $data )
    	{
    			$check_callback = $data['check'];
    			$show_callback = $data['display'];
    			
		    	array_walk( $data['items'], 
		    			function( $value, $setting ) use (&$text, $check_callback, $show_callback) {
		    					$line = $show_callback($setting, $value);
		    					if ( !$check_callback($setting, $value) ) $line = "<b>".$line."</b>";
		    					$text .= $line."<br/>";
		    			}
				);
    	}
    	
        return $text;
    }
    
    function buildSettings()
    {
    	$me = $this;
    	
    	return array (
    			array (
    					'items' => $this->buildBooleanSettings(),
    					'check' => function( $setting, $value ) {
    									return ini_get($setting) == $value;
    							   },
    					'display' => function( $setting, $value ) {
    									return $setting." = ".($value ? "on" : "off");
    							     }
    			),
    			array (
    					'items' => array ( 'error_reporting' ),
    					'check' => function( $setting, $value ) {
    									return !(error_reporting() & E_NOTICE ) && !(error_reporting() & E_DEPRECATED );
    							   },
        				'display' => function( $setting, $value ) {
    									return "error_reporting = E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED";
    							     }
    			),
    			array (
    					'items' => array ( 'disable_functions' ),
    					'check' => function( $setting, $value ) {
    									return strpos(ini_get( 'disable_functions' ), 'shell_exec') === false
    										   && strpos(ini_get( 'disable_functions' ), 'disk_free_space') === false;
    							   },
        				'display' => function( $setting, $value ) {
    									return "disable_functions = ";
    							     }
    			),
            array (
                'items' => array ( 'open_basedir' ),
                'check' => function( $setting, $value ) {
                    return ini_get( 'open_basedir' ) == '';
                },
                'display' => function( $setting, $value ) {
                    return "open_basedir = ";
                }
            ),
    			array (
    					'items' => $this->buildNoLessSettings(),
    					'check' => function( $setting, $value ) use ($me) {
    									return $me->return_bytes(ini_get($setting)) >= $value;
    			                   },
    					'display' => function( $setting, $value ) {
    									return $setting." = ".$value;
    							     }
    			),
    		    array (
    					'items' => $this->buildExtentionSettings(),
    					'check' => function( $value, $setting ) {
    									return extension_loaded($setting);
    			                   },
    					'display' => function( $value, $setting ) {
    									return "extension = ".$setting;
    							     }
    		    )
    	);
    }
    
    function buildBooleanSettings()
    {
    	$items = array (
    			"file_uploads" => true,
    			"allow_url_fopen" => true,
    			"short_open_tag" => true,
    			"log_errors" => true
    	);
    	
    	if ( version_compare(phpversion(), '5.3.0', '<') )
		{
			$items[] = "register_long_arrays";
		}
    	
    	return $items;
    }
    
   	function buildNoLessSettings()
   	{
   		$items = array (
   				"upload_max_filesize" => 6 * 1024 * 1024,
   				"post_max_size" => 6 * 1024 * 1024,
   				"max_execution_time" => 600,
   				"max_input_time" => 600,
   				"memory_limit" => 128 * 1024 * 1024
   		);
   		
   		if ( ini_get("suhosin.post.max_vars") !== false )
   		{
	   		$items["suhosin.post.max_vars"] = 2000;
	   		$items["suhosin.request.max_vars"] = 2000;
   		}
   		
   		return $items;
   	}

   	function buildExtentionSettings()
   	{
   		return array (
   				"mbstring",
   				"gd",
   				"zip",
   				"zlib",
   				"curl",
   				"openssl",
   				"dom",
   				"fileinfo",
   				"pdo_mysql",
   				"imap"
   		);
   	}
   	
    function return_bytes($val)
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);

        switch($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

    function getWarning()
    {
        return text(2255);
    }
}