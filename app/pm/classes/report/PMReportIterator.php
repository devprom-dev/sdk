<?php

class PMReportIterator extends CacheableIterator
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
			    
		        if ( $value == '' && $this->get_native('Module') != '' ) {
        	        return getFactory()->getObject('Module')->getExact( $this->get_native('Module') )->getDisplayName();
        	    }
        	    return preg_replace_callback('/text\(([a-zA-Z\d]+)\)/i', iterator_text_callback, $value);
			
            case 'QueryString':
                $value = parent::get('QueryString');
                return SystemDateTime::parseRelativeDateTime($value, getLanguage());
			    
			default:
				return parent::get($attr);
		}
	}
	
 	function buildMenuItem( $query_string = '' )
	{
	    if ( !getFactory()->getAccessPolicy()->can_read($this) ) return array();

	    $module_uid = $this->get('Module');
	    
	    if ( $module_uid == '' ) {
	        $report_it = $this->object->getExact( $this->get('Report') );
	        if ( !getFactory()->getAccessPolicy()->can_read($report_it) ) return array();
	        
	        $module_uid = $report_it->get('Module');
	        $base_parm = "&basereport=".$report_it->getId();
	    }
	    else {
	        $base_parm = "&basemodule=".$module_uid;
	    }
	    
	    $url = $this->get_native('Url');
	    
	    if ( $module_uid != '' )
	    {
			$module = getFactory()->getObject('Module');
			$module->setVpdContext($this);
	        $module_it = $module->getExact( $module_uid );
	        if ( !getFactory()->getAccessPolicy()->can_read($module_it) ) return array();

	        $url = $url == '' ? $module_it->get('Url') : $url;
			if ( $this->getId() != $module_it->getId() ) {
				$url .= '/'.$this->getId();
			}
	    }
	    else
	    {
	        $parts = parse_url( $url );
 	        if ( $parts['scheme'] == '' ) {
 	            $url = getSession()->getApplicationUrl($this).$url;
 	        }
			$url .= '/'.$this->getId();
	    }

	    if ( $url == '' ) return array();
	    if ( $query_string != '' ) $query_string .= '&clear';

	    return array(
            'name' => $this->getDisplayName(),
	        'title' => $this->getDisplayName(),
            'url' => $url.'?report='.$this->getId().$base_parm.'&'.$query_string,
            'uid' => $this->getId(),
			'icon' => $this->get('Icon') != '' ? $this->get('Icon') : (is_object($module_it) ? $module_it->get('Icon') : ''),
	    	'report' => $this->getId()
	    );
	}
	
	function getUrl( $query_string = '', $projectIt = null )
	{
	    $info = $this->buildMenuItem(trim($query_string,'?&'));
	    return is_object($projectIt)
            ? preg_replace('/\/pm\/[^\/]+\//i', '/pm/'.$projectIt->get('CodeName').'/', $info['url'])
            : $info['url'];
	}
	
	function getViewUrl()
	{
		return $this->getUrl();
	}
}