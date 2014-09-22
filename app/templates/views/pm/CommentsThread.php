<?php if ( count($comments) > 0 ) { ?>

<?php } ?>

<?php foreach( $comments as $comment ) { ?>

<div style="padding:0 0 0 <?=($level * 68)?>px;">
	<a name="comment<?=$comment['id']?>"></a>
	
	<div id="comment<?=$comment['id']?>">
        <table class="comment-line">
            <tr>
    	        <td width="1%" style="padding-left:0;padding-right:0;">
    	        	<?php echo $view->render('core/UserPicture.php', array ( 'id' => $comment['author_id'], 'class' => 'user-avatar', 'title' => $comment['author'] )); ?>
    	        </td>
    	        <td style="padding-left:2px;">
                    <div class="comment-author">
        	            <?php 
        	            
            	            echo $view->render('core/TextMenu.php', array ( 
                    	        'title' => $comment['author'].' - '.$comment['created'].'&nbsp; ', 
                    	        'items' => $comment['actions']
                    	    ));
        	            
        	            ?>
                    </div>
                    <div class="comment-text">
                        
                        <div>
                        	<? echo $view->render('core/AttachmentTitles.php', array( 'files' => $comment['files'] )); ?>
                        </div>

                        <?=(count($comment['files']) > 0 ? '<br/>' : '')?>
                        
                        <? echo $view->render('core/PageFormAttribute.php', $comment); ?>
                        
                        <p></p>
                	    
                	    <div>
                    		<a name="comment_id_<?=$comment['id']?>"></a>
                    		
                    		<div id="commentsreply<?=$comment['id']?>">
                    			
                    			<div class="comment">
                    				<input class="btn btn-small" type="button" onclick="javascript: showCommentForm('<?=$url?>',$('#commentsreply<?=$comment['id']?>'), '', '<?=$comment['id']?>');" value="<?=translate('Ответить')?>">
                    			 </div>
                    		</div>
                    	</div>
                    </div>
    	        </td>
    	     </tr>
	    </table>
	</div>
</div>

<?php $list->renderThread( $view, $comment['thread_it'], $level + 1 ); ?>

<?php } ?>