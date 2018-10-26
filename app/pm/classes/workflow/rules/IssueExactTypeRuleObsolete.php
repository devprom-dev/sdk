<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

include_once "BusinessRulePredicate.php";

class IssueExactTypeRuleObsolete extends BusinessRulePredicate
{
 	private $type_name = '';
 	private $type_id = '';
 	private $type_ref = '';
 	
 	function __construct( $type_it = null )
 	{
		if ( $type_it instanceof OrderedIterator ) {
			$this->type_id = $type_it->getId();
			$this->type_ref = $type_it->get('ReferenceName');
			$this->type_name = $type_it->getDisplayName();
		}
		else {
			$this->type_name = $this->getObject()->getDisplayName();
		}
 	}

	public function __sleep() {
		return array('type_name','type_ref','type_id');
	}

 	function getId() {
        switch( $this->type_ref ) {
            case '':
                return 550535644;
            case 'bug':
                return 279263446;
            case 'enhancement':
                return 220019480;
        }
 	}
 	
 	function getObject() {
 		return getFactory()->getObject('Request');
 	}
 	
 	function getDisplayName() {
 		return str_replace('%1', $this->type_name, text(1157));
 	}
 	
 	function check( $object_it, $transitionIt ) {
 		return $object_it->get('Type') == $this->type_id;
 	}
}
