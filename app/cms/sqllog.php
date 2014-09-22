<?php

  require_once('common.php');
  require_once('design.php');

  $export_date = $_REQUEST['export_date'];
  
  if(isset($export_date)) 
  {
	header('Content-type: text/plain');
 	header("Content-Disposition: attachment; filename=export_".str_replace('-', '_', $export_date).".sql");
	
	$sql = 'SELECT * FROM SystemLogSQL WHERE DATE_FORMAT(RecordCreated, \'%Y-%m-%d\') = \''.$export_date.'\' ORDER BY RecordCreated ASC';
	$res = mysql_query($sql) or die('SQL ERROR: '.mysql_error().', SQL: '.$sql);

	for($i = 0; $i < mysql_num_rows($res); $i++) {
		$data = mysql_fetch_array($res);
		echo $data['SQLContent'].';'.Chr(13).Chr(10).Chr(13).Chr(10);
	}
	exit();
  }
  
  beginPage('Протокол выполнения SQL');
?>
	<table width=100% height=100%>
		<?
 			$sql = 'SELECT DISTINCT DATE_FORMAT(RecordCreated, \'%Y-%m-%d\') AS RecordCreated FROM SystemLogSQL '.
				   ' WHERE (TO_DAYS(NOW()) - TO_DAYS(RecordCreated)) < 6 ';
 			$res = mysql_query($sql) or die('SQL ERROR: '.mysql_error().', SQL: '.$sql);
		?>
		<tr>
			<td align=left style="border-bottom:.5pt solid silver;padding-bottom:10pt;" height=30>
				<table cellpadding=3 cellspacing=3>
					<tr>
        		<?
        		for($i = 0; $i < mysql_num_rows($res); $i++) {
        			$data = mysql_fetch_array($res);
        		?>
					<td><a target="_blank" href="sqllog.php?export_date=<? echo $data['RecordCreated']; ?>"><? echo $data['RecordCreated']; ?></a></td>
        		<?
        		}
        		?>
					</tr>
				</table>
			</td>
		</tr>
		<?
 			$sql = 'SELECT * FROM SystemLogSQL WHERE (TO_DAYS(NOW()) - TO_DAYS(RecordCreated)) < 2 ORDER BY RecordCreated DESC';
 			$res = mysql_query($sql) or die('SQL ERROR: '.mysql_error().', SQL: '.$sql);
		?>
		<tr>
			<td style="padding-top:10pt;" valign=top>
				<table width=100% cellpadding=3 cellspacing=3>
        		<?
        		for($i = 0; $i < mysql_num_rows($res); $i++) {
        			$data = mysql_fetch_array($res);
        		?>
					<tr>
						<td width=100><? echo $data['RecordCreated']; ?></td>
						<td><? echo $data['SQLContent']; ?></td>
					</tr>
        		<?
        		}
        		?>
				</table>
			</td>
		</tr>
	</table>
<?
	endPage();
?>
