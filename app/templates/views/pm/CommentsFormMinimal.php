<?php foreach( $attributes as $key => $attribute ) { if ( $attribute['visible'] ) continue; ?>
    <input id="<?=$attribute['id']?>" type="hidden" name="<?=$key?>" value="<?=$attribute['value']?>">
<?php
}

if ( $object_id == '' ) {
    $form->renderThread( $view );
    echo '<br/>';
}

echo $view->render('core/PageFormAttribute.php', $attributes['Caption']);
if ( $attributes['Caption']['description'] != '' ) {
    echo '<span class="help-block" style="margin-bottom:10px;">'.$attributes['Caption']['description'].'</span>';
}
echo $view->render('core/PageFormAttribute.php', $attributes['Attachment']);
