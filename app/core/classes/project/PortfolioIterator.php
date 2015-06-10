<?php

class PortfolioIterator extends ProjectIterator
{
    protected $methodology_it;
    
    protected $callback_array = array();
    
    public function setCallbacks( $callbacks )
    {
    	$this->callback_array = $callbacks; 
    }
    
    public function getSession()
    {
    	$callback = $this->callback_array[$this->get('CodeName')];
    	
		if ( !is_callable($callback) ) return;

        return call_user_func($callback, $this);
    }
    
    function getMethodologyIt()
 	{
 	    global $model_factory;

 	    if ( is_object($this->methodology_it) ) return $this->methodology_it;
 	    
 	    $methodology = $model_factory->getObject('pm_Methodology');
        
        $this->methodology_it = $methodology->createCachedIterator( array( array (
            'pm_Methodology' => 9999999,
            'Project' => $this->getId(),
            'IsReportsOnActivities' => 'N',
            'IsPlanningUsed' => 'N',
        	'IsSupportUsed' => 'Y',
        	'IsKnowledgeUsed' => 'Y'
        )));
        
        $attributes = $methodology->getAttributes();
        
        $data = $this->methodology_it->getRowset();
        
        $methodology_it = $methodology->getByRefArray( array( 
                'Project' => $this->getRef('RelatedProject')
        ));  
        
        while ( !$methodology_it->end() )
        {
            foreach ( $attributes as $key => $value )
            {
            	if ( $key == 'IsRequestOrderUsed' ) continue;
                if ( $methodology_it->get($key) != 'N' ) $data[0][$key] = $methodology_it->get($key);
            }
            
            $methodology_it->moveNext();
        }
        
        $this->methodology_it->setRowset( $data );
 	    
 		return $this->methodology_it;
 	}
 	
 	function getId()
 	{
 	    if ( $this->get('BaseId') == '' ) return parent::getId();
 	    
 	    return $this->get('BaseId') + getSession()->getUserIt()->getId();
 	}
 	
 	function setUser( $user_it )
 	{
        $data = $this->getData();
        
        $data['pm_ProjectId'] = $data['BaseId'] + $user_it->getId();
        $data['BaseId'] = '';
        
        $this->setData( $data );
 	}
}
