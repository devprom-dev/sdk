<div style="display:table;width:100%;">

	<h4 class="title-cell bs">
	    <? $attributes['Caption']['field']->draw(); ?>
	</h4>

	<div class="title-cell" style="width:2%;"></div>
	
	<div class="title-cell" style="width:1%;"><?=$uid_icon?></div>
	<div class="title-cell" style="width:1%;"></div>
	
	<div class="title-cell" style="width:1%;">
		<?php if ( count($actions) > 0 ) { ?>
		<div class="btn-group operation last">
			<a class="btn btn-small dropdown-toggle" href="#" data-toggle="dropdown">
				<i class="icon-asterisk icon-gray"></i>
				<span class="caret"></span>
			</a>
		
			<? echo $view->render('core/PopupMenu.php', array ('items' => $actions)); ?>
		</div>		
		<?php } ?>
	</div>
</div>

<p><? $attributes['Content']['field']->draw(); ?></p>

<div class="clear-fix" style="padding-top:4px;"></div>

<?php if ( $attributes['AuthorId']['visible'] ) { ?>

<p><? echo $view->render('core/PageFormAttribute.php', $attributes['AuthorId']); ?></p>

<?php } else { ?>

<br/>

<?php } ?>

<div class="clear-fix" style="padding-top:4px;"></div>

<div class="pull-left" style="padding-bottom:16px;">
	<?php if ( $comments_count > 0 ) { ?> 
	
	<a class="btn dropdown-toggle btn-mini btn-success dropdown-comments" href="#" title="<?=text(1501)?>">
		<i class="icon-comment icon-white"></i>
		<?=$comments_count?>
	</a>
	
	<div class="comments-section" style="display:none;">
		<?php $comments->render($this, array()); ?>
	</div>
	
	<?php } else { ?>
	
	<div class="comments-section">
		<?php $comments->render($this, array('icon_size' => 'small')); ?>
	</div>

	<?php } ?>
</div>

<div class="clear-fix" ></div>

<script type="text/javascript">
	$(document).ready( function() {
		makeupUI($(document));
	});
</script>