<?php

include_once "IteratorExport.php";

class IteratorExportHtml extends IteratorExport
{
	function export()
	{
 		$uid = new ObjectUID;

	 	header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-control: no-store");

		header('Content-Type: text/html; charset='.APP_ENCODING);

 		$fields = $this->getFields();
 		
 		$keys = array_keys($fields);
 		
 		$result = '<html><link rel="stylesheet" type="text/css" href="/cache/?type=css">';
 		$result .= '<body style="background:white;"><table class="table table-bordered"><tr>';

		foreach ( $fields as $key => $field )
		{
			$result .= '<th class="">'.$field.'</th>';
		}
		$result .= '</tr>';

		$it = $this->getIterator();
		
		$i = 0;
		
 		while( !$it->end() )
 		{
 			$result .= '<tr>';
 			
 			for ( $j = 0; $j < count($keys); $j++ )
 			{
 				switch ( $keys[$j] )
 				{
 					case 'UID':
 						
 					    $text = $uid->getObjectUid($it->getCurrentIt());
 						
 					    break;
 						
 					default:

 					    $value = $this->get($keys[$j]);
 					    
 					    if ( is_array($value) ) $value = join('<br/>', $value);
 					    
 						$text = html_entity_decode($value, ENT_COMPAT | ENT_HTML401, APP_ENCODING); 
 				}
 				
 				$result .= '<td>'.$text.'</td>';
 			}
 			
 			$result .= '</tr>';

 			$it->moveNext();
 		}

 		$result .= '</table></body></html>';
 		echo $result;
 	}
}