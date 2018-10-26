<?php

include_once SERVER_ROOT_PATH.'pm/views/ui/FieldAttachments.php';
 
class FieldCommentAttachments extends FieldAttachments
{
 	function draw( $view = null )
 	{
 	    $this->setAddButtonText(text(2081));
        echo '<div class="uneditable-input" style="width:99%;height:auto;overflow:inherit;">';
            echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
                echo '<div style="float:left;width:90%;">';
                    $this->render($view);
                echo '</div>';
            echo '</div>';
        echo '</div>';
 	}
}
