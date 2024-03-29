<?php
global $attributeIndex;
if ( !isset($attributeIndex) ) $attributeIndex = 10;
$attributeIndex++;

if ( $field instanceof FieldForm || $field instanceof FieldListOfReferences || ($field instanceof FieldDictionary && !$editable) )
{
    $field->setTabIndex($attributeIndex);
	echo '<span name="'.$field->getId().'" class="input-block-level well well-text '.$field->getCssClass().'" style="width:100%;height:auto;">';
		$field->render( $view );
	echo '</span>';
}
else if ( $field instanceof FieldEditable && $field->hasBorder() )
{
    $field->setTabIndex($attributeIndex);
	echo '<div class="well '.($field->readOnly() ? "input-block-level well-text" : "").' well-wysiwyg">';
		$field->render( $view );
	echo '</div>';
}
else
{
	if ( is_object($field) )
	{
        $field->setTabIndex($attributeIndex);
		$field->render( $view );
	}
	else
	{
		echo $html;		
	}
}
