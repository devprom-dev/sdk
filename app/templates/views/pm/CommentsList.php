<a name="comments"></a>

<div id="commentsthread<?=$control_uid?>" url="<?=$url?>">

	<div style="display:table-cell;padding-right:30px;" class="comments-thread">
		<?php if (!$form_ready ) { ?>

		<?php if ( $sort == "asc" ) $list->renderThread( $view ); ?>

		<div class="hidden-print" id="comments-form<?=$control_uid?>" style="min-height: 48px;" modified="<?=time()?>">
			<div class="comment">

                <? if ( $form->IsAttributeModifable('Caption') ) { ?>
				<a tabindex="5" class="btn btn-sm btn-success" type="button" title="" onclick="showCommentForm('<?=$url?>',$('#comments-form<?=$control_uid?>'), '', '');">
					<i class="icon-comment icon-white"></i> <?=translate('Добавить комментарий')?>
				</a>
                <? } ?>

				<? if ( $collapseable ) { ?>
				<a tabindex="6" class="btn btn-sm btn-link" title="" onclick="toggleDocumentPageComments($('#comments-form<?=$control_uid?>'));">
					<?=text(2231)?>
				</a>
				<? } ?>

				<div class="clear-fix">&nbsp;</div>
			</div>
		</div>

		<?php if ( $sort == "desc" ) $list->renderThread( $view ); ?>

		<?php } else { ?>

			<?php echo $form->render($view, array_merge($form->getRenderParms())); ?>
			<?php $list->renderThread( $view ); ?>

		<?php } ?>
	</div>

	<? if ( $comments_count > 0 ) { if ( !$object_it->object instanceof WikiPage ) { ?>
	<div style="display:table-cell;vertical-align: top;width:15%;">
		<div class="sort-btn-desc" style="display:<?=($sort=='asc'?'block':'none')?>;">
			<a class="dashed" href="javascript:sortComments('-1')"><?=text(2320)?></a>
		</div>
		<div class="sort-btn-asc" style="display:<?=($sort=='desc'?'block':'none')?>;">
			<a class="dashed" href="javascript:sortComments('1')"><?=text(2321)?></a>
		</div>
	</div>
	<? }} ?>
</div>

<script type="text/javascript">
	$(function()
	{
		var locstr = String(window.location);
		if ( locstr.indexOf('#comment') > 0 )
		{
			var commentString = locstr.substring(locstr.indexOf('#comment'));
			var parts = commentString.split('#');
			if ( parts.length > 0 ) {
				$('#'+parts[1]).addClass('active');
			}
		}
	});

	var lastForm = $('#comments-form<?=$control_uid?>');
	var lastFormContent = $(lastForm).html();

	function showCommentForm( url, placeholder, comment_id, parent_id )
	{
		formDestroy('<?=$form->getId()?>');

		lastForm.html(lastFormContent);
		
	    if ( comment_id != '' )
		{
			placeholder = $('#comment'+comment_id); 
		}

	    lastForm = placeholder;

		lastFormContent = placeholder.html();
	    
		placeholder.html('<img src="/images/ajax-loader.gif">');
		
		$.ajax({
			url: url+'&form=comments',
			dataType: 'html',
			data: { 
				'comment': comment_id,
				'prevcomment': parent_id
			},
			error: function( xhr ) {
			},
			success: function( result ) 
			{
				placeholder.html(result);
                completeUIExt(placeholder);
				setTimeout(function() {
					try {
						var captionElement = placeholder.contents().find('[id*=Caption]');
						var editor = CKEDITOR.instances[captionElement.attr("id")];
						if ( editor ) editor.focus();
						placeholder.contents().find('body').focus();
					}
					catch(e) {}
				}, 300);
			}
		});				
	}

	function hideCommentForm()
	{
		formDestroy('<?=$form->getId()?>');

		lastForm.html(lastFormContent);
	}

	function refreshCommentsThread( thread_id )
	{
		if ( !validateForm($('form[id]')) ) return false; 

		$('#commentsreply'+thread_id).html('<img src="/images/ajax-loader.gif">');
		
		var url = $('#commentsthread'+thread_id).attr('url');

		$('.actionbutton').attr('disabled', true);
		
		$.ajax({
			url: url,
			dataType: 'html',
			error: function( xhr ) {
			},
			success: function( result ) 
			{
				$('#commentsreply'+thread_id).html('');
				
				formDestroy('<?=$form->getId()?>');
				
				$('#commentsthread'+thread_id).html(result);
                completeUIExt($('#commentsthread'+thread_id));
				
				$('.list_row_popup').each(function() {
					$(this).contextMenu( $('#'+$(this).attr('menu')) );
				});
			}
		});				
	}
</script>