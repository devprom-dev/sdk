<?php

class PMReportIterator extends OrderedIterator
{
	function get( $attr )
	{
		global $model_factory;

		switch ( $attr )
		{
			case 'Url':
			    
			    $url = parent::get($attr);
			    
			    if ( $url == '' )
			    {
    			    $module_uid = $this->get('Module');
    	    
            	    if ( $module_uid == '' )
            	    {
            	        $report_it = $this->object->getExact( $this->get('Report') );
            	        
            	        $module_uid = $report_it->get('Module'); 
            	    }
            	    
            	    if ( $module_uid != '' )
            	    {
            	        $module = $model_factory->getObject('Module');
            
            	        $module_it = $module->getExact( $module_uid );
            	        
            	        $url = $url == '' ? $module_it->get('Url') : $url;
            	    }
			    }
	    
        	    return $url.'?report='.$this->getId();
		    
			case 'Caption':

			    $value = parent::get($attr);
			    
		        if ( $value == '' && $this->get_native('Module') != '' )
        	    {
        	        $module = $model_factory->getObject('Module');
        	        
        	        $module_it = $module->getExact( $this->get_native('Module') );
        	        
        	        return $module_it->getDisplayName();
        	    }
        	    
        	    return $value;
			
            case 'QueryString':
			    
         	    $language = getLanguage();
         	    
                $last_month = $language->getPhpDate( strtotime('-1 month', strtotime(date('Y-m-j'))) );
        
                $last_week = $language->getPhpDate( strtotime('-1 week', strtotime(date('Y-m-j'))) );
         	    
                $value = parent::get('QueryString');
                
				$value = str_replace('user-id', getSession()->getUserIt()->getId(), $value);
	            
				$value = preg_replace('/last-month/', $last_month, $value);

				$value = preg_replace('/last-week/', $last_week, $value);
				
                return $value;
			    
			default:
			    
				return parent::get($attr);
		}
	}
	
 	function buildMenuItem( $query_string = '' )
	{
	    global $model_factory;
	    
	    if ( !getFactory()->getAccessPolicy()->can_read($this) ) return array();

	    $module_uid = $this->get('Module');
	    
	    if ( $module_uid == '' )
	    {
	        $report_it = $this->object->getExact( $this->get('Report') );
	        
	        if ( !getFactory()->getAccessPolicy()->can_read($report_it) ) return array();
	        
	        $module_uid = $report_it->get('Module');

	        $base_parm = "&basereport=".$report_it->getId();
	    }
	    else
	    {
	        $base_parm = "&basemodule=".$module_uid;
	    }
	    
	    $url = $this->get_native('Url');
	    
	    if ( $module_uid != '' )
	    {
	        $module = $model_factory->getObject('Module');

	        $module_it = $module->getExact( $module_uid );
	        
	        if ( !getFactory()->getAccessPolicy()->can_read($module_it) ) return array();

	        $url = $url == '' ? $module_it->get('Url') : $url;
	    }
	    else
	    {
	        $parts = parse_url( $url );
 	    
 	        if ( $parts['scheme'] == '' )
 	        {
 	            $url = getSession()->getApplicationUrl().$url;
 	        }
	    }

	    if ( $url == '' ) return array();
	    
	    $language = getLanguage();
	    
	    return array(
            'name' => $this->getDisplayName(),
	        'title' => $this->getDisplayName(),
            'url' => $url.'/'.$this->getId().'?report='.$this->getId().$base_parm.'&'.$query_string,
            'uid' => $this->getId()
	    );
	}
	
	function getUrl()
	{
	    $info = $this->buildMenuItem();
	    
	    return $info['url'];
	}
	
	function getViewUrl()
	{
		return $this->getUrl();
	}
}