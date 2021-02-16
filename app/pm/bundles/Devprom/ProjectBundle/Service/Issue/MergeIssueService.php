<?php
namespace Devprom\ProjectBundle\Service\Issue;

abstract class MergeIssueService
{
    abstract function run( $targetIssueIt, $duplicateIt );
}