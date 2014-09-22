<?php

abstract class WorkflowBuilder
{
	abstract public function build( WorkflowRegistry & $registry );
}