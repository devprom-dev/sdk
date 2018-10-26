<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

class PortfolioIterator extends ProjectIterator
{
    public function __wakeup()
    {
        IteratorBase::__wakeup();
        $this->setObject( new Portfolio );
    }

    public function getSession() {
        if ( class_exists($this->get('sessionClassName')) ) {
            $className = $this->get('sessionClassName');
            return new $className($this);
        }
        throw new Exception('Unknown session class given for portfolio');
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
        	'IsKnowledgeUsed' => 'Y'
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
                if ( $key == 'RequestEstimationRequired' ) {
                    if ( $data[0][$key] == '' ) {
                        $data[0][$key] = $it->get($key);
                    }
                    else if ( $data[0][$key] != $it->get($key) ) {
                        $data[0][$key] = 'estimationnonestrategy';
                    }
                    continue;
                }
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
