<?php
include_once SERVER_ROOT_PATH."pm/methods/CommentWebMethod.php";

$commentMethod = new CommentWebMethod( $object_it );
$commentMethod->setRedirectUrl('donothing');
$url = $commentMethod->hasAccess() ? $commentMethod->getJSCall() : '';

?>
<a title="<?=text(2477)?>" onclick="<?=$url?>">
    <? if ( $object_it->get('NewComments') != '' ) { ?>
        <span class="label label-info">
    	    <i class="icon-white icon-comment has-comments"></i>
        </span>
    <? } else { ?>
        <i class="icon-comment"></i>
    <? } ?>
</a>
