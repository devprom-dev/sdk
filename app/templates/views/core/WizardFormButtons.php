<?php 

echo '<div style="margin-left:1px;">';		
	$script = "javascript: window.location = '?';";

	echo '<input class="btn" value="'.translate('Отменить').'" style="float:left;" type="button" onclick="'.$script.'">';
echo '</div>';

?>

<script type="text/javascript">

$(document).ready( function() {
	$('#action<?=$form_id?>').val(1);

	formOptions.beforeSubmit = function() {};
	
	$('#<?=$form_id?>').ajaxSubmit(formOptions);
});

</script>