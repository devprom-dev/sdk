<?php

class ProjectRoleBaseIterator extends OrderedIterator
{
 	function get( $attr )
 	{
 		switch( $attr )
 		{
 			case 'Caption':
 				return translate(parent::get($attr));
 				
 			default:
 				return parent::get($attr);
 		}
 	}
 	
 	function getDerivedRole( $project_it )
 	{
 		global $model_factory;
 		
 		$sql = " SELECT t.* " .
 			   "   FROM pm_ProjectRole t" .
 			   "  WHERE t.ProjectRoleBase = '".$this->getId()."'" .
 			   "    AND EXISTS (SELECT 1 FROM pm_Project i " .
 			   "		         WHERE i.pm_ProjectId = ".$project_it->getId()."" .
 			   "				   AND i.VPD = t.VPD)";
 			   
 		$role = $model_factory->getObject('pm_ProjectRole');
 		
 		return $role->createSQLIterator( $sql );
 	}
}