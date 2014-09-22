<br/>

<?php foreach( $buttons_parms['actions'] as $action ) { ?>

<input tabindex="1000" class="btn <?=$action['class']?>" type="button" onclick="<?=$action['url']?>" value="<?=$action['name']?>"> &nbsp; 

<?php } ?>
