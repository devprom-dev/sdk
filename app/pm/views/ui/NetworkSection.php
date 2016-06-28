<?php

class NetworkSection extends InfoSection
{
    var $object_it;
    
    function __construct( $object_it )
	{
		parent::__construct();
		$this->object_it = $object_it;
		$this->setPlacement('bottom');
    }
    
 	function getCaption() {
 		return text(2127);
 	}

	function getTemplate() {
		return 'pm/NetworkSection.php';
	}

	function getRenderParms()
	{
		return array_merge(
			parent::getRenderParms(),
			array (
				'id' => md5(microtime().get_class($this)),
				'networkUrl' => getSession()->getApplicationUrl().'network/'.get_class($this->object_it->object).'/'.$this->object_it->getId()
			)
		);
	}

	function IsActive()
	{
		$attributes = array_merge(
			$this->object_it->object->getAttributesByGroup('trace'),
			array(
				'Tasks',
				'Function'
			)
		);
		foreach( $attributes as $attribute ) {
			if ( $this->object_it->get($attribute) != '' ) return true;
		}
		return false;
	}
}