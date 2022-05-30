<a name="comments"></a>

<div id="commentsthread<?=$control_uid?>" url="<?=$url?>" >

	<div style="display:table-cell;padding-right:30px;width:75%" class="comments-thread pull-left">
		<?php if (!$form_ready ) { ?>

		<?php if ( $sort == "asc" ) $list->renderThread( $view ); ?>

		<div class="hidden-print" id="comments-form<?=$control_uid?>" style="min-height: 48px;" modified="<?=time()?>">
			<div class="comment">

                <? if ( $form->IsAttributeModifiable('Caption') ) { ?>
                    <? if ( $public_comment ) { ?>
                        <a tabindex="5" class="btn btn-sm btn-success" type="button" title="" onclick="showCommentForm('<?=$url?>',$('#comments-form<?=$control_uid?>'), '', '', '<?=$control_uid?>');">
                            <i class="icon-comment icon-white"></i> <?=text(2477)?>
                        </a>
                    <? } ?>
                    <? if ( $private_comment ) { ?>
                        &nbsp; <a tabindex="5" class="btn btn-sm btn-secondary" type="button" title="" onclick="showCommentForm('<?=$url . '&IsPrivate=Y'?>',$('#comments-form<?=$control_uid?>'), '', '', '<?=$control_uid?>');">
                            <i class="icon-comment icon-white"></i> <?=text(2804)?>
                        </a>
                    <? } ?>
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

	<? if ( $comments_count > 0 ) { ?>
	<div style="vertical-align: top;position:absolute;right:0;">
        <div class="comments-toolbar">
            <?php foreach( $options as $optionKey => $optionName ) { ?>
                <label class="checkbox">
                    <input name="<?=$optionKey?>" onclick="javascript: filterComments();" class="comments-filter" type="checkbox" <?=(in_array($optionKey, $optionsDefault) ? 'checked' : '')?> > <?=$optionName?>
                </label>
            <?php } ?>
            <div class="comments-toolbar-btn"></div>
        </div>
	</div>
	<? } ?>
</div>

<script type="text/javascript">
	var lastForm = $('#comments-form<?=$control_uid?>');
	var lastFormContent = $(lastForm).html();

	function showCommentForm( url, placeholder, comment_id, parent_id, control_uid )
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
				'prevcomment': parent_id,
                'control-uid': control_uid
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