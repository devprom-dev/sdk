<?php

class TaskCommand extends Command
{
	protected function getJob( $ref_name = '' )
	{
		if ( $ref_name == '' ) $ref_name = get_class($this);
		
		$registry = getFactory()->getObject('cms_BatchJob')->getRegistry();
		
		$registry->setLimit(1);
		
		return $registry->Query(
				array (
						new FilterAttributePredicate('Caption', $ref_name)
				)
		);
	}
	
	protected function addJob( $parms, $ref_name = '' )
	{
		if ( $ref_name == '' ) $ref_name = get_class($this);
		
		getFactory()->getObject('cms_BatchJob')->add_parms(
				array ( 
						'Caption' => $ref_name,
					    'Parameters' => $parms
				) 
		);
	}
}