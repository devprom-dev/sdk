<?php if ( count($files) == 1 ) { ?>

<?php $file = array_pop($files); ?>

<div class="btn-group">
	<a class="<?=$file['type']?>_attach" href="<?=$file['url']?>&.png" title="<?=$file['name']?> (<?=$file['size']?> KB)">
		<img src="/images/<?=($file['type'] == 'image' ? 'image' : 'attach')?>.png">
	</a>
</div>

<?php } else { ?>

<?php 

$actions = array();
$id = md5(uniqid(time().$random,true));

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
<div class="btn-group">
	<a class="dropdown-toggle" data-toggle="dropdown" href="#" data-placement="right" data-target="#attachments<?=$id?>">
		<img src="/images/image.png">
	</a>
</div>
<div class="btn-group dropdown-fixed" id="attachments<?=$id?>">
	<? echo $this->render('core/PopupMenu.php', array ( 'items' => $actions )); ?>
</div>

<?php } ?>

<?php } ?>