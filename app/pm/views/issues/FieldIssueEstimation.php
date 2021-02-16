<?php
include_once SERVER_ROOT_PATH . "pm/views/ui/FieldEstimation.php";

class FieldIssueEstimation extends FieldEstimation
{
	function __construct( $object_it = null ) {
		parent::__construct($object_it, 'Estimation',
            getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy());
	}
}
