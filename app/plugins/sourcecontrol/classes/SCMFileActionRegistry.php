<?php

include_once "SCMDataRegistry.php";

class SCMFileActionRegistry extends SCMDataRegistry
{
	public function addFileAction( $type, $path, $name, $action )
	{
		$parts = pathinfo($name);
		
		$this->addData( array (
				'Type' => $type,
				'Path' => $path,
				'Name' => $parts['basename'],
				'Action' => $action
		));
	}
}