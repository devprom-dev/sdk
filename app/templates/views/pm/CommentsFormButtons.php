<?php

echo $view->render('core/FormAsyncButtons.php', array(
    'b_has_preview' => $b_has_preview,
    'form_id' => $form_id,
    'form_action' => $form_action,
    'button_text' => $button_text,
    'redirect_url' => $redirect_url
));

echo '<input class="btn btn-link btn-sm" type="button" onclick="javascript: hideCommentForm()" value="'.translate('Отмена').'">';
echo '<div class="clear-fix"></div>';
echo '<br/>';
