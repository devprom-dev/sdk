<?php 

if ( is_a($field, 'FieldForm') )
{
	echo '<span id="'.$field->getId().'" class="input-block-level well well-text" style="width:100%;height:auto;">';
		$field->render( $view );
	echo '</span>';
}
else if ( is_a($field, 'FieldWYSIWYG') && $field->hasBorder() )
{
	echo '<div class="well well-wysiwyg '.($field->readOnly() ? "input-block-level well-text" : "").'">';
		$field->render( $view );
	echo '</div>';
}
else
{
	if ( is_object($field) )
	{
		$field->render( $view );
	}
	else
	{
		echo $html;		
	}
}
