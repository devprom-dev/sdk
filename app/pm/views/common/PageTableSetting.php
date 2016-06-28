<?php

class PageTableSetting
{
    function __construct( $class_name )
    {
        $this->class_name = $class_name;
    }
    
    function getClassName()
    {
        return $this->class_name;
    }

    function setFilters( $filters_array )
    {
        $this->filters = $filters_array;
    }
    
    function getFilters()
    {
        return $this->filters;
    }
    
    function setRowsNumber( $rows )
    {
        $this->rows = $rows;
    }
    
    function getRowsNumber()
    {
        return $this->rows;
    }

    function setSorts( $sorts_array )
    {
        $index = '';
        foreach( $sorts_array as $sort ) {
            $this->sorts['sort'.$index] = $sort;
            $index = $index == '' ? 2 : $index + 1;
        }
    }
    
    function getSorts()
    {
        return $this->sorts;
    }
    
    protected $sorts;
    
    protected $filters;
    
    protected $rows;
}