<ul class="changes-section">
<?php foreach( $rows as $row ) { ?>

	<li>
		<div class="title">
			<i class="<?=$row['icon']?>"></i>
			<?php if ( $row['author'] != '' ) { ?>
				<b><?=$row['author']?></b>: 
			<?php } ?>
			<?=$row['datetime']?>
		</div>
		
		<?=$row['state']?>
		
		<br/>
		<?=$row['transition']?>
		
		<?php if ( $row['comment'] != '' ) { ?>
		
		<div class="alert alert-info"><?=$row['comment']?></div>
		
		<?php } ?>
		
	</li>
	
<?php } ?>

</ul>