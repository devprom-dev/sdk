<?php

class ScrumForm extends PMPageForm
{
 	function IsNeedButtonNew() {
		return false;
	}
	
 	function IsNeedButtonCopy() {
		return false;
	}

 	function IsAttributeVisible( $attr_name ) {
 		switch($attr_name) {
 			case 'Participant':
 			case 'OrderNum':
 				return false;
 		}
		return parent::IsAttributeVisible( $attr_name );
	}
}
