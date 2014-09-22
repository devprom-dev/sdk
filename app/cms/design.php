<?php

function beginPage($title) 
 {
 	header('Content-Type: text/html; charset=windows-1251');
?>
    <html>
    <link rel="stylesheet" type="text/css" href="style.css">
	<title><? echo $title.' - eCMS'; ?></title>
    <body>
    	<script src="/cache/" type="text/javascript" charset="UTF-8"></script>
		<table width=100% height=100%>
			<tr>
        		<td height=40 width=50 style="background:#5357F9;">
				</td>
        		<td style="background:#5357F9;">
					<table width=100%>
						<tr>
							<td align=left>
							<?
								$parts = preg_split('/\//', $_SERVER['PHP_SELF']);
								if($parts[count($parts)-1] != 'index.php') {
								?>
									<a href="index.php" style="text-decoration:none;color:white;font-weight:bold">Главная »</a>
								<?
								}
								
								$parms = array();
								$keys = array_keys($_GET);
								for($k = 0; $k < count($keys); $k++) {
									array_push($parms, $keys[$k].'='.$_GET[$keys[$k]]);
								}
								$pageUrl = $parts[count($parts)-1].(count($parms) > 0 ? '?'.join('&',$parms): '');
							?>
								<a href="<? echo $pageUrl; ?>" 
								   style="text-decoration:none;color:white;font-weight:bold"><? echo $title; ?></a>
							</td>
							<td align=right>
								<a href="model.php" style="text-decoration:none;color:white;font-weight:bold">Модель</a>
								&nbsp&nbsp
								<a href="object.php?class=settings&settingsId=1&settingsaction=show" 
								   style="text-decoration:none;color:white;font-weight:bold">Настройки</a>
								&nbsp&nbsp
								<a href="sqllog.php" 
								   style="text-decoration:none;color:white;font-weight:bold">Журнал SQL</a>
							</td>
						</tr>
					</table>
        		</td>
				<td width=20 style="background:#5357F9;">
				</td>
			<tr>
				<td style="background:#efefef"></td>
				<td style="padding-top:5pt;padding-left:10pt;" valign=top>
<?
 }
 
 function endPage()
 {
?>
				</td>
				<td></td>
			</tr>
		</table>
    </body>
    </html>
<?
 }
?>