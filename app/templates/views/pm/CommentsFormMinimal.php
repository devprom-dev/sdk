<?php foreach( $attributes as $key => $attribute ) { if ( $attribute['visible'] ) continue; ?>
    <input id="<?=$attribute['id']?>" type="hidden" name="<?=$key?>" value="<?=$attribute['value']?>">
<?php
}

$form->renderThread( $view, $form->getCommentIt() );
echo '<br/>';

echo $view->render('core/PageFormAttribute.php', $attributes['Caption']);
if ( $attributes['Caption']['description'] != '' ) {
    echo '<span class="help-block" style="margin-bottom:10px;">'.$attributes['Caption']['description'].'</span>';
}
echo $view->render('core/PageFormAttribute.php', $attributes['Attachment']);
echo '<br/>';

if ( $attributes['Notification']['visible'] ) {
    echo $view->render('core/PageFormAttribute.php', $attributes['Notification']);
}
