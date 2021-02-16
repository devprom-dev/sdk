<?php foreach( $comments as $comment ) { ?>

<div class="comment-thread-container <?=($comment['private']?'private':'')?> collapse <?=($comment['closed'] ? '' : 'in')?>" modified="<?=$comment['modified']?>" id="commentcontainer<?=$comment['uid']?>" <?=$comment['attributes']?> >
	<a name="comment<?=$comment['id']?>"></a>

    <table id="comment<?=$comment['id']?>" class="comment-line">
        <tr style="height: 1px;">
            <td class="hidden-print comm-thread" width="1%" style="height: inherit;vertical-align:top;">
                <table style="height: 100%">
                    <tr><td colspan="2" style="height: 1px;">
                        <div class="plus-minus-toggle <?=($comment['closed'] ? 'collapsed' : '')?>" data-toggle="collapse" href="#commentcontainer<?=$comment['uid']?>"></div>
                    </td></tr>
                    <tr>
                        <td width="45%"></td>
                        <td width="55%"><div class="com-collapse"></div></td>
                    </tr>
                </table>
            </td>
            <td>
                <div class="hidden-print comment-collapsed">
                    <span class="who"><?=$comment['author']?>, <?=$comment['created']?></span> &mdash; <?=$comment['text']?>
                </div>
                <div class="comment-line-holder">
                    <table>
                        <tr>
                            <td rowspan="2">
                                <?php echo $view->render('core/UserPicture.php', array ( 'id' => $comment['author_id'], 'class' => 'user-avatar', 'title' => $comment['author'] )); ?>
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
                            </td>
                        </tr>
                        <tr>
                            <td>
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
                                                    <a class="btn btn-sm btn-link" onclick="showCommentForm('<?=$url?>',$('#commentsreply<?=$comment['uid']?>'), '', '<?=$comment['id']?>');" style="padding-left:0;">
                                                        <?=translate('Ответить')?>
                                                    </a>
                                                    <? if ( $private_comment ) { ?>
                                                        &nbsp; <a class="btn btn-sm btn-link btn-link-hidden" onclick="showCommentForm('<?=$url.'&IsPrivate=Y'?>',$('#commentsreply<?=$comment['uid']?>'), '', '<?=$comment['id']?>');" style="padding-left:0;">
                                                            <?=text(2804)?>
                                                        </a>
                                                    <? } ?>
                                                <?php } ?>
                                                <? if ( is_array($comment['uid_info']) ) { ?>
                                                    <a class="btn btn-sm btn-link clipboard btn-link-hidden" data-clipboard-text="<?=$comment['uid_info']['url']?>" data-message="<?=text(2029)?>" tabindex="-1"><?=translate('Ссылка')?></a>
                                                <?php } ?>
                                                <? if ( count($comment['actions']) > 0 ) { ?>
                                                    <div class="btn-link-hidden">
                                                        <div class="btn-group row-group-btn more-actions">
                                                            <div class="btn dropdown-toggle transparent-btn" data-toggle="dropdown" href="">
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

                                <?php
                                if ( is_object($comment['thread_it']) ) {
                                    $list->renderThread( $view, $comment['thread_it'], $level + 1 );
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
         </tr>
    </table>

</div>
<?php } ?>