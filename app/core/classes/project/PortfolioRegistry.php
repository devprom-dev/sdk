<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

class PortfolioRegistry extends ObjectRegistrySQL
{
    protected $portfolios = array();

    function __sleep() {
        return array('portfolios', 'callbacks');
    }

    function addPortfolio( $attributes, $sessionClassName )
    {
        foreach ( $this->getObject()->getAttributes() as $key => $value ) {
            $base_attributes[$key] = '';
        };
        
        $attributes['IsTender'] = 'F'; // portfolio
        $attributes['VPD'] = ModelProjectOriginationService::getOrigin($attributes['pm_ProjectId']);
        $attributes['sessionClassName'] = $sessionClassName;
        
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

        return $this->createIterator( $this->portfolios );
    }
}