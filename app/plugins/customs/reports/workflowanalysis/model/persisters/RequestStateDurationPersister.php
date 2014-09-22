<?php

class RequestStateDurationPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$state_it = $this->getObject()->cacheStates();
 		
 		$columns = array();

		$is_first_state = true;
		
		while( !$state_it->end() )
		{
			if ( $state_it->get('IsTerminal') == 'Y' )
			{
				$state_it->moveNext();
				
				continue;
			}
			
			if ( $is_first_state )
			{
				$add_sql = 
				   " ((SELECT UNIX_TIMESTAMP(IFNULL(MIN(ps.RecordCreated), NOW())) ".
				   "     FROM pm_StateObject ps ".
				   "    WHERE ps.ObjectClass = '".$this->getObject()->getStatableClassName()."'".
				   " 	  AND ps.ObjectId = ".$this->getPK($alias).") - UNIX_TIMESTAMP(t.RecordCreated)) / 3600 + ";
				
				$is_first_state = false;
			}
			else
			{
				$add_sql = "";
			}
			
			$columns[] = 
				   " (SELECT ROUND(".$add_sql."(SELECT IFNULL(SUM(ABS(UNIX_TIMESTAMP(so.RecordCreated) ".
				   "			- (SELECT UNIX_TIMESTAMP(IFNULL(MIN(ps.RecordCreated), NOW())) ".
				   "				 FROM pm_StateObject ps ".
				   "				WHERE ps.ObjectClass = so.ObjectClass ".
				   "				  AND ps.ObjectId = so.ObjectId ".
				   "				  AND ps.pm_StateObjectId > so.pm_StateObjectId)) / 3600), 0) ".
				   "    		FROM pm_StateObject so ".
				   "   		   WHERE so.State = ".$state_it->getId().
				   "     		 AND so.ObjectId = ".$this->getPK($alias)." ) ".
				   "  		, 1)) State_".$state_it->getDbSafeReferenceName();
			
			$state_it->moveNext();
		}
		
 		return $columns;
 	}
}
