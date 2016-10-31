<?php

include_once "WebMethod.php";

class BulkDeleteWebMethod extends WebMethod
{
	function __construct()
	{
		parent::__construct();
		
		$this->setRedirectUrl('donothing');
	}
	
 	function getCaption()
 	{
 		return translate('Удалить');
 	}

	function getDescription()
	{
		return text(911); 	
	}

	function url( $object, $ids = '' )
	{
		if ( $ids == '' ) $ids = '0';
 		return "javascript:processBulk('".$this->getCaption()."','?formonly=true&operation=Method:BulkDeleteWebMethod:class=".strtolower(get_class($object)).":objects=%ids%', ".$ids.", ".$this->getRedirectUrl().")";
	}
	
 	function execute_request()
 	{
 		global $_REQUEST, $model_factory;

		if ( $_REQUEST['class'] == '' || $_REQUEST['objects'] == '' ) throw new Exception('Required parameters missed');
		
		$class = $model_factory->getClass($_REQUEST['class']);
		
		if ( !class_exists($class) ) throw new Exception('Unknown class name given: '.$class); 
		
		$object = $model_factory->getObject($class);
		
		$ids = preg_split('/[,-]/', trim($_REQUEST['objects'], '-'));
		
		$object_it = $object->getExact($ids); 

        if ( $object instanceof Project ) {
            echo JsonWrapper::encode(
                array (
                    'state' => 'redirect',
                    'message' => '',
                    'object' => '/admin/backups.php?action=backupdatabase&parms=project,'. join('-',$object_it->idsToArray())
                )
            );
            exit();
        }

		while ( !$object_it->end() )
		{
		    if ( !getFactory()->getAccessPolicy()->can_delete($object_it) ) throw new Exception(text(1927));
		    
			$object_it->delete();
			
			$object_it->moveNext();
		}

		if ( class_exists('UndoWebMethod') ) {
			$method = new UndoWebMethod(ChangeLog::getTransaction());
			$method->setCookie();
		}
	}
}