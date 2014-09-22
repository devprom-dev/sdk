<?php

class Term
{
    protected $realizations = '';
    
    protected $caption = '';
    
    function __construct( $caption, $realizations )
    {
        $this->realizations = $realizations;
        
        $this->caption = $caption;
    }
    
    function getCaption()
    {
        return $this->caption;
    }
    
    function getRealizations()
    {
        return $this->realizations;
    }
    
    function getBase()
    {
        return array_keys($this->realizations);
    }
    
    function getTarget()
    {
        return array_values($this->realizations);
    }
}

class UpdateTerminology extends Installable
{
    var $terms = array();
    
	// checks all required prerequisites
	function check()
	{
		return true;
	}

	// skip install actions
	function skip()
	{
		return false;
	}

	// cleans after the installation script has been executed
	function cleanup()
	{
	}

	// makes install actions
	function install()
	{
	    global $model_factory;
	    
	    $terms = $this->buildTerms();
	    
	    foreach( $terms as $term )
	    {
	        $tobe_updated = $this->getUpdatedTerminology($term);

	        $vpds = array();
	        
	        $resource = $model_factory->getObject('cms_Resource');
	        
	        $resource->disableVpd();
	        
	        $resource_it = $resource->search( $term->getCaption(), array('ResourceValue') );
	        
	        while( !$resource_it->end() )
	        {
	            $vpds[$resource_it->get('VPD')] = 1;
	            
	            $resource_it->moveNext();
	        }
  
	        $vpds = array_keys($vpds);
	        
	        foreach( $vpds as $vpd )
	        {
	            $resource = $model_factory->getObject('cms_Resource');
	             
	            $resource->disableVpd();
	            
	            $resource_it = $resource->getByRef( 'VPD', $vpd );
	            
	            $updated_already = $resource_it->fieldToArray('ResourceKey');
            
	            foreach( $tobe_updated as $key => $value )
	            {
	                if ( in_array($key, $updated_already) ) continue;

	                $this->info('Add resource: '.JsonWrapper::encode(array (
	                        'ResourceKey' => $key,
	                        'VPD' => $vpd
	                )));
	                
	                $resource->add_parms( array (
	                        'ResourceKey' => $key,
	                        'ResourceValue' => $value,
	                        'VPD' => $vpd,
	                        'OrderNum' => 0
	                ));
	            }
	        }
	    }
	    
		return true;
	}
	
	// build dictionary
	function buildTerms()
	{
	    $terms = array();
	    
	    $terms[] = new Term( 'История пользователя', array (
	        'пожеланий' => 'историй',
	        'пожелания' => 'истории'
	    ));

	    $terms[] = new Term( 'Эпик', array (
	        'функция' => 'эпик',
	        'функций' => 'эпиков',
	    ));
	    
	    $terms[] = new Term( 'Все заявки', array (
	        'Продукт' => 'Заявки',
	        'пожеланий' => 'заявок',
	    ));
	    
	    return $terms;
	}
	
	function getUpdatedTerminology( $term )
	{
	    global $model_factory;
	    
	    $result = array();
	    
	    $terms = $term->getRealizations();
	    
	    $resource = $model_factory->getObject('cms_Resource');
	    
	    $resource_it = $resource->getAll();
	    
	    while( !$resource_it->end() )
	    {
	        foreach( $terms as $base => $target )
	        {
	            if ( strpos($resource_it->get('ResourceValue'), $base) !== false )
	            {
	                $result[$resource_it->getId()] = str_replace($base, $target, $resource_it->get('ResourceValue'));
	            }
	        }
	        
	        $resource_it->moveNext();
	    }
	    
	    return $result;
	}
}
