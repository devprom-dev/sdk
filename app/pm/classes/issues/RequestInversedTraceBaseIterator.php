<?php

include_once "RequestTraceBaseIterator.php";

class RequestInversedTraceBaseIterator extends RequestTraceBaseIterator
{
     function getDisplayNameReference()
     {
         return 'ChangeRequest';
     }
}
