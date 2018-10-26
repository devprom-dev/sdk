<?php

class PMCustomReportIterator extends OrderedIterator
{
    function get( $attr )
    {
        switch ( $attr )
        {
            case 'Caption':
                
                $value = parent::get($attr);
                
                if ( $value == '' )
                {
                	$report_id = parent::get('ReportBase');
                	
                	if ( $report_id != '' )
                	{
	    				return getFactory()->getObject('PMReport')->getExact($report_id)->get($attr);
                	}
                	
                	$module_id = parent::get('Module');
                	
                    if ( $module_id != '' )
                	{
	    				return getFactory()->getObject('Module')->getExact($module_id)->get($attr);
                	}
                }
                
                return $value;
                
            default:
                return parent::get($attr);
        }
    }
    
    function getEditUrl()
    {
        if ( $this->get('Category') == '' ) return parent::getEditUrl();
        
        return str_replace(
                $this->object->getPage(), 
                trim($this->object->getPage(), '?').'/'.$this->get('Category').'?', 
                parent::getEditUrl()
        );
    }
}