<?php
include_once "IteratorExport.php";

class IteratorExportXml extends IteratorExport
{
	function export()
	{
 		$uid = new ObjectUID;

	 	header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-control: no-store");
		header('Content-Type: text/xml; charset='.APP_ENCODING);
        header(EnvironmentSettings::getDownloadHeader($this->getName().'.xml'));

        $result = '<?xml version="1.0" encoding="utf-8"?><root>';

 		$fields = $this->getFields();
 		$keys = array_keys($fields);

		$it = $this->getIterator();
 		while( !$it->end() )
 		{
 			$result .= '<row>';
 			
 			foreach( $keys as $key )
 			{
 				switch ( $key )
 				{
 					case 'UID':
 					    $text = $uid->getObjectUid($it->getCurrentIt());
 					    break;
 						
 					default:
 					    $value = $this->get($key);
 					    if ( is_array($value) ) $value = join(PHP_EOL, $value);
 					    
 						$text = TextUtils::getXmlString($value);
 				}
 				$result .= '<'.$key.'>'.$text.'</'.$key.'>';
 			}
 			
 			$result .= '</row>';
 			$it->moveNext();
 		}

 		$result .= '</root>';
 		echo $result;
 	}
}