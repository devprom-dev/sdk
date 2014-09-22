<?php

$view->extend('core/PageBody.php'); 

if (!session_id()) {
    session_start();
}

?>
<form>
	<fieldset>
		<legend> 
		    500 / Internal Server Error
		</legend>
	</fieldset>
	
	<p><?=text(1315)?></p>

	<br/>

    <p><?=(isset($_SESSION['error']) ? htmlentities($_SESSION['error']['error']['message'], ENT_QUOTES | ENT_HTML401, 'windows-1251') : htmlentities($text, ENT_QUOTES | ENT_HTML401, 'windows-1251'))?></p>
    <br/>
    
    <?php if ( defined('DISPLAY_ARCHIVE_ON_ERROR') && DISPLAY_ARCHIVE_ON_ERROR || !defined('DISPLAY_ARCHIVE_ON_ERROR') ) { ?>
    <p><?=text(1314)?></p>
    <ul>
    <li><p><a href="http://support.devprom.ru/issue/new">http://support.devprom.ru</a></p></li>
    <li>email: <a href="mailto:support@devprom.ru?subject=<?=text(1316)?>">support@devprom.ru</a></li>
    </ul>
    <?php } ?>
	
</form>



