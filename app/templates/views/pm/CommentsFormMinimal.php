<?php

foreach( $attributes as $key => $attribute ) { if ( $attribute['visible'] ) continue;

?>

<input id="<?=$attribute['id']?>" type="hidden" name="<?=$key?>" value="<?=$attribute['value']?>">

<?php 

}

echo $view->render('core/PageFormAttribute.php', $attributes['Caption']);

?>
