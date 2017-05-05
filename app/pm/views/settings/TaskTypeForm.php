<?php
include "FieldTaskTypeStates.php";

class TaskTypeForm extends PMPageForm
{
 	function buildModelValidator()
 	{
 		$validator = parent::buildModelValidator();
 		$validator->addValidator( new ModelValidatorUnique(array('ReferenceName')) );
 		return $validator;
 	}
	
	function IsAttributeVisible( $attr_name ) 
 	{
 		switch ( $attr_name )
 		{
 			case 'ReferenceName':
 			case 'ParentTaskType':
 			case 'Stages':
			case 'States':
 				return true;

 			default:
 				return parent::IsAttributeVisible( $attr_name );
 		}
 	}

	function createFieldObject( $attr_name ) 
	{
		$this->object_it = $this->getObjectIt();
		switch( $attr_name )
		{
			case 'ParentTaskType': 
				return new FieldDictionary( getFactory()->getObject('TaskTypeBase') );

			case 'States':
				return new FieldTaskTypeStates($this->object_it);

			default:
				return parent::createFieldObject( $attr_name );
		}
	}
}