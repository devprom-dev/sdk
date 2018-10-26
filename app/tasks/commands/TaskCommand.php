<?php
include_once SERVER_ROOT_PATH . 'core/c_command.php';

class TaskCommand extends Command
{
	function setChunk( $parms )
	{
		$this->parms = $parms;
	}
	
	function getChunk()
	{
		return $this->parms;
	}
	
	protected function getData()
	{
		return getFactory()->getObject('co_ScheduledJob')->getRegistry()->Query(
				array (
						new FilterAttributePredicate('ClassName', strtolower(get_class($this)))
				)
		);
	}
	
	protected function getJob( $ref_name = '' )
	{
		if ( $ref_name == '' ) $ref_name = get_class($this);
		
		$registry = getFactory()->getObject('cms_BatchJob')->getRegistry();
		
		$registry->setLimit(1);
		
		return $registry->Query(
				array (
						new FilterTextExactPredicate('Caption', $ref_name),
						new SortOrderedClause()
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
	
	private $parms = array();
}