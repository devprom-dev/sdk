<?php
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class EntityView extends ViewBasic
 {
	function createForm() {
		return new EntityForm( $this->object );
	}

	function createListForm() {
		$list = new EntityList( $this->object );
		$list->maxonpage = 20;
		return $list;
	}
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class EntityForm extends Form
 {
 	function getCaption() {
		return '—ущность';
	}

	function createFieldObject( $name ) 
	{
		if( $name == 'Description' ) return new FieldRichEdit;
		if( $name == 'packageId' ) 
		{
			$ent = new Package;
			if ( isset($this->object_it) )
			{
				return new FieldListSelector( $ent->getExact($this->object_it->get('packageId')) );
			}
			else
			{
				return new FieldListSelector( $ent->getExact(0) );
			}
		}
		if( $name == 'IsOrdered' ) return new FieldCheckIsOrdered;
		if( $name == 'IsDictionary' ) return new FieldCheckIsDictionary;
		else return parent::createFieldObject( $name );
	}

	function draw() {
		parent::draw();
		
		if($_REQUEST['entityId'] != '')
		{
			$attribute = new Attribute($this->object->getExact($_REQUEST['entityId']));
	?>
		<table width=100% cellpadding=3 cellspacing=3>
			<tr><td>
				<a href="<? echo $attribute->getPageName(); ?>">
					–едактировать атрибуты</a> ї
			</td></tr>
		</table>
	<?	
		}
	}
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class EntityList extends ListForm 
 {
 	function drawItem( $object_it )
	{
	?>
		<table cellpadding=2 cellspacing=2>
			<tr><td> <? echo $object_it->getCaption(); ?> [<? echo $object_it->get('ReferenceName'); ?>]</td></tr>
		</table>
	<?
	}
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////
 class FieldCheckIsOrdered extends FieldCheck
 {
 	function FieldCheckIsOrdered() {
		$this->checkName = 'Ёкземпл€ры упор€дочены';
	}
 }

 /////////////////////////////////////////////////////////////////////////////////////////////////////
 class FieldCheckIsDictionary extends FieldCheck
 {
 	function FieldCheckIsDictionary() {
		$this->checkName = 'явл€етс€ справочником';
	}
 }
 
 
 ?>