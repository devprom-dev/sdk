<?php

$view->extend('core/PageBody.php'); 

?>

<div class="span9"> 
	<?php $form->draw(); ?> 
</div>

<div class="span3">
<?php 
	echo $view->render('core/PageSections.php', array(
		'sections' => $sections,
		'object_class' => $object_class,
		'object_id' => $object_id 
	));
?>
</div>
