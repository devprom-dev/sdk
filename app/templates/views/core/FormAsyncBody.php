<?php if ( !$formonly ) { ?>
<script language="javascript">
	var formOptions = {};
	
	$(document).ready(function() 
	{
		formOptions = makeAsyncForm('<?=$form_id?>', '<?=$url?>', '<?=text(632)?>', typeof options != 'undefined' ? options : asyncFormOptions);

		<?php if ($_REQUEST['autosubmit'] == 'yep' ) { ?>
			$('#action<?=$form_id?>').val(1);
			formOptions.beforeSubmit = function() {};
			$('#<?=$form_id?>').ajaxSubmit(formOptions);
		<?php } ?>		
	});
</script>
<?php } ?>

<form id="<?=$form_id?>" action="<?=$form_processor_url?>" method="post" enctype="application/x-www-form-urlencoded" autocomplete="off" module="<?=$module?>" style="margin-right: 15px;">
	<fieldset>
		<?php if ( !$formonly && $form_title != '' ) { ?>

		<legend class="span12">
		    <?=$form_title?>

            <?php if ( $actions_on_top ) { ?>
            <div class="actions">
                <div class="btn-group">
                    <input type="submit" class="btn btn-primary" onclick="javascript: $('#action<?=$form_id?>').val(<?=$form_action?>);" value="<?=$button_text?>"/>

                    <?php if ( count($actions) > 1 ) { ?>
                        <a class="btn btn-sm dropdown-toggle btn-secondary" href="" data-toggle="dropdown">
                            <?=translate('Действия')?>
                            <span class="caret"></span>
                        </a>
                        <? echo $view->render('core/PopupMenu.php', array ('items' => $actions)); ?>
                    <?php } ?>

                    <?php if ( count($actions) == 1 ) { ?>
                        <?php $item = array_shift($actions); ?>
                        <a class="btn btn-secondary" href="<?=$item['url']?>">
                            <?=$item['name']?>
                        </a>
                    <?php } ?>
                </div>
            </div> <!-- end actions -->
            <? } ?>
		</legend>


		<?php } ?>
		
		<div class="clearfix"></div>

		<?php if ( $warning != '' ) { ?>

        <div class="alert alert-error"><?=$warning?></div>
        
        <?php } ?>
        
        <?php if ( $alert != '' ) { ?>
        
        <div class="alert alert-info"><?=$alert?></div>
        
        <?php } ?>
		
		<div class="" id="result<?=$form_id?>"></div>
	
		<input type="hidden" id="action<?=$form_id?>" name="action" value="<?=$form_action?>">
		<input type="hidden" name="MAX_FILE_SIZE" value="<?=EnvironmentSettings::getMaxFileSize()?>">
		<input type="hidden" name="object_id" value="<?=htmlentities($object_id)?>">
		<input type="hidden" name="redirect" value="<?=htmlentities($redirect_url)?>">
		<input type="hidden" name="form_url" value="<?=htmlentities($form_url)?>">

        <?php foreach( $columns as $columnKey => $attributes ) { ?>
            <div class="form-col-<?=$columnKey?> pull-left">
            <?php foreach( $attributes as $key => $attribute ) { ?>

                <? if ( $attribute['caption'] != '' && $attribute['visible'] ) { ?>
                <label><?=$attribute['caption']?></label>
                <? } ?>

                <? echo $view->render('core/PageFormAttribute.php', $attribute); ?>

                <?php if ( $attribute['description'] != '' && $attribute['visible'] ) { ?>

                <span class="help-block"><?=$attribute['description']?></span>
                <?=$fields_separator?>

                <?php } ?>

            <?php } ?>
            </div>
        <?php } ?>

		<?php
			echo $view->render('core/Hint.php', array('title' => $bottom_hint, 'name' => $bottom_hint_id, 'open' => $hint_open));
		?>
	</fieldset>

    <?php 
    
    if ( $buttons_template != '' && !$formonly )
    {
	    echo $view->render($buttons_template, array_merge($buttons_parms, array(
	            'b_has_preview' => $b_has_preview,
	            'form_id' => $form_id,
	            'form_action' => $form_action,
	            'button_text' => $button_text,
	            'buttons_parms' => $buttons_parms,
	            'redirect_url' => $redirect_url,
	            'object_id' => $object_id,
				'actions' => $actions
	    )));
    }
    
    ?>
    <br/><br/>
    
</form>
