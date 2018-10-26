<div style="display:table;width:100%;">

	<h4 class="title-cell bs" style="width:auto;padding-left:0;">
	    <? $attributes['Caption']['field']->draw(); ?>
	</h4>

	<div class="title-cell"><?=$uid_icon?></div>

	<div class="title-cell">
		<?php if ( count($actions) > 0 ) { ?>
		<div class="btn-group operation last">
			<a class="btn btn-xs dropdown-toggle" href="#" data-toggle="dropdown">
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
		<div class="document-page-bottom hidden-print">
			<div style="display:table;width:100%;height:23px;">
				<div style="display:table-cell;">
			<span class="<?=($comments_count < 1 ? 'document-item-bottom-hidden': '')?>">
				<i class="icon-comment"></i>
				<a class="document-page-comments-link dashed" style="margin-top:3px;">
					<?=translate('комментарии').($comments_count > 0 ? ' ('.$comments_count.')' : '')?>
				</a>
			</span>
				</div>
			</div>
			<div class="comments-section" style="display:none;">
				<?php $comments->render($this, array('new_link_class' => 'document-item-bottom-hidden')); ?>
			</div>
		</div>
	<?php } ?>
</div>

<div class="clear-fix" ></div>

<script type="text/javascript">
	$(document).ready( function() {
		makeupUI($(document));
	});
</script>