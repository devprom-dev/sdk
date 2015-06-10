<?php

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class AttributeView extends ViewBasic
 {
	function createForm() {
		return new AttributeForm( $this->object );
	}

	function createListForm() {
		$list = new AttributeList( $this->object );
		$list->maxonpage = 99;
		return $list;
	}
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class AttributeForm extends Form
 {
 	function getCaption() {
		return 'Свойство';
	}
	
	function createFieldObject( $name ) {
		if( $name == 'IsRequired' ) return new FieldCheckIsRequired;
		if( $name == 'IsVisible' ) return new FieldCheckIsVisible;
		if( $name == 'AttributeType' ) return new FieldAttributeType ( $this->object_it );
		else return parent::createFieldObject( $name );
	}

	function draw()
	{
		parent::draw();
		
		if($_REQUEST['entityId'] != '')
		{
			$ent = new Entity;
	?>
		<table width=100% cellpadding=3 cellspacing=3>
			<tr><td>
				<a href="<? echo $ent->getPageNameEditMode($_REQUEST['entityId']); ?>">
					Перейти к классу</a> »
			</td></tr>
		</table>
	<?	
		}
	}
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class AttributeList extends ListForm
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
 class FieldCheckIsRequired extends FieldCheck
 {
 	function FieldCheckIsRequired() {
		$this->checkName = 'Обязательно для заполнения';
	}
 }

 /////////////////////////////////////////////////////////////////////////////////////////////////////
 class FieldCheckIsVisible extends FieldCheck
 {
 	function FieldCheckIsVisible() {
		$this->checkName = 'Видимо на форме';
	}
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////
 class FieldAttributeType extends Field
 {
 	var $object_it;
	
	function FieldAttributeType( $object_it ) 
	{
		$this->object_it = $object_it;
	}
	
 	function draw()
	{
		$values = array( array('TEXT', 'Строка'),
						 array('LARGETEXT', 'Текст'),
						 array('RICHTEXT', 'Текст с форматированием'),
						 array('INTEGER', 'Число'),
						 array('FLOAT', 'Число с запятой'),
						 array('DATE', 'Дата/Время'),
						 array('CHAR', 'Галочка'),
						 array('PRICE', 'Цена'),
						 array('IMAGE', 'Изображение'),
						 array('FILE', 'Файл'),
						 );
						 
		$ents = new Entity;
		$entity_it = $ents->getAll();
		
		for($j = 0; $j < $entity_it->count(); $j++)
		{
			array_push($values, array('REF_'.$entity_it->get('ReferenceName').'Id', 'Ссылка: '.$entity_it->getCaption()));
			$entity_it->moveNext();
		}
	?>
		<table cellpadding=0 cellspacing=0>
			<tr>
				<td>
					<select name="AttributeType">
					<?
            			for($i = 0; $i < count($values); $i++) {
							if(isset($this->object_it)) {
								$selected = $values[$i][0] == $this->object_it->get("AttributeType") ? "selected" : "";
							}
						?>
							<option value="<? echo $values[$i][0]; ?>" <? echo $selected; ?>><? echo $values[$i][1]; ?></option>
						<?
            			}
					?>
					</select>
				</td>
			</tr>
		</table>
	<?
	}
 }
  
 ?>