<?php if ( !$formonly ) $view->extend('core/PageBody.php'); else echo $scripts; ?>

<?php if ( !$formonly ) { ?>
<div class="pull-left <?=(count($sections) > 0 ? 'span8' : 'span10')?>">
<?php } else { ?>
<div style="margin: 10px 10px 0 10px;">
<div id="result" class=""></div>
<?php } ?>
	<?php 
		echo $view->render('core/FormAsyncBody.php', array_merge($parms, array('formonly' => $formonly))); 
	?>
</div>

<?php if ( !$formonly ) { ?>
<div class="<?=(count($sections) > 0 ? 'span4' : 'span2')?>">
	<?php 
		echo $view->render('core/PageSections.php', array(
			'sections' => $sections,
			'object_class' => $object_class,
			'object_id' => $object_id 
		));
	?>
</div>
<?php } ?>