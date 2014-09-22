<?php

include_once "FunctionTraceIterator.php";

class FunctionInversedTraceIterator extends FunctionTraceIterator
{
    function getDisplayNameReference()
    {
        return 'Feature';
    }
}
