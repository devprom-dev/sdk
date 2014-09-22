<?php
 
class Bulk extends CommandForm
{
 	function validate()
 	{
		$this->checkRequired( array('ids', 'redirect', 'object', 'operation') );
		
		return true;
 	}
 	
 	function create()
	{
		global $model_factory, $_REQUEST;
		
		$object = $model_factory->getObject( $_REQUEST['object'] );
		
		if ( !is_a($object, 'Metaobject') ) $this->replyError( text(1061) );
		
		$object_it = $object->getExact( preg_split('/-/', trim($_REQUEST['ids'], '-')) );
		
		if ( !getFactory()->getAccessPolicy()->can_modify($object_it) ) $this->replyError( text(1062) );
		
		if ( $_REQUEST['operation'] == 'Delete' )
		{
			while ( !$object_it->end() )
			{
				if ( getFactory()->getAccessPolicy()->can_delete($object_it) ) $object_it->delete(); 
				
				$object_it->moveNext();
			}

			$this->replySuccess(text(496));
		}
		
		$this->replyRedirect( SanitizeUrl::parseUrl($_REQUEST['form_url']).'&bulkmode=complete&operation='.SanitizeUrl::parseUrl($_REQUEST['operation']) );
	}

	function getResultDescription( $result )
	{
		switch($result)
		{
			default:
				return parent::getResultDescription( $result );
		}
	}
}
