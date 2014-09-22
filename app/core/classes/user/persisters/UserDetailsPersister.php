<?php

class UserDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		$alias = $alias != '' ? $alias."." : "";
 		
		$object = $this->getObject();
  		$objectPK = $alias.$object->getClassName().'Id';
 		
 		array_push( $columns, "(CASE (SELECT COUNT(1) FROM cms_User us WHERE us.IsAdmin = 'Y') WHEN 0 THEN 'Y' ELSE ".$alias."IsAdmin END ) IsAdministrator " );

 		array_push( $columns, "(SELECT COUNT(1) FROM cms_BlackList i WHERE i.SystemUser =  ".$objectPK." ) Blocks " );

 		return $columns;
 	}
}
