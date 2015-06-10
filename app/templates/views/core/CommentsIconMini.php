<?php

include_once SERVER_ROOT_PATH."pm/methods/CommentWebMethod.php";

$method = new CommentWebMethod( $object_it );
		
if ( !$method->hasAccess() ) return;

$method->setRedirectUrl($redirect != '' ? $redirect : 'function() {window.location.reload();}');

?>
<a title="<?=$method->getCaption()?>" onclick="<?=$method->getJSCall()?>">
	<i class="icon-comment"></i>
</a>
