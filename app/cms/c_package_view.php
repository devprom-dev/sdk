<?php

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class PackageView extends ViewBasic
 {
	function createForm() {
		return new PackageForm( $this->object );
	}

	function createListForm() {
		$list = new PackageList( $this->object );
		$list->maxonpage = 20;
		return $list;
	}
	
	function draw()
	{
		global $_REQUEST;
		parent::draw();

		if($_REQUEST['packageId'] != '')
		{
			$entity = getFactory()->getObject('Entity');
			$function = getFactory()->getObject('BusinessFunction');
	?>
		<table width=100%>
			<tr><td>
				<a href="<? echo $entity->getPageName().'&packageId='.$_REQUEST['packageId'];?>">
					Добавить новую сущность в пакет</a> »
			</td></tr>
			<tr><td>
				<a href="<? echo $function->getPageName().'&packageId='.$_REQUEST['packageId'];?>">
					Добавить новую бизнес-функцию в пакет</a> »
			</td></tr>
		</table>
	<?	
		}
	}
 }

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class PackageForm extends Form
 {
 	function getCaption() {
		return 'Пакет';
	}

	function createFieldObject( $name ) {
		if( $name == 'Description' ) return new FieldRichEdit;
		else return parent::createFieldObject( $name );
	}
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class PackageList extends ListForm 
 {
 	function drawItem( $object_it )
	{
	?>
		<table cellpadding=2 cellspacing=2>
			<tr><td> <? echo $object_it->getCaption(); ?> </td></tr>
		</table>
	<?
	}
 }
 