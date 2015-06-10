<?php

foreach( $attributes as $key => $attribute ) { if ( $attribute['visible'] ) continue;

?>

<input id="<?=$attribute['id']?>" type="hidden" name="<?=$key?>" value="<?=$attribute['value']?>">

<?php 

}

$form->renderThread( $view );
echo '<br/>';
echo $view->render('core/PageFormAttribute.php', $attributes['Caption']);

?>
