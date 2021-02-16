<?php

class PMDetailsList extends PageList
{
	function getIds( $values ) {
		return array(); // skip incremental update of list content
	}

	function getHeaderAttributes( $attr )
	{
		return array (
			'script' => '#',
			'name' => ''
		);
	}

	function IsNeedToDisplayNumber() {
		return false;
	}
	function IsNeedToSelect() {
		return false;
	}
	function IsNeedToDisplayOperations() {
		return false;
	}

	function getGroupDefault() {
		return '';
	}

	function getRenderParms()
	{
		return array_merge(
			parent::getRenderParms(),
			array (
				'show_header' => false,
				'autorefresh' => false
			)
		);
	}
}