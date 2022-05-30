<?php

abstract class WorkItemRegistryBuilder
{
    abstract function build( WorkItemRegistry $registry, array $filters );
}