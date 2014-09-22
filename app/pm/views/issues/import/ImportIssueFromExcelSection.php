<?php

include SERVER_ROOT_PATH.'pm/views/import/ImportXmlSection.php';

class ImportIssueFromExcelSection extends ImportXmlSection
{
	function getExcelUrl()
	{
		$url = parent::getExcelUrl();
		
		if ( $url == '' ) return $url;
		
		return $url.'&hide=all&show=Caption-Type-Description-Author-Priority';
	}
}