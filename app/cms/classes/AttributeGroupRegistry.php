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
		$this->add('system', translate('Системные'));

		$this->add('trace', translate('Трассировка'));

		$this->add('workflow', translate('Жизненный цикл'));
    	
    	$this->add('states', translate('Состояния'));

    	$this->add('transition', translate('Для перехода'));
    	
		$this->add('workload', translate('Трудозатраты'));
		
		$this->add('dates', translate('Даты'));

        $this->add('sla', 'SLA');

		return $this->createIterator($this->data);
	}
}