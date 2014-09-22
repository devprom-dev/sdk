<?php

include_once "TaskTraceBaseIterator.php";

class TaskInversedTraceBaseIterator extends TaskTraceBaseIterator
{
     function getDisplayNameReference()
     {
         return 'Task';
     }
}
