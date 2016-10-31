<ul class="changes-section">
<?php foreach( $rows as $row ) { ?>
	<li>
		<div class="title">
			<i class="<?=$row['icon']?>"></i>
			<?php if ( $row['author'] != '' ) { ?>
				<b><?=$row['author']?></b>: 
			<?php } ?>
			<?=$row['datetime']?>
			<? if ( $row['duration'] >= 0 ) { ?>
			(<?=str_replace('%1', $row['duration'], text(2100))?>)
			<? } ?>
		</div>

		<?=($row['transition'] != '' ? $row['transition'] . ' &rarr; ' : '')?>
		<?=$row['state']?>
		<br/>

		<?php if ( $row['comment'] != '' ) { ?>
			<div class="alert alert-info"><?=$row['comment']?></div>
		<?php } ?>
	</li>
<?php } ?>
</ul>