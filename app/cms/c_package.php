<?php

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class PackageIterator extends OrderedIterator
 {
 	function getCaption() {
		return $this->get('Caption');
	}
	function getDescription() {
		$richedit = new FieldRichEdit;
		return $richedit->decode($this->get('Description'));
	}
 }

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class Package extends StoredObjectDB
 {
 	function Package()
	{
		$this->attributes = array( 'Caption' => array('TEXT', '��������', true),
								   'Description' => array('TEXT', '��������', true),
								   'OrderNum' => array('INTEGER', '���������� �����', true)
								    );
		$this->defaultsort = 'OrderNum';

		parent::StoredObjectDB();
	}
	
	function createIterator() {
		return new PackageIterator( $this );
	}

	function createDefaultView() {
		return new PackageView( $this ); 
	}
 }
