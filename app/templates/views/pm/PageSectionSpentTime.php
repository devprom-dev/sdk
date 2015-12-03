<a name="" id="spent-time-details"></a>
<? if ( $spent_hours != '' ) { ?>
<div><?=str_replace('%1', $spent_hours, text(2075))?></div>
<? } ?>
<br/>
<?php foreach( $activities as $row ) { ?>
	<div class="embeddedRowTitle spent-time-row" date="<?=$row['date']?>" hours="<?=$row['capacity']?>" author="<?=$row['user']?>">
		<div class="title">
		<?php  
			echo $view->render('core/EmbeddedRowTitleMenu.php', array (
					'title' => '<b>'.$row['user'].'</b>:&nbsp;'.$row['capacity'].'&nbsp;'.translate('ч.').',&nbsp;'.$row['date'],
					'items' => $row['actions'],
					'position' => 'last'
			));
		?>
		</div>
		<div class="description">
			<?=$row['description']?>
		</div>
	</div>
	<br/>
<?php } ?>
