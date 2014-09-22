<?php $view->extend('core/PageBody.php'); ?>

<div class="pull-left <?=(count($sections) > 0 ? 'span8' : 'span10')?>">
	<?php echo $view->render('core/FormAsyncBody.php', $parms); ?>
</div>

<div class="<?=(count($sections) > 0 ? 'span4' : 'span2')?>">
	<?php 
		echo $view->render('core/PageSections.php', array(
			'sections' => $sections,
			'object_class' => $object_class,
			'object_id' => $object_id 
		));
	?>
</div>