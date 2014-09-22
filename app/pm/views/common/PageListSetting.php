<?php

class PageListSetting
{
    function __construct( $class_name )
    {
        $this->class_name = $class_name;
    }
    
    function getClassName()
    {
        return $this->class_name;
    }
    
    function setGroup( $group )
    {
        $this->group = $group;
    }
    
    function getGroup()
    {
        return $this->group;
    }
    
    function setSections( $sections_array )
    {
        $this->sections = join(',',$sections_array);
    }
    
    function getSections()
    {
        return $this->sections;
    }
    
    function setVisibleColumns( $columns_array )
    {
        $this->columns_array = $columns_array;
    }
    
    function getVisibleColumns()
    {
        return $this->columns_array;
    }

    protected $columns_array = array();
    
    protected $class_name = '';
    
    protected $group = '';
    
    protected $sections = array();
    
    protected $filters = array();
}