<?php
namespace Devprom\ProjectBundle\Service\Model\FilterResolver;

class TestScenarioFilterResolver
{
	public function __construct() {
	}
	
	public function resolve()
	{
	    return array(
            new \FilterHasNoAttributePredicate('PageType',
                getFactory()->getObject('TestScenario')->getTestPlanTypeIt()->getId()
            )
        );
	}
}