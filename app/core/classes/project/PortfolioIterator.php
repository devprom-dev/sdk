<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

class PortfolioIterator extends ProjectIterator
{
    protected $callback_array = array();

    public function __wakeup()
    {
        IteratorBase::__wakeup();
        $this->setObject( new Portfolio );
    }

    public function setCallbacks( $callbacks ) {
    	$this->callback_array = $callbacks; 
    }
    
    public function getSession()
    {
    	$callback = $this->callback_array[$this->get('CodeName')];
    	
		if ( !is_callable($callback) ) return;

        return call_user_func($callback, $this);
    }
    
    function buildMethodologyIt()
 	{
 	    $methodology = getFactory()->getObject('Methodology');
        $methodology_it = $methodology->createCachedIterator( array( array (
            'pm_MethodologyId' => 9999999,
            'Project' => $this->getId(),
            'IsReportsOnActivities' => 'N',
            'IsPlanningUsed' => 'N',
        	'IsSupportUsed' => 'N',
        	'IsKnowledgeUsed' => 'Y',
            'RequestEstimationRequired' => 'estimationnonestrategy'
        )));
        
        $attributes = $methodology->getAttributes();
        $data = $methodology_it->getRowset();
        
        $it = $methodology->getRegistry()->Query(
            array(
                new FilterAttributePredicate('Project', $this->getRef('RelatedProject')->idsToArray())
            )
        );
        $attributeKeys = array_merge(array_keys($attributes), array_keys($data[0]));
        while ( !$it->end() )
        {
            foreach ( $attributeKeys as $key ) {
                if ( $key == 'RequestEstimationRequired' ) continue;
                if ( $it->get($key) == 'Y' ) $data[0][$key] = $it->get($key);
            }
            $it->moveNext();
        }

        $methodology_it->setRowset( $data );
 		return $methodology_it;
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
