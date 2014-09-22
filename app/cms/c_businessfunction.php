<?php
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class BusinessFunctionIterator extends OrderedIterator
 {
 	function getCaption() 
 	{
		return $this->get('Caption');
	}
	
	function getDescription() 
	{
		$richedit = new FieldRichEdit;
		return $richedit->decode($this->get('Description'));
	}
 }

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class BusinessFunction extends StoredObjectDB
 {
 	function BusinessFunction()
	{
		$this->attributes = array( 'Caption' => array('TEXT', 'Название', true),
								   'ReferenceName' => array('TEXT', 'Имя класса', true),
								   'packageId' => array('INTEGER', 'Пакет', true),
								   'OrderNum' => array('INTEGER', 'Порядковый номер', true),
								   'Description' => array('TEXT', 'Описание', true)
								    );
		$this->defaultsort = 'OrderNum';

		parent::StoredObjectDB();
	}
	
	function isAttributeRequired( $name ) {
		if( $name == 'ReferenceName' ) return true;
		if( $name == 'packageId' ) return true;
		return parent::isAttributeRequired( $name );
	}
	
	function createIterator() {
		return new BusinessFunctionIterator( $this );
	}

	function createDefaultView() {
		return new BusinessFunctionView( $this ); 
	}
 }

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class BusinessFunctionView extends ViewBasic
 {
	function createForm() {
		return new BusinessFunctionForm( $this->object );
	}

	function createListForm() {
		$list = new BusinessFunctionList( $this->object );
		$list->maxonpage = 30;
		return $list;
	}
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class BusinessFunctionForm extends Form
 {
 	function getCaption() {
		return 'Бизнес-функция';
	}

	function createFieldObject( $name ) {
		if( $name == 'Description' ) return new FieldRichEdit;
		if( $name == 'packageId' ) return new FieldListSelector( new Package );
		else return parent::createFieldObject( $name );
	}
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class BusinessFunctionList extends ListForm 
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
?>