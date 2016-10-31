<?php foreach( $comments as $comment ) { ?>

<div style="padding:0 0 0 <?=($level * 59)?>px;">
	<a name="comment<?=$comment['id']?>"></a>
	
	<div id="comment<?=$comment['id']?>" class="comment-line-holder">
        <table class="comment-line">
            <tr>
    	        <td width="1%" style="padding-left:0;padding-right:0;vertical-align:top;">
    	        	<?php echo $view->render('core/UserPicture.php', array ( 'id' => $comment['author_id'], 'class' => 'user-avatar', 'title' => $comment['author'] )); ?>
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
        	             &nbsp; 
        	            <? if ( is_array($comment['uid_info']) ) { echo $view->render('core/Clipboard.php', $comment['uid_info']); } ?>
        	        </div>
                    <div class="comment-text">
                        <?php if ( count($comment['files']) > 0 ) { ?>
                        <div style="margin-bottom:8px;">
                        	<? echo $view->render('core/AttachmentTitles.php', array( 'files' => $comment['files'] )); ?>
                        </div>
                        <?php } ?>
                        
                        <? echo $view->render('core/PageFormAttribute.php', $comment); ?>
                        <p></p>
                        <?php if ( !$readonly ) { ?>
                	    <div>
                    		<a name="comment_id_<?=$comment['id']?>"></a>
                    		<div id="commentsreply<?=$comment['id']?>">
                    			<div class="comment">
                    				<a class="btn btn-small btn-link" onclick="showCommentForm('<?=$url?>',$('#commentsreply<?=$comment['id']?>'), '', '<?=$comment['id']?>');" style="padding-left:0;">
										<?=translate('Ответить')?>
									</a>
                    			 </div>
                    		</div>
                    	</div>
                    	<?php } ?>
                    </div>
    	        </td>
    	     </tr>
	    </table>
	</div>
</div>

<?php $list->renderThread( $view, $comment['thread_it'], $level + 1 ); ?>

<?php } ?>