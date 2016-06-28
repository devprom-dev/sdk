<?php

class FieldWikiPageDependency extends Field
{
	function render( $view )
	{
		$uid = new ObjectUID();

		$objects = preg_split('/,/', $this->getValue());
		$uids = array();
		foreach( $objects as $object_info )
		{
			list($class, $id) = preg_split('/:/',$object_info);
			if ( !class_exists($class, false) ) continue;
			$ref_it = getFactory()->getObject($class)->getExact($id);
			$uids[$class.$id] = $uid->getUidWithCaption($ref_it);
		}
		ksort($uids);

		echo '<div class="input-block-level well well-text">';
			echo join('<br/>', $uids);
		echo '</div>';
	}
}