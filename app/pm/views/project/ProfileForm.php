<?php

include "ParticipantForm.php";

class ProfileForm extends ParticipantForm
{
	function transformInputValues( $id, $action )
	{
		global $_REQUEST;
		
		if($_REQUEST['IsActive'] == '') $_REQUEST['IsActive'] = 'Y';
	}
 	
 	function getCaption() 
 	{
 	    return text(1292);
	}
	
	function getFormPage() {
		return 'profile.php';
	}
	
 	function IsNeedButtonNew() {
		return false;
	}
 	function IsNeedButtonCopy() {
		return false;
	}
 	function IsNeedButtonDelete() {
		return false;
	}
 	function IsNeedButtonSave() {
		return true;
	}
	
 	function IsAttributeEditable( $attr ) 
 	{
 		return true;
 	}
 	
 	function checkAccess()
 	{
 		return true;
 	}
 	
	function IsAttributeVisible( $attr ) 
 	{
 		switch ( $attr )
 		{
 			case 'IsActive':
 			case 'SystemUser':
 			    return false;

 			default:
 				return parent::IsAttributeVisible( $attr );
 		}
	}
	
	function getActions()
	{
	    global $part_it;
	    
	    $session = getSession();
	    
	    $actions = array();
	    
	    $actions[] = array (
	        'name' => text(1289), 'url' => '/profile'
	    );
	    
	    $actions[] = array();

	    $actions[] = array (
	            'name' => text(1290), 'url' => '?mode=watchings'
	    );

	    $actions[] = array();
	    
	    $actions[] = array (
	            'name' => text(1291), 
	            'url' => $session->getApplicationUrl().'participants/rights?participant='.$part_it->getId()
	    );
	     
	    return $actions;
	}
}