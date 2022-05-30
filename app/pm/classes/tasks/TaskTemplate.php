<?php
include_once SERVER_ROOT_PATH."core/classes/templates/ObjectTemplate.php";

class TaskTemplate extends ObjectTemplate
{
	public function getListName() {
		return 'template-'.$this->getTypeName();
	}
	
	public function getTypeName() {
		return 'task';
	}
	
	public function getAttributesTemplated()
	{
		$object = getFactory()->getObject($this->getTypeName());
		
		$attributes = array_diff(
            array_keys($object->getAttributes()),
            $object->getAttributesByGroup('system'),
            $object->getAttributesByGroup('trace'),
            array(
                'Project', 'RecordCreated', 'RecordModified', 'Author',
                'OrderNum', 'Tags', 'TraceTask', 'TraceInversedTask', 'Watchers', 'ChangeRequest', 'Attachment'
            )
		);

		foreach( $attributes as $key => $attribute ) {
		    if ( !$object->getAttributeEditable($attribute) ) {
		        unset($attributes[$key]);
            }
        }

		return $attributes;
	}
	
	function getDisplayName() {
		return text(3108);
	}
	
	function getClassName() {
		return get_class($this);
	}
	
	function getPage()
	{
		return getSession()->getApplicationUrl($this).
			'project/dicts/TaskTemplate?ListName='.$this->getListName().
				'&ObjectClass=Task&';
	}
}