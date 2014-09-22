<?php

class AttributeGroupRegistry extends ObjectRegistrySQL
{
	private $data = array();
	
	public function add( $group, $name )
	{
		$this->data[] = array(
				'entityId' => $group,
				'Caption' => $name
		);
	}
	
	function getAll()
	{
		$this->add('system', translate('Ñèñòåìíûå'));

		$this->add('trace', translate('Òğàññèğîâêà'));

		$this->add('workflow', translate('Æèçíåííûé öèêë'));
    	
    	$this->add('states', translate('Ñîñòîÿíèÿ'));

    	$this->add('transition', translate('Äëÿ ïåğåõîäà'));
    	
		$this->add('time', translate('Òğóäîçàòğàòû'));
		
		$this->add('dates', translate('Äàòû'));
		
		return $this->createIterator($this->data);
	}
}