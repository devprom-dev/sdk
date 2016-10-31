<?php

$view->extend('core/PageBody.php');

?>
<form>
	<fieldset>
		<legend> 
		    404 Not Found
		</legend>
	</fieldset>

	<? if ( !is_array($_SESSION['error']) && $_SESSION['error'] != '' ) { ?>
		<p><?=$_SESSION['error']?></p>
	<? } else { ?>
		<p><?=text(674)?></p>
		<?php
		echo '<br/><br/>';
		echo '<ul>';
		foreach( $reasons as $reason )
		{
			echo '<li>';
				echo preg_replace('/%1/', $missed_url, preg_replace('/%2/', $missed_url, $reason));
			echo '</li>';
		}
		echo '</ul>';
		?>
	<? } ?>
</form>
