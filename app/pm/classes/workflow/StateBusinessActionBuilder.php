<?php

abstract class StateBusinessActionBuilder
{
    abstract function getEntityRefName();

    abstract function build( StateBusinessActionRegistry & $set );
}