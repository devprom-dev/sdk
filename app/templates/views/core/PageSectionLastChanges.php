<ul class="changes-section">
<?php foreach( $rows as $row ) { ?>

	<li>
		<div class="title">
			<i class="<?=$row['icon']?>"></i>
			<b><?=$row['author']?></b>: <?=$row['datetime']?>
		</div>
		
		<?=$row['caption']?>
	</li>
	
<?php } ?>

</ul>