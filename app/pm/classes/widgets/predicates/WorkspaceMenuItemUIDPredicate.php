<?php

class WorkspaceMenuItemUIDPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		$uids = array_filter(preg_split('/,/', $filter), function($value) {
			return $value != '';
		});
		return " AND (ReportUID IN ('".join("','",$uids)."') OR ModuleUID IN ('".join("','",$uids)."')) ";
 	}
}
