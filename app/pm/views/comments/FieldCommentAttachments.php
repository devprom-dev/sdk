<?php

include_once SERVER_ROOT_PATH.'pm/views/ui/FieldAttachments.php';
 
class FieldCommentAttachments extends FieldAttachments
{
 	function draw( $view = null )
 	{
		$count = $this->getAttachments()->getRecordCount();
		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'" style="float:left;width:360px;">';
			if ( $count < 1 ) {
				echo '<div style="float:left;width:20px;"><img src="/images/attach.png"></div>';
			}
			echo '<div style="float:left;width:90%;">';
				$this->drawBody($view);
			echo '</div>';
		echo '</div>';
 	}
}
