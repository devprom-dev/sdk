<?php

include_once SERVER_ROOT_PATH."core/classes/project/Project.php";
include_once "PortfolioIterator.php";
include "PortfolioRegistry.php";
include "predicates/PortfolioPredicate.php";

class Portfolio extends Project
{
    public function __construct()
    {
        parent::__construct(new PortfolioRegistry($this));
        
        $this->addAttribute('RelatedProject', 'REF_ProjectId', '', false);
        $this->addAttribute('LinkedProject', 'REF_pm_ProjectId', '', false);
    }

    function createIterator()
    {
        return new PortfolioIterator( $this );
    }
}