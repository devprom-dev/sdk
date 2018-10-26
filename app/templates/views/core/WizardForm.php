<?php $view->extend('core/PageBody.php'); ?>

<?php $parms['buttons_template'] = 'core/WizardFormButtons.php'; ?>

<div class="pull-left span8">
	<?php echo $view->render('core/FormAsyncBody.php', array_merge($parms, array( 'actions_on_top' => false))); ?>
</div>
