<?php
if ( $text == '' ) $text = translate('Ответить');
include_once SERVER_ROOT_PATH."pm/methods/CommentWebMethod.php";

$method = new CommentWebMethod( $object_it );
if ( !$method->hasAccess() ) return;
if ( $redirect != '' ) $method->setRedirectUrl($redirect);

?>
<div>
	<a class="btn btn-xs btn-success" title="<?=$method->getCaption()?>" onclick="<?=$method->getJSCall()?>">
		<i class="icon-comment icon-white"></i>
		<?=$text?>
	</a>
</div>
												
