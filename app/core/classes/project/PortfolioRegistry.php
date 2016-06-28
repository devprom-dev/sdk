<?php

class PortfolioRegistry extends ObjectRegistrySQL
{
    protected $portfolios = array();
    
    protected $callbacks = array();
    
    function addPortfolio( $attributes, $match_callback )
    {
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
        
        foreach( getSession()->getBuilders('PortfolioBuilder') as $builder ) {
            $builder->build($this);
        }

        $lastItems = array();
        foreach( $this->portfolios as $key => $portfolio ) {
            if ( in_array($portfolio['CodeName'], array('my','all')) ) {
                $lastItems[] = $portfolio;
                unset($this->portfolios[$key]);
            }
        }
        $this->portfolios = array_merge($this->portfolios, $lastItems);

        $it = $this->createIterator( $this->portfolios );
        $it->setCallbacks( $this->callbacks );
        return $it;
    }
}