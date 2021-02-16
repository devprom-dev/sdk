<?php
include_once SERVER_ROOT_PATH.'pm/views/issues/FieldIssueInverseTrace.php';

class MilestoneForm extends PMPageForm
{
 	function IsAttributeVisible( $attr_name ) 
 	{
 		switch($attr_name) 
 		{
 			case 'OrderNum':
            case 'RecentComment':
 				return false;
 		}
		return parent::IsAttributeVisible( $attr_name );
	}

	function createFieldObject( $name )
	{
		switch ( $name )
		{
			case 'TraceRequests':
				$field = new FieldIssueInverseTrace($this->getObjectIt(), getFactory()->getObject('RequestInversedTraceMilestone'));
				$field->showDeliveryDate();
				return $field;

			default:
				
				return parent::createFieldObject( $name );
		}
	}
}
