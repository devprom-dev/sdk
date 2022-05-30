<?php
include_once SERVER_ROOT_PATH."pm/methods/CommentWebMethod.php";

$commentMethod = new CommentWebMethod( $object_it );
$commentMethod->setRedirectUrl('donothing');

$url = $object_it->get('CommentsCount') < 1
    ? ($commentMethod->hasAccess() ? $commentMethod->getJSCall() : '')
    : "javascript: $('.details-header a[did=comments]').attr('active-item', ".$object_it->getId().").click(); setTimeout(function(){filterItemComments(".$object_it->getId().");}, 500);";

$title = $object_it->get('CommentsCount') < 1
    ? text(2477)
    : text(3005);
?>
<a tabindex="-1" title="<?=$title?>" onclick="<?=$url?>">
    <? if ( $object_it->get('NewComments') != '' && $object_it->get('CommentsCount') > 0 ) { ?>
        <span class="label label-info">
    	    <i class="icon-white icon-comment has-comments"></i>
        </span>
    <? } else { ?>
        <i class="icon-comment <?=($object_it->get('CommentsCount') > 0 ? 'has-comments' : '' )?>"></i>
    <? } ?>
</a>
