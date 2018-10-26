<? foreach( $files as $file ) { ?>

<? 

$url = $file['type'] == 'image' ? $file['url'].'&.png' : $file['url'];  

$title = $file['name']." (".$file['size']." KB)";

?>

<span>
	<a class="<?=$file['type']?>_attach" data-fancybox="gallery" href="<?=$url?>" title="<?=$title?>">
		<img src="/images/<?=($file['type'] == 'image' ? 'image' : 'attach')?>.png"> <?=$title?>
	</a>
</span>

&nbsp;

<? } ?>