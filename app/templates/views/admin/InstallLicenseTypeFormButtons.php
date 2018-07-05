<?php 

echo '<br/>'; 
echo '<br/>';

foreach( $buttons_parms['actions'] as $action )
{
?>

<input tabindex="1000" class="btn <?=$action['class']?>" type="<?=($action['type']==''?'button':$action['type'])?>" onclick="<?=$action['url']?>" value="<?=$action['name']?>"> &nbsp; 

<?php
}