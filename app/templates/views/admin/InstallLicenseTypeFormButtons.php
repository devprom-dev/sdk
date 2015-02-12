<?php 

echo '<br/>'; 

echo '<div style="margin-left:1px;">';		
	echo text(1279);
echo '</div>';

echo '<br/>'; 

foreach( $buttons_parms['actions'] as $action )
{
?>

<input tabindex="1000" class="btn <?=$action['class']?>" type="<?=($action['type']==''?'button':$action['type'])?>" onclick="<?=$action['url']?>" value="<?=$action['name']?>"> &nbsp; 

<?php
}