<?php

class IteratorExportIssueBoard extends IteratorExport
{
	/*
	 * returns a width of the column
	 */
	function getWidth( $field )
	{
		return 200;
	}
	
	function export()
	{
	 	header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-control: no-store");

		header('Content-Type: text/html; charset='.APP_ENCODING);

 		$fields = $this->getFields();
 		$keys = array_keys($fields);
 		
 		$it = $this->getIterator();
 		
 		$uid = new ObjectUID;
 		echo '<html><link rel="stylesheet" type="text/css" href="/cache/?type=print"><body style="background:white;">';

 		$i = 0;
 		
 		while( !$it->end() )
 		{
 			echo '<table class="taskcard" cellspacing="0" cellpadding="0">';
 				echo '<tr>';
	 				echo '<td class="left">';
	 					echo '<div class="caption">';
	 						echo $it->getHtml('Caption');
		 				echo '</div>';
	 				echo '</td>';
	 				echo '<td class="right">';
	 					echo '<table cellspacing="0" cellpadding="0" style="width:100%;height:100%;border-collapse:collapse;">';
		 					echo '<tr><td class="uid">';
		 						echo $uid->getObjectUid($it->getCurrentIt());
			 				echo '</td></tr>';
		 					echo '<tr><td class="field">';
		 						echo join(',', $this->get('Priority'));
			 				echo '</td></tr>';
		 					echo '<tr><td class="field">';
		 						echo join(',', $this->get('Author'));
			 				echo '</td></tr>';
		 					echo '<tr><td class="uid" style="border-bottom:0">';
		 						echo $this->get('Estimation');
			 				echo '</td></tr>';
		 				echo '</table>';
	 				echo '</td>';
 				echo '</tr>';
 			echo '</table>';
 			
 			if ( ($i + 1) % 4 == 0 ) {
 				echo '<div class="pagebreak"></div>';
 			}
 			
 			$it->moveNext();
 		}

 		echo '</body></html>';
 		echo $result;
 	}
}