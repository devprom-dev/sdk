<div class="history-user">
    <div class="actions hidden-print">
        <?php
            $form->drawButtons();
            if ( count($actions) > 0 && $action != 'show' ) {
                echo $view->render('core/PageFormButtons.php', array(
                    'actions' => $actions
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
            'title' => $title
        ));
    ?>
    <div class="form-container" style="clear:both;">
        <form class="form-horizontal frm-inline <?=$form_class?>" id="<?=$form_id?>" class_name="<?=$form_class_name?>" autocomplete="off">
            <fieldset>
                <?php
                    echo $view->render( $form_body_template, array(
                        'warning' => $warning,
                        'alert' => $alert,
                        'attributes' => $attributes,
                        'shortAttributes' => $shortAttributes,
                        'form' => $form,
                        'form_groups' => $form_groups
                    ));
                ?>
           </fieldset>
        </form>
    </div>

    <?php
        echo $view->render('core/PageSections.php', array(
            'sections' => $sections,
            'object_class' => $object_class,
            'object_id' => $object_id,
            'placement' => 'bottom'
        ));
    ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        makeForm('<?=$form_id?>','<?=$action?>');
    });
</script>
