<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE
include_once "BusinessRulePredicate.php";

class IssueReleaseRule extends BusinessRulePredicate
{
 	private $releaseName = '';
    private $releaseRefName = '';
 	private $releaseId = '';

 	function __construct( $releaseIt = null )
 	{
		if ( $releaseIt instanceof OrderedIterator ) {
			$this->releaseId = $releaseIt->getId();
            $this->releaseRefName = $releaseIt->get('Caption');
			$this->releaseName = $releaseIt->getDisplayName();
		}
		else {
			$this->releaseName = $this->getObject()->getDisplayName();
		}
 	}

	public function __sleep() {
		return array('releaseName','releaseRefName','releaseId');
	}

 	function getId() {
 		return md5(strtolower(get_class($this)).$this->releaseRefName);
 	}
 	
 	function getObject() {
 		return getFactory()->getObject('Request');
 	}
 	
 	function getDisplayName() {
 		return str_replace('%1', $this->releaseName, text(2616));
 	}
 	
 	function check( $object_it, $transitionIt ) {
 		return $object_it->get('PlannedRelease') == $this->releaseId;
 	}
}
