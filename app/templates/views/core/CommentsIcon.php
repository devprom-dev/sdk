<?php

include_once SERVER_ROOT_PATH."pm/methods/CommentWebMethod.php";

$method = new CommentWebMethod( $object_it );
		
if ( !$method->hasAccess() ) return;

$method->setRedirectUrl($redirect != '' ? $redirect : 'function() {window.location.reload();}');

?>
<div>
	<a class="btn btn-mini btn-success" title="<?=$method->getCaption()?>" onclick="<?=$method->getJSCall()?>">
		<i class="icon-comment icon-white"></i>
		<?=translate('Ответить')?>
	</a>
</div>
												
