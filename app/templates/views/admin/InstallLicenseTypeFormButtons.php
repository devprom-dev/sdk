<?php 

echo '<br/>'; 

echo '<div style="margin-left:1px;">';		
	echo text(1279);
echo '</div>';

echo '<br/>'; 

echo '<div style="margin-left:1px;">';		
	$script = "javascript: $('#action".$form_id."').val(".$form_action.");";

	echo '<input class="btn btn-primary" value="'.translate('Получить ключ').'" style="float:left;" type="submit" onclick="'.$script.'">';

	$script = "javascript: $('#action".$form_id."').val(3);";
	
	echo '<input class="btn" value="'.translate('Ввести ключ').'" style="float:left;margin-left:12px;" type="submit" onclick="'.$script.'">';
echo '</div>';

?>