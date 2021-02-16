<?php
include "FeatureHasIssuesRegistry.php";

class FeatureHasIssues extends Feature
{
    function __construct() {
        parent::__construct(new FeatureHasIssuesRegistry($this));
    }
}