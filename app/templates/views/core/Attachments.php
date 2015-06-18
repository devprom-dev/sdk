<?php if ( count($files) == 1 ) { ?>

<?php $file = array_pop($files); ?>

<div class="btn-group" style="margin:2px 2px 0 0;">
	<a class="<?=$file['type']?>_attach" href="<?=$file['url']?>&.png" title="<?=$file['name']?> (<?=$file['size']?> KB)">
		<img src="/images/<?=($file['type'] == 'image' ? 'image' : 'attach')?>.png">
	</a>
</div>

<?php } else { ?>

<?php 

$actions = array();

foreach( $files as $file )
{
	$actions[] = array(
		'name' => $file['name'].' ('.$file['size'].'KB)',
		'url' => $file['type'] == 'image' ? $file['url'].'&.png' : $file['url'],
		'class' => $file['type'] == 'image' ? "image_attach" : ""
	);
}

?>

<?php if ( count($actions) > 0 ) { ?>
<div class="btn-group" style="margin:2px 2px 0 2px;">
	<a class="dropdown-toggle" data-toggle="dropdown" href="#">
		<img src="/images/image.png">
	</a>
	
	<? echo $this->render('core/PopupMenu.php', array ( 'items' => $actions )); ?>
</div>

<?php } ?>

<?php } ?>