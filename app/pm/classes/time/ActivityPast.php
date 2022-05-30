<?php
include "ActivityPastRegistry.php";

class ActivityPast extends Metaobject
{
 	function __construct() {
 		parent::__construct('pm_Activity', new ActivityPastRegistry($this));
    }

    function getDisplayName() {
        return text(3134);
    }
}