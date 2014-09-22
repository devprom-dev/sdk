<?php

class IteratorExportSnapshot extends IteratorExport
{
	function getObjectIt()
	{
		global $model_factory;
		
		$table = $this->getTable();

		if ( !is_object($table) ) return getSession()->getProjectIt();
		
		if ( is_a($table, 'PMWikiDocument') ) return $table->getDocumentIt();
		
		$report_id = $table->getReport();
		
		if ( $report_id != '' ) return $model_factory->getObject('PMReport')->getExact($report_id);
		
		return getSession()->getProjectIt();
	}
	
	function export()
	{
		global $model_factory;
		
        $list_name = is_object($this->getTable()) ? $this->getTable()->getId() : '';
		
        $snapshot = $model_factory->getObject('cms_Snapshot');

 		exit(header('Location: '.$snapshot->getMakePage($this->getObjectIt(), $this->getIterator(), $list_name, $_REQUEST['redirect'])));
 	}
}