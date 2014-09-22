<?php

class ParticipantDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		$alias = $alias != '' ? $alias."." : "";
 		
 		$object = $this->getObject();
  		$objectPK = $alias.$object->getClassName().'Id';
 		
 		array_push( $columns, 
 			"( SELECT u.Caption FROM cms_User u WHERE u.cms_UserId = ".$alias."SystemUser ) Caption " );

 		array_push( $columns, 
 			"( SELECT u.Login FROM cms_User u WHERE u.cms_UserId = ".$alias."SystemUser ) Login " );

 		array_push( $columns, 
 			"( SELECT u.Email FROM cms_User u WHERE u.cms_UserId = ".$alias."SystemUser ) Email " );

 		array_push( $columns, 
 			"( SELECT IFNULL(SUM(r.Capacity), 0) " .
			"  	 FROM pm_ParticipantRole r" .
			" 	WHERE r.Participant = ".$objectPK." ) Capacity " );

 		return $columns;
 	}
}
