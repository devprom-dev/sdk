<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE
include_once "BusinessRulePredicate.php";

class IssuePriorityRule extends BusinessRulePredicate
{
 	private $priorityName = '';
 	private $priorityId = '';

 	function __construct( $priorityIt = null )
 	{
		if ( $priorityIt instanceof OrderedIterator ) {
			$this->priorityId = $priorityIt->getId();
			$this->priorityName = $priorityIt->getDisplayName();
		}
		else {
			$this->priorityName = $this->getObject()->getDisplayName();
		}
 	}

	public function __sleep() {
		return array('priorityName','priorityId');
	}

 	function getId() {
 		return md5(strtolower(get_class($this)).$this->priorityId);
 	}
 	
 	function getObject() {
 		return getFactory()->getObject('Request');
 	}
 	
 	function getDisplayName() {
 		return str_replace('%1', $this->priorityName, text(2625));
 	}
 	
 	function check( $object_it, $transitionIt ) {
 		return $object_it->get('Priority') == $this->priorityId;
 	}
}
