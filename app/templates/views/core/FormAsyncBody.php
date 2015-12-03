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

<form id="<?=$form_id?>" action="<?=$form_processor_url?>" method="post" enctype="application/x-www-form-urlencoded">
	<fieldset>
		<?php if ( !$formonly && $form_title != '' ) { ?>

		<legend class="<?=(count($actions) > 0 ? 'span10' : 'span12')?>"> 
		    <?=$form_title?>
		</legend>
		
		<?php if ( count($actions) > 1 ) { ?>
			<div class="actions">
				<div class="btn-group">
					<a class="btn btn-small dropdown-toggle btn-inverse" href="#" data-toggle="dropdown">
						<?=translate('Действия')?>
						<span class="caret"></span>
					</a>
					<? echo $view->render('core/PopupMenu.php', array ('items' => $actions)); ?>
				</div>
			</div> <!-- end actions -->
		<?php } ?>

		<?php if ( count($actions) == 1 ) { ?>
			<?php $item = array_shift($actions); ?>
			<div class="actions">
				<div class="btn-group">
					<a class="btn btn-small btn-inverse" href="<?=$item['url']?>">
						<?=$item['name']?>
					</a>
				</div>
			</div> <!-- end actions -->
		<?php } ?>
		
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
		<input type="hidden" name="object_id" value="<?=$object_id?>">
		<input type="hidden" name="redirect" value="<?=$redirect_url?>">
		<input type="hidden" name="form_url" value="<?=$form_url?>">
		
		<?php foreach( $attributes as $key => $attribute ) { ?>
		
			<?php if ( !$attribute['visible'] ) { ?>
			
      			<input type="hidden" name="<?=$key?>" value="<?=$attribute['value']?>">
			
			<?php continue; } ?>
			
		    <?php if ( $attribute['type'] == 'char' ) { ?>

			<label class="checkbox">
      			<input type="checkbox" tabindex="<?=$attribute['index']?>" id="<?=$attribute['id']?>" name="<?=$attribute['id']?>" <?=($attribute['value'] == 'Y' ? 'checked' : '')?> > <?=$attribute['caption']?>
    		</label>

			<?php } else if ( $attribute['type'] == 'custom' ) { ?>

			<? echo $form->drawCustomAttribute($key, $attribute['value'], $attribute['index']); ?>
			
    		<?php } else { ?>
			
			<label><?=$attribute['caption']?></label>
			
			<? echo $view->render('core/PageFormAttribute.php', $attribute); ?>

			<?php } ?>

			<?php if ( $attribute['description'] != '' ) { ?>
			
			<span class="help-block"><?=$attribute['description']?></span>
			<br/>
			
			<?php } ?>

		<?php } ?>
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
    
</form>
