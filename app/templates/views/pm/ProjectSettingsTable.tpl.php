<?php

$view->extend('core/PageBody.php');
$view['slots']->start('_header');
$view['slots']->stop();

?>

<div class="form-container project-settings">
<? foreach( $sections as $index => $section ) { ?>
	<div class="row">
		<div class="page-header">
			<h4><?=$section['name']?></h4>
		</div>
		<div>
			<? foreach( $section['items'] as $key => $item ) { ?>
			<span class="span3 settings-box" style="margin-left:0;">
				<ul class="nav nav-pills nav-stacked">
					<li><a uid="<?=$item['uid']?>" href="<?=$item['url']?>"><strong><?=$item['name']?></strong></a><p><?=$item['description']?></p></li>
				</ul>
			</span>
			<? } ?>
		</div>
	</div>
<? } ?>
</div>