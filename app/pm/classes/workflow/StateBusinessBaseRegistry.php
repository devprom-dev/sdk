<?php

abstract class StateBusinessBaseRegistry extends ObjectRegistrySQL
{
 	protected $rules = array();
 	
 	abstract public function getBuilderInterfaceName();
 	
 	function registerRule( $rule )
 	{
 		foreach( $this->rules as $tmp )
 		{
 			if ( $tmp->getId() == $rule->getId() ) return;
 		}
 		
 		array_push( $this->rules, $rule );
 	}

 	function Query( $parms = array() )
 	{
 		$filters = array_merge($this->getFilters(), $this->extractPredicates($parms));
 		
 	    foreach( getSession()->getBuilders($this->getBuilderInterfaceName()) as $builder )
 	    {
 	    	$skip_builder = false;
 	    	
 	    	foreach ( $filters as $filter )
 	    	{
 	    		if ( is_a($filter, 'StateBusinessEntityFilter') )
 	    		{
 	    			$entity = $filter->getValue();

 	    			if ( !is_a($entity, $builder->getEntityRefName()) && $builder->getEntityRefName() != $entity->getEntityRefName() ) {
 	    				$skip_builder = true;
 	    			}
 	    		}
 	    	}

 	    	if ( $skip_builder ) continue;
 	    	
 	        $builder->build( $this );
 	    }
 	    
 	    $data = array();
 	    
 	    foreach( $this->rules as $rule )
 	    {
 	        $data[] = array (
 	                'pm_PredicateId' => $rule->getId(),
 	                'Caption' => $rule->getDisplayName(),
 	                'RuleObject' => $rule
 	        );
 	    }

 	    return $this->createIterator($data);
 	}
}