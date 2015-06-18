<?php

 require_once('c_object.php');
 require_once('c_view.php');

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class Settings extends SingletonDB
 {
 	function Settings()
	{
		$this->attributes = array( 'FontSize' => array('INTEGER', 'Размер шрифта', true)
								    );
		parent::SingletonDB();
	}
	
	function createIterator() {
		return new Iterator( $this );
	}

	function createDefaultView() {
		return new SettingsView( $this ); 
	}
	
	function getPageName() {
		return 'object.php?class='.$this->getClassName().'&'.$this->getClassName().'Id=1&'.$this->getClassName().'action=show';
	}
 }

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class SettingsView extends ViewSimple
 {
	function createForm() {
		return new SettingsForm( $this->object );
	}
 }

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class SettingsForm extends Form
 {
 	function getCaption() {
		return 'Общие настройки';
	}
	
	function createFieldObject( $name ) {
		if( $name == 'Description' ) return new FieldRichEdit;
		if( $name == 'Phone' ) return new FieldLargeText;
		else return parent::createFieldObject( $name );
	}
 }
 ?>