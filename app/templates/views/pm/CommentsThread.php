<?php foreach( $comments as $comment ) { ?>

<div class="comment-thread-container <?=($comment['private']?'private':'')?>" style="padding:0 0 0 <?=(min(1,$level) * 59)?>px;" modified="<?=$comment['modified']?>">
	<a name="comment<?=$comment['id']?>"></a>

	<div id="comment<?=$comment['id']?>" class="comment-line-holder">
        <table class="comment-line">
            <tr>
    	        <td width="1%" style="padding-left:0;padding-right:3px;vertical-align:top;">
    	        	<?php echo $view->render('core/UserPicture.php', array ( 'id' => $comment['author_id'], 'class' => 'user-avatar', 'title' => $comment['author'] )); ?>
    	        </td>
				<td style="width:4px;">
				</td>
    	        <td>
                    <div class="comment-author">
        	            <?php
							$title = '';
        	            	if ( $comment['photo_id'] == '' ) {
								$title = $comment['author'].', ';
							}
							$title .=  $comment['created'];

            	            echo $view->render('core/ActionsMenu.php', array (
                    	        'title' => $title.'&nbsp; ',
                    	        'items' => $comment['actions']
                    	    ));
        	            
        	            ?>
        	        </div>
                    <div class="comment-text">
                        <?php if ( count($comment['files']) > 0 ) { ?>
                        <div style="margin-bottom:8px;">
                        	<? echo $view->render('core/AttachmentTitles.php', array( 'files' => $comment['files'] )); ?>
                        </div>
                        <?php } ?>
                        
                        <? echo $view->render('core/PageFormAttribute.php', $comment); ?>
                        <p></p>
                	    <div>
                    		<a name="comment_id_<?=$comment['id']?>"></a>
                    		<div class="comments-reply" object-id="<?=$comment['id']?>" id="commentsreply<?=$comment['uid']?>">
                    			<div class="comment">
									<?php if ( !$readonly ) { ?>
										<a class="btn btn-small btn-link" onclick="showCommentForm('<?=$url?>',$('#commentsreply<?=$comment['uid']?>'), '', '<?=$comment['id']?>');" style="padding-left:0;">
											<?=translate('Ответить')?>
										</a>
									<?php } ?>
									<? if ( is_array($comment['uid_info']) ) { ?>
										<a class="btn btn-small btn-link clipboard" data-clipboard-text="<?=$comment['uid_info']['url']?>" data-message="<?=text(2029)?>" tabindex="-1">
											<?=translate('Ссылка')?>
										</a>
									<?php } ?>
								</div>
                    		</div>
                    	</div>
                    </div>
    	        </td>
    	     </tr>
	    </table>
	</div>

	<?php $list->renderThread( $view, $comment['thread_it'], $level + 1 ); ?>
</div>

<?php } ?>