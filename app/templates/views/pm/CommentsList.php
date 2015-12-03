<a name="comments"></a>

<div id="commentsthread<?=$control_uid?>" url="<?=$url?>">

	<?php if (!$form_ready ) { ?>

	<?php $list->renderThread( $view ); ?>
	
	<div id="comments-form<?=$control_uid?>" style="<?=($icon_size != 'small' ? "min-height: 48px;" : '').($comments_count > 0 ? "margin-top:20px;" : "")?>"">
		<div class="comment">
			
			<?php if( $icon_size == 'small' ) { ?>
			
			<a tabindex="5" class="dashed <?=$new_link_class?>" onclick="javascript: showCommentForm('<?=$url?>',$('#comments-form<?=$control_uid?>'), '', '');">
				<?=translate('добавить комментарий')?>
			</a>
			
			<?php } else { ?>

			<a tabindex="5" class="btn btn-small btn-success" type="button" title="" onclick="javascript: showCommentForm('<?=$url?>',$('#comments-form<?=$control_uid?>'), '', '');">
			    <i class="icon-comment icon-white"></i> <?=translate('Добавить комментарий')?>
			</a>
			
			<div class="clear-fix">&nbsp;</div>
			
			<?php } ?>
        </div>
	</div>

	<?php } else { ?>
	
		<?php echo $form->render($view, array_merge($form->getRenderParms())); ?>
		<?php $list->renderThread( $view ); ?>
		
	<?php } ?>
	
</div>

<script type="text/javascript">
	$(function() 
	{
		var locstr = new String(window.location);
		
		if ( locstr.indexOf('#comment') > 0 )
		{
			var commentString = locstr.substring(locstr.indexOf('#comment'));

			var parts = commentString.split('#');
			
			if ( parts.length > 0 )
			{
				$('#'+parts[1]).find('.comment-author').css( {'background':'#ffdfdf'} );
			}
		}
	});

	var lastForm = $('#comments-form<?=$control_uid?>');
	var lastFormContent = $(lastForm).html();

	function showCommentForm( url, placeholder, comment_id, parent_id )
	{
		cookies.set('comments-state-<?=$form->getAnchorIt()->getId()?>', 'open', {hoursToLive:0});
	
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

				setTimeout(function() {
					$('.comments-section #Caption*').focus();
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
				
				$('.list_row_popup').each(function() {
					$(this).contextMenu( $('#'+$(this).attr('menu')) );
				});
				$("a.image_attach").fancybox({ 'hideOnContentClick': true });
			}
		});				
	}
</script>