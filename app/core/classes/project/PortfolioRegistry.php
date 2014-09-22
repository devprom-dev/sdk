<?php

class PortfolioRegistry extends ObjectRegistrySQL
{
    protected $portfolios = array();
    
    protected $callbacks = array();
    
    function addPortfolio( $attributes, $match_callback )
    {
        global $model_factory;

        foreach ( $this->getObject()->getAttributes() as $key => $value )
        {
            $base_attributes[$key] = '';
        };
        
        $attributes['IsTender'] = 'F'; // portfolio
        
        $attributes['VPD'] = ModelProjectOriginationService::getOrigin($attributes['pm_ProjectId']); 
        
        $this->callbacks[$attributes['CodeName']] = $match_callback;
                        
        $this->portfolios[] = array_merge($base_attributes, $attributes);
    }
    
    function createSQLIterator( $sql )
    {
        $this->portfolios = array();
        
        $builders = getSession()->getBuilders('PortfolioBuilder');

        foreach( $builders as $builder )
        {
            $builder->build($this);
        }

        usort($this->portfolios, function( $left, $right ) 
        {
            if ( $left['CodeName'] == $right['CodeName'] ) return 0;
             
            if ( $left['CodeName'] == 'my' && $right['CodeName'] != 'my' ) return 1;
            
            if ( $left['CodeName'] == 'all' && $right['CodeName'] != 'all' ) return 1;
            
            return -1;
        });

        $it = $this->createIterator( $this->portfolios );

        $it->setCallbacks( $this->callbacks );
        
        return $it;
    }
}