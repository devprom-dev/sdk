<?php

include_once SERVER_ROOT_PATH."pm/classes/settings/EstimationStrategyBuilder.php";

include "EstimationNoneStrategy.php";
include "EstimationHoursStrategy.php";
include "EstimationTShirtStrategy.php";

class EstimationStrategyCommonBuilder extends EstimationStrategyBuilder
{
    public function getStrategies()
    {
        return array (
            new EstimationNoneStrategy(),
            new EstimationHoursStrategy(),
            new EstimationTShirtStrategy()
        ); 
    }
}