<?php

include_once SERVER_ROOT_PATH."pm/classes/settings/EstimationStrategyBuilder.php";

include "EstimationStoryPointsStrategy.php";
include "EstimationFibonacciStrategy.php";
include "EstimationPowerOfTwoStrategy.php";

class EstimationStrategyScrumBuilder extends EstimationStrategyBuilder
{
    public function getStrategies()
    {
        return array (
                new EstimationFibonacciStrategy(),
                new EstimationStoryPointsStrategy(),
                new EstimationPowerOfTwoStrategy()
        ); 
    }
}