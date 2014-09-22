<?php

include_once "ObjectSQLPersister.php";

class ObjectSQLPasswordPersister extends ObjectSQLPersister
{
 	var $algorithm;
 	
 	function ObjectSQLPasswordPersister()
 	{
		$this->algorithm = 'DES';
		
		if ( defined('MYSQL_ENCRYPTION_ALGORITHM') ) {
			$this->algorithm =  MYSQL_ENCRYPTION_ALGORITHM;
		}
 	}
 	
	function add( $object_id, $parms )
 	{
 		$this->modify( $object_id, $parms );
 	}

 	function modify( $object_id, $parms )
 	{
 		$object = $this->getObject();
 		
 		foreach( $object->getAttributes() as $ref_name => $attr )
 		{
 			if ( $object->getAttributeType($ref_name) != 'password' ) continue;
 			if ( $parms[$ref_name] == '' || $parms[$ref_name] == SHADOW_PASS ) continue;
 			
 			switch ( $this->algorithm )
			{
				case 'AES':
					$sql = 
						"UPDATE ".$object->getClassName().
						" SET ".$ref_name."=AES_ENCRYPT(".$ref_name.", '".INSTALLATION_UID."') ".
					    " WHERE ".$object->getClassName()."Id = ".$object_id;
					break;
	
				default:
					$sql = 
						"UPDATE ".$object->getClassName().
						" SET ".$ref_name."=DES_ENCRYPT(".$ref_name.", '".INSTALLATION_UID."') ".
					    " WHERE ".$object->getClassName()."Id = ".$object_id;
			}
			
			DAL::Instance()->Query($sql);
 		}
 	}

 	function delete( $object_id )
 	{
 	}
 	
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		$alias = $alias != '' ? $alias."." : "";
 		
 		$object = $this->getObject();
 		foreach( $object->getAttributes() as $ref_name => $attr )
 		{
 			if ( $object->getAttributeType($ref_name) != 'password' ) continue;
 			
			switch ( $this->algorithm )
			{
				case 'AES':
					$column = "AES_DECRYPT(".$alias.$ref_name.", '".INSTALLATION_UID."') ".$ref_name;
					break;
	
				default:
					$column = "DES_DECRYPT(".$alias.$ref_name.", '".INSTALLATION_UID."') ".$ref_name;
			}
			
 			array_push( $columns, $column ); 
 		}
 		
 		return $columns;
 	}
} 