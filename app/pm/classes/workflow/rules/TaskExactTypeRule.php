<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

include_once "BusinessRulePredicate.php";

class TaskExactTypeRule extends BusinessRulePredicate
{
 	private $type_name;
 	private $type_id;
 	private $type_ref;
 	
 	function __construct( $type_it )
 	{
 		$this->type_id = $type_it->getId();
 		$this->type_ref = $type_it->get('ReferenceName');
 		$this->type_name = $type_it->getDisplayName();
 	}

	public function __sleep() {
		return array('type_name','type_ref','type_id');
	}

 	function getId() {
 		return abs(crc32(strtolower(get_class($this)).$this->type_ref));
 	}
 	
 	function getObject() {
 		return getFactory()->getObject('Task');
 	}
 	
 	function getDisplayName() {
 		return str_replace('%1', $this->type_name, text(1158));
 	}
 	
 	function check( $object_it, $transitionIt ) {
 		return $object_it->get('TaskType') == $this->type_id;
 	}
}
