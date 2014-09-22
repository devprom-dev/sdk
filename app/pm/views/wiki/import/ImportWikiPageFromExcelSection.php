<?php

include SERVER_ROOT_PATH.'pm/views/import/ImportXmlSection.php';

class ImportWikiPageFromExcelSection extends ImportXmlSection
{
	function getExcelUrl()
	{
		$url = parent::getExcelUrl();
		
		if ( $url == '' ) return $url;
		
		return $url.'&hide=all&show=Caption-Content-ParentPage';
	}
}