<?php

class FieldIssueTraces extends Field
{
	private $traces = '';

	function __construct( $traces )
	{
		parent::__construct();
		$this->traces = $traces;
	}

	function render( $view )
	{
		$uid = new ObjectUID();

		$objects = preg_split('/,/', $this->traces);
		$uids = array();
		foreach( $objects as $object_info )
		{
			list($class, $id) = preg_split('/:/',$object_info);
			if ( in_array($class, array('','TestCaseExecution')) ) continue;
			$ref_it = getFactory()->getObject($class)->getExact($id);
			$uids[$class.$id] = $uid->getUidWithCaption($ref_it);
		}
		ksort($uids);

		echo '<div class="input-block-level well well-text">';
			echo join('<br/>', $uids);
		echo '</div>';
	}
}