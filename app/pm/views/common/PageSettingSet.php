<?php

include "PageListSetting.php";
include "PageTableSetting.php";
include "ReportSetting.php";

class PageSettingSet extends Metaobject
{
    protected $settings;
    
    function __construct()
    {
        parent::__construct('entity');
        
        $this->settings = array();
    }

    function add( $setting )
    {
        if ( is_a($setting, 'ReportSetting') )
        {
            if ( $setting->getReport() == '' )
            {
                throw new Exception('Unable to add page settings on unknown report');
            }
            
            $this->settings[$setting->getReport()] = $setting;
        }
        else if ( is_a($setting, 'PageListSetting') )
        {
            if ( $setting->getClassName() == '' )
            {
                throw new Exception('Unable to add page settings on unknown page list');
            }
            
            $this->settings[$setting->getClassName()] = $setting;
        }
        else if ( is_a($setting, 'PageTableSetting') )
        {
            if ( $setting->getClassName() == '' )
            {
                throw new Exception('Unable to add page settings on unknown page table');
            }
            
            $this->settings[$setting->getClassName()] = $setting;
        }
    }
    
    function getByReport( $report_uid )
    {
        return $this->settings[$report_uid];
    }
    
    function getByPageList( $list )
    {
        $class_name = get_class($list);
    	
    	while ( $class_name !== false )
    	{
    		if ( is_object($this->settings[$class_name]) )
    		{
    			return $this->settings[$class_name];
    		}
    		
    		$class_name = get_parent_class($class_name);  
    	}
    	
    	return null;
    }
    
    function getByPageTable( $table )
    {
    	$class_name = get_class($table);
    	
    	while ( $class_name !== false )
    	{
    		if ( is_object($this->settings[$class_name]) )
    		{
    			return $this->settings[$class_name];
    		}
    		
    		$class_name = get_parent_class($class_name);  
    	}
    	
        return null;
    }
    
    function getAll()
 	{
        $data = array();
 		
 		foreach( $this->settings as $key => $setting )
 		{
 			$data[] = array (
 				'entityId' => $key,
 				'setting' => $setting
 			);
 		}
 		
 		return $this->createCachedIterator( $data );
 	}
}
