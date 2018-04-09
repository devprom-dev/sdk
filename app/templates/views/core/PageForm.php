<?php 

if ( !$formonly )
{
    $view->extend('core/PageBody.php'); 
    
    $view['slots']->output('_content');
}
else
{
    echo $scripts;
}

$no_sections_class = is_a($form, 'PMWikiForm') ? 'span12' : 'span10';

$has_caption = $uid_icon != '' || $caption != '' && $caption != $navigation_title;
?>

<?php if (!$formonly) { ?>
    <div class="actions hidden-print">
        <?php
        $form->drawButtons();
        if ( count($actions) > 0 && $action != 'show' ) {
            echo $view->render('core/PageFormButtons.php', array(
                'actions' => $actions,
                'sections' => $bottom_sections
            ));
        }
        ?>
    </div> <!-- end actions -->

    <ul class="breadcrumb hidden-print" style="margin-right: 0px;">
        <?php
        if ( $uid != '' ) {
            if ( $navigation_url != '' ) {
                if ( $parent_widget_url != '' ) {
                    echo '<li><a href="'.$parent_widget_url.'">'.$parent_widget_title.'</a><span class="divider">/</span></li>';
                }
                echo '<li><a href="'.$navigation_url.'">'.$navigation_title.'</a><span class="divider">/</span></li>';
            }
            else if ( $has_caption ) {
                echo '<li>'.$caption.'<span class="divider">/</span></li>';
            }
            echo '<li>'.$view->render('core/Clipboard.php', array ('url' => $uid_url, 'uid' => $uid)).'</li>';

            if ( $state_name != '' ) {
                echo '<li class="clip" style="margin-left:8px;">'.$view->render('pm/StateColumn.php', array (
                    'color' => $form->getObjectIt()->get('StateColor'),
                    'name' => $form->getObjectIt()->get('StateName'),
                    'terminal' => $form->getObjectIt()->get('StateTerminal') == 'Y',
                    'id' => 'state-label'
                )).'</li>';
            }
            if ( $nextUrl != '' ) {
                echo '<li class="hidden-phone next-item">&#10140; <a class="btn btn-link" title="'.text(2333).'" href="'.$nextUrl.'">'.$nextTitle.'</a></li>';
            }
        }
        else {
            echo '<li><a href="'.$navigation_url.'">'.$navigation_title.'</a></li>';
        }
        ?>
    </ul> <!-- end breadcrumb -->
<?php } ?>


<div class="<?=($formonly ? '' : ($draw_sections && count($sections) > 0 ? 'span8' : $no_sections_class))?>" style="margin-left:0;overflow:hidden;">
    <form class="form-horizontal <?=$form_class?>" id="<?=$form_id?>" method="post" action="<?=$form_processor_url?>" enctype="<?=($formonly ? "application/x-www-form-urlencoded" : "multipart/form-data")?>" class_name="<?=$form_class_name?>" autocomplete="off">
    	<fieldset>
    	  	<input id="<?=$action_mode?>" type="hidden" name="action_mode" value="form">
    	  	<input name="entity" value="<?=$entity?>" type="hidden">
    	  	<input name="WasRecordVersion" value="<?=$record_version?>" type="hidden">
    		<input type="hidden" action="true" id="<?=$class_name?>action" name="<?=$class_name?>action" value="">
    		<input type="hidden" id="<?=$class_name?>Id" name="<?=$class_name.'Id'?>" value="<?=$object_id?>">
    		<input id="<?=$class_name?>redirect" type="hidden" name="redirect" value="<?=$redirect_url?>">
    		<input type="hidden" id="unsavedMessage" value="<?=text(632)?>">
    		<input type="hidden" name="Transition" value="<?=$transition?>">
    		
    		<?php 
				echo $view->render( $form_body_template, array(
					'warning' => $warning,
					'alert' => $alert,
					'attributes' => $attributes,
					'shortAttributes' => $shortAttributes,
					'formonly' => $formonly,
					'form' => $form
				));
				echo $view->render('core/Hint.php', array('title' => $bottom_hint, 'name' => $bottom_hint_id, 'open' => $hint_open));
            ?>
       </fieldset>
    </form>
</div>

<?php if (!$formonly && $draw_sections) { ?>
    <div class="span4">
        <?php 
        	echo $view->render('core/PageSections.php', array(
        		'sections' => $sections,
        		'object_class' => $object_class,
        		'object_id' => $object_id 
        	));
        ?>
    </div>
<?php } ?>


<?php 

if ( !$formonly && $draw_sections && count($bottom_sections) > 0 )
{
    echo '<div class="clearfix"></div>';
    
    echo $view->render('core/PageSections.php', array(
            'sections' => $bottom_sections,
            'object_class' => $object_class,
            'object_id' => $object_id
    ));

} // count($bottom_sections)

?>

<?php if ( !$formonly ) { ?>

<script type="text/javascript">
	var originalState = '';
	$(document).ready(function() 
	{
		makeForm('<?=$form_id?>','<?=$action?>');
	});
</script>

<?php } ?>

<script type="text/javascript">
    devpromOpts.saveButtonName = '<?=$button_save_title?>';
</script>
