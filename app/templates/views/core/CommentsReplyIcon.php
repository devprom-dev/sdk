<?php
$url = "workflowNewObject('".getSession()->getApplicationUrl($objectIt).'comment/'.$commentId.'/reply'."','Comment','Comment','',[],devpromOpts.UpdateUI);";
?>
<div>
	<a tabindex="-1" class="btn btn-xs btn-success" onclick="<?=$url?>">
		<i class="icon-comment icon-white"></i>
		<?=translate('Ответить')?>
	</a>
</div>
												
