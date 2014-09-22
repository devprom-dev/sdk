<?php

include "FieldTaskTypeStages.php";

class TaskTypeForm extends PMPageForm
{
	function TaskTypeForm() 
	{
		global $model_factory;
		parent::PMPageForm( $model_factory->getObject('pm_TaskType') );
	}

 	function IsNeedButtonNew() {
		return false;
	}
	
 	function IsNeedButtonCopy() {
		return false;
	}

 	function IsAttributeVisible( $attr_name ) 
 	{
 		switch ( $attr_name )
 		{
 			case 'ReferenceName':
 			case 'ParentTaskType':
 			case 'Stages':
 				return true;

 			default:
 				return parent::IsAttributeVisible( $attr_name );
 		}
 	}

	function createFieldObject( $attr_name ) 
	{
		global $model_factory;

		$this->object_it = $this->getObjectIt();
				
		switch( $attr_name )
		{
			case 'ParentTaskType': 
				return new FieldDictionary( $model_factory->getObject('TaskTypeBase') );

			case 'Stages':
				return new FieldTaskTypeStages($this->object_it);
								
			default:
				return parent::createFieldObject( $attr_name );
		}
	}

	function getFieldDescription( $attr_name )
	{
		switch ( $attr_name )
		{
			case 'UsedInPlanning':
				return text(695);

			case 'Stages':
				return text(750);
		}
		
		return parent::getFieldDescription( $attr_name ); 
	}
}