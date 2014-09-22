<?php

class EstimationStrategyDictionary extends FieldDictionary
{
    function __construct()
    {
        parent::__construct( getSession()->getProjectIt()->object );
        
        $this->setNullOption(false);
    }

    function getOptions()
    {
        $options = array();
        
        foreach( getSession()->getBuilders('EstimationStrategyBuilder') as $builder )
        {
            foreach( $builder->getStrategies() as $strategy )
            {
        	    $options[] = array (
                    'value' => strtolower(get_class($strategy)),
                    'caption' => $strategy->getDisplayName(),
                    'disabled' => false
                );
            }
        }
        
        return $options;
    }
}