<?php

 
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class MetaObjectView extends ViewBasic
 {
	function createForm() {
		return new MetaObjectForm( $this->object );
	}

	function createListForm() {
		$list = new MetaObjectList( $this->object );
		$list->maxonpage = 20;
		return $list;
	}
	
	function draw()
	{
	}
 }

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class MetaObjectForm extends Form
 {
 	function MetaObjectForm( $object, $dynamic_mode = false )
 	{
 		parent::Form( $object, $dynamic_mode );
 	}
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class MetaObjectList extends ListForm 
 {
 	function drawItem( $object_it )
	{
	?>
		<table cellpadding=2 cellspacing=2>
			<tr><td> <? echo $object_it->get(2); ?> </td></tr>
		</table>
	<?
	}
 }
 