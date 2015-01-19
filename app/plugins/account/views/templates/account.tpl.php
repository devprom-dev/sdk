<!DOCTYPE html>    
<html>
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1251"></meta>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="/cache/?v=<?=$current_version?>&type=css"/>
	<script src="/cache/?v=<?=$current_version?>&l=<?=$language_code?>" type="text/javascript" charset="UTF-8"></script>
  </head>
  <body style="background: white;margin: 10px 10px 0 10px;">
  		<div id="result" class=""></div>
		<?php echo $view->render('core/FormAsyncBody.php', $parms); ?>
  </body>
</html>