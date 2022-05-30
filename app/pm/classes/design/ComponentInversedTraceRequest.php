<?php
include_once "ComponentInversedTraceIterator.php";

class ComponentInversedTraceRequest extends ComponentTraceRequest
{
 	function createIterator() {
 		return new ComponentInversedTraceIterator( $this );
 	}
}
