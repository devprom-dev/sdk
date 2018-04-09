<?php foreach( $comments as $comment ) { ?>

<div class="comment-thread-container <?=($comment['private']?'private':'')?>" style="padding-left:<?=(min(1,$level) * 59)?>px;" modified="<?=$comment['modified']?>">
	<a name="comment<?=$comment['id']?>"></a>

	<div id="comment<?=$comment['id']?>" class="comment-line-holder">
        <table class="comment-line">
            <tr>
    	        <td class="hidden-print" width="1%" style="padding-left:0;padding-right:3px;vertical-align:top;">
    	        	<?php echo $view->render('core/UserPicture.php', array ( 'id' => $comment['author_id'], 'class' => 'user-avatar', 'title' => $comment['author'] )); ?>
    	        </td>
				<td class="hidden-print" style="width:4px;">
				</td>
    	        <td>
                    <div class="comment-author">
                        <span class="visible-print"><?=$comment['author']?>, <?=$comment['created']?></span>
                        <span class="hidden-print">
        	            <?php
							$title = '';
        	            	if ( $comment['photo_id'] == '' ) {
								$title = $comment['author'].', ';
							}
							$title .=  $comment['created'];
                            echo $title;
        	            ?>
                        </span>
        	        </div>
                    <div class="comment-text">
                        <?php if ( count($comment['files']) > 0 ) { ?>
                        <div style="margin-bottom:8px;">
                        	<? echo $view->render('core/AttachmentTitles.php', array( 'files' => $comment['files'] )); ?>
                        </div>
                        <?php } ?>
                        
                        <? echo $view->render('core/PageFormAttribute.php', $comment); ?>
                        <p></p>
                	    <div class="hidden-print">
                    		<a name="comment_id_<?=$comment['id']?>"></a>
                    		<div class="comments-reply" object-id="<?=$comment['id']?>" id="commentsreply<?=$comment['uid']?>">
                    			<div class="comment">
									<?php if ( !$readonly ) { ?>
										<a class="btn btn-small btn-link" onclick="showCommentForm('<?=$url?>',$('#commentsreply<?=$comment['uid']?>'), '', '<?=$comment['id']?>');" style="padding-left:0;">
											<?=translate('Ответить')?>
										</a>
									<?php } ?>
									<? if ( is_array($comment['uid_info']) ) { ?>
										<a class="btn btn-small btn-link clipboard btn-link-hidden" data-clipboard-text="<?=$comment['uid_info']['url']?>" data-message="<?=text(2029)?>" tabindex="-1"><?=translate('Ссылка')?></a>
									<?php } ?>
                                    <? if ( count($comment['actions']) > 0 ) { ?>
                                    <div class="btn-link-hidden">
                                        <div class="btn-group row-group-btn more-actions">
                                            <div class="btn dropdown-toggle transparent-btn" data-toggle="dropdown" href="#">
                                                <span class="label">...</span>
                                            </div>
                                            <?php echo $view->render('core/PopupMenu.php', array ( 'items' => $comment['actions']) ); ?>
                                        </div>
                                    </div>
                                    <? } ?>
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