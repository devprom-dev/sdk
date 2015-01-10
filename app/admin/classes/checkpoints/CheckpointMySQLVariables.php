<?php

class CheckpointMySQLVariables extends CheckpointEntryDynamic
{
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

    function buildSettings()
    {
    	$me = $this;
    	
    	return array (
    			array (
    					'items' => $this->buildNoLessSettings(),
    					'check' => function( $setting, $value ) use ($me) {
    									return $me->getSettingValue($setting) >= $value;
    			                   },
    					'display' => function( $setting, $value ) {
    									return $setting." = ".$value;
    							     }
    			)
    	);
    }
    
   	function buildNoLessSettings()
   	{
   		$items = array (
   				"lower_case_table_names" => 1,
   				"ft_min_word_len" => 3,
   				"group_concat_max_len" => 4294967295,
   				"open_files_limit" => $this->checkWindows() ? 2048 : 8192
   		);
   		
   		return $items;
   	}
    
   	function getSettingValue( $name )
   	{
        return getFactory()->getObject('cms_SystemSettings')->createSQLIterator( "show variables like '%".$name."%'" )->get('Value');
   	}
   	
    function getTitle()
    {
        return text(1430);
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

    function checkWindows()
    {
        global $_SERVER;

        return strpos($_SERVER['OS'], 'Windows') !== false
            || $_SERVER['WINDIR'] != ''  || $_SERVER['windir'] != '';
    }
}
