<?php echo $scripts; ?>
<?php if ( !is_array($sections) || $transition != '' || $action != 'show' ) $sections = array(); ?>
<?php if ( $transition == '' ) $sections = array_merge($bottom_sections,$sections);?>
<?php
$secondary_attributes = array();
$skip_attributes = array();
$primary_sections = array();
$secondary_sections = array();

foreach( $sections as $key => $section ) {
	if ( $section instanceof PageSectionAttributes ) {
		$secondary_attributes[$section->getId()] = $section->getFields();
		$skip_attributes = array_merge($skip_attributes, $section->getFields());
		$primary_sections[$key] = $section;
	}
	else {
		$secondary_sections[$key] = $section;
	}
}
?>
<div class="tabs">
	<?php if ( count($sections) > 0 ) { ?>
    <ul class="ui-dialog-titlebar">
      <li><a href="#tab-main"><?=$caption.' '.$uid?></a></li>
	  <?php foreach ( $primary_sections as $key => $section ) { ?>
		<li><a href="#tab-<?=$section->getId()?>"><?=$section->getCaption()?></a></li>
	  <?php } ?>
	  <?php foreach ( $secondary_sections as $key => $section ) { ?>
	  <li><a href="#tab-<?=$section->getId()?>"><?=$section->getCaption()?></a></li>
	  <?php } ?>
      <li class="ui-tabs-close-button" style="float:right;"><span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span></li>
    </ul>
    <?php } ?>
	    <form class="form-horizontal" id="<?=$form_id?>" method="post" action="<?=$form_processor_url?>" enctype="<?=($formonly ? "application/x-www-form-urlencoded" : "multipart/form-data")?>" autocomplete="off" class_name="<?=$form_class_name?>">
	    	<fieldset>
	    	  	<input id="<?=$action_mode?>" type="hidden" name="action_mode" value="form">
	    	  	<input name="entity" value="<?=$entity?>" type="hidden">
	    	  	<input name="WasRecordVersion" value="<?=$record_version?>" type="hidden">
	    		<input type="hidden" action="true" id="<?=$class_name?>action" name="<?=$class_name?>action" value="">
	    		<input type="hidden" id="<?=$class_name?>Id" name="<?=$class_name.'Id'?>" value="<?=$object_id?>">
	    		<input type="hidden" name="Transition" value="<?=$transition?>">
				<div id="tab-main">
					<div class="<?=($source_parms['uid'] != '' ? 'source-left' : '')?>">
					<?php
						echo $view->render( $form_body_template, array(
							'warning' => $warning,
							'alert' => $alert,
							'attributes' => array_filter($attributes,
												function($value) use($skip_attributes) {
													return !in_array($value['id'], $skip_attributes);
												}),
							'formonly' => $formonly,
							'form' => $form
						));
						if ( $bottom_hint != '' ) echo $view->render('core/Hint.php', array('title' => $bottom_hint, 'name' => $form_id));
					?>
					</div>
					<? if ( $source_parms['uid'] != '' ) { ?>
					<div class="source-text">
						<div>
							<?=$source_parms['uid']?>
						</div>
						<br/>
						<div>
							<?=$source_parms['text']?>
						</div>
					</div>
					<? } ?>
				</div>
				<?php
				foreach( $secondary_attributes as $referenceName => $secondary ) {
					echo '<div id="tab-'.$referenceName.'">';
					echo $view->render( $form_body_template, array(
						'attributes' => array_filter($attributes,
											function($value) use($secondary) {
												return in_array($value['id'], $secondary);
											}),
						'formonly' => $formonly,
						'form' => $form
					));
					echo '</div>';
				}
				?>
			</fieldset>
		</form>

	<?php foreach ( $secondary_sections as $key => $section ) { ?>
	<div id="tab-<?=$section->getId()?>">
		<?php $section->render( $this, array() ); ?>
	</div>
	<?php }	?>

</div>

<script type="text/javascript">
    devpromOpts.saveButtonName = '<?=$button_save_title?>';
    devpromOpts.closeButtonName = '<?=translate('Отменить')?>';
    devpromOpts.deleteButtonName = '<?=translate('Удалить')?>';
</script>
