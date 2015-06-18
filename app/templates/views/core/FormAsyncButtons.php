<div id="preview"></div>

<?php $tab_index = 1000 ?>

<br/>

<?php if ( $b_has_preview ) { ?>

<input tabindex="<?php echo (++$tab_index); ?>" id="btn" class="btn btn-success" type="submit" 
	onclick="javascript: $('#action<?=$form_id?>').val(<?=CO_ACTION_PREVIEW?>);" value="<?=translate('Просмотр')?>">

<?php } ?>

<input tabindex="<?=(++$tab_index)?>" id="btn" class="btn btn-primary" type="submit" 
	onclick="javascript: $('#action<?=$form_id?>').val(<?=$form_action?>);" value="<?=$button_text?>">
	