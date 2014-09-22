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
		$this->add('system', translate('���������'));

		$this->add('trace', translate('�����������'));

		$this->add('workflow', translate('��������� ����'));
    	
    	$this->add('states', translate('���������'));

    	$this->add('transition', translate('��� ��������'));
    	
		$this->add('time', translate('������������'));
		
		$this->add('dates', translate('����'));
		
		return $this->createIterator($this->data);
	}
}