<!DOCTYPE html>
<html>
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="/cache/?v=<?=$current_version?>&asset=1&type=css"/>
	<link rel="stylesheet" type="text/css" href="/cache/?v=<?=$current_version?>&asset=2&type=css"/>
	<script src="/cache/?v=<?=$current_version?>&asset=1&l=<?=$language_code?>" type="text/javascript" charset="UTF-8"></script>
	<script src="/cache/?v=<?=$current_version?>&asset=2&l=<?=$language_code?>" type="text/javascript" charset="UTF-8"></script>
	<script src="/cache/?v=<?=$current_version?>&asset=3&l=<?=$language_code?>" type="text/javascript" charset="UTF-8"></script>
  </head>
  <body style="background: white;margin: 10px 10px 0 10px;">
	  	<?=text('accountclient3')?>
	  	<hr/>
	  	<div class="alert alert-error">
	  	<?php
			  	switch( $parms['code'] ) {
			  	    case '1':
			  	    	echo text('accountclient4');
			  	    	break;
			  	    	
			  	    case '2':
			  	    	echo text('accountclient5');
			  	    	break;
			  	    	
			  	    case '3':
			  	    	echo text('accountclient6');
			  	    	break;
			  	    
			  	    default:
			  	    	echo text('accountclient7');
				}
	  	?>
	  	</div>
	  	<hr/>
	  	<?=text('accountclient8')?>
		<script type="text/javascript">
			$(document).ready( function() {
				window.parent.resizeModalWindow(); 
			});
		</script>
  </body>
</html>