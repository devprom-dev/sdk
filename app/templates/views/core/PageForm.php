<?php
$view->extend('core/PageBody.php');
$view['slots']->output('_content');

$has_caption = $uid_icon != '' || $caption != '' && $caption != $title;
?>

<div class="history-user">
    <div class="actions hidden-print">
        <?php
        $form->drawButtons();
        if ( count($actions) > 0 && $action != 'show' ) {
            echo $view->render('core/PageFormButtons.php', array(
                'actions' => $actions,
                'sections' => $sections
            ));
        }
        ?>
    </div> <!-- end actions -->
    <?php
        echo $view->render('core/PageBreadcrumbs.php', array(
            'navigation_url' => $navigation_url,
            'parent_widget_url' => $parent_widget_url,
            'parent_widget_title' => $parent_widget_title,
            'nearest_title' => $nearest_title,
            'has_caption' => $has_caption,
            'caption' => $caption,
            'uid' => $uid,
            'uid_url' => $uid_url,
            'state_name' => $state_name,
            'form' => $form,
            'listWidgetIt' => $listWidgetIt,
            'nextUrl' => $nextUrl,
            'nextTitle' => $nextTitle,
            'title' => $title
        ));
    ?>

    <div class="form-container <?=(count($sections) > 0 ? 'frm-bdy' : '')?>">
        <form class="form-horizontal frm-inline <?=$form_class?>" id="<?=$form_id?>" method="post" action="<?=$form_processor_url?>" enctype="<?=($formonly ? "application/x-www-form-urlencoded" : "multipart/form-data")?>" class_name="<?=$form_class_name?>" autocomplete="off">
            <fieldset>
                <input id="<?=htmlentities($action_mode)?>" type="hidden" name="action_mode" value="form">
                <input name="entity" value="<?=$className?>" type="hidden">
                <input name="WasRecordVersion" value="<?=htmlentities($record_version)?>" type="hidden">
                <input type="hidden" action="true" id="<?=$entity?>action" name="<?=$entity?>action" value="">
                <input type="hidden" id="<?=$entity?>Id" name="<?=$entity.'Id'?>" value="<?=htmlentities($object_id)?>">
                <input id="<?=$entity?>redirect" type="hidden" name="redirect" value="<?=htmlentities($redirect_url)?>">
                <input type="hidden" id="unsavedMessage" value="<?=text(632)?>">
                <input type="hidden" name="Transition" value="<?=htmlentities($transition)?>">

                <?php
                    echo $view->render( $form_body_template, array(
                        'warning' => $warning,
                        'alert' => $alert,
                        'attributes' => $attributes,
                        'shortAttributes' => $shortAttributes,
                        'form' => $form,
                        'form_groups' => $form_groups
                    ));
                    echo $view->render('core/Hint.php', array('title' => $bottom_hint, 'name' => $bottom_hint_id, 'open' => $hint_open));
                ?>
           </fieldset>
        </form>
    </div>
</div>

<div class="frm-sct">
    <?php
        echo $view->render('core/PageSections.php', array(
            'sections' => $sections,
            'object_class' => $object_class,
            'object_id' => $object_id,
            'style_class' => 'right-side-tab'
        ));
    ?>
</div>

<script type="text/javascript">
    var originalState = '';
    $(document).ready(function() {
        makeForm('<?=$form_id?>','<?=$action?>');
    });
    devpromOpts.saveButtonName = '<?=$button_save_title?>';
</script>
