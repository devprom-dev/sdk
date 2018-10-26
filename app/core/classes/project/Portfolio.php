<?php

include_once SERVER_ROOT_PATH."core/classes/project/Project.php";
include_once "PortfolioIterator.php";
include "PortfolioRegistry.php";
include "predicates/PortfolioPredicate.php";
include_once "persisters/ProjectLinkedPersister.php";

class Portfolio extends MetaobjectCacheable
{
    public function __construct()
    {
        parent::__construct('pm_Project', new PortfolioRegistry($this));
        $this->addAttribute( 'LinkedProject', 'REF_pm_ProjectId', translate('Связанные проекты'), false );
        $this->addPersister( new ProjectLinkedPersister() );
        $this->addAttribute('RelatedProject', 'REF_ProjectId', '', false);
        $this->addAttribute('LinkedProject', 'REF_pm_ProjectId', '', false);
    }

    function createIterator()
    {
        return new PortfolioIterator( $this );
    }
}