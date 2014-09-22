<?php

abstract class StateBusinessRuleBuilder
{
    abstract function getEntityRefName();
    
    abstract function build( StateBusinessRuleRegistry & $set );
}