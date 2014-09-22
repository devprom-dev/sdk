<?php

class ProjectVPDPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		$alias = $alias != '' ? $alias."." : "";
 		
		$object = $this->getObject();
  		$objectPK = $alias.$object->getClassName().'Id';
 		
 		array_push( $columns, "LCASE(CodeName) LowerCodeName " );
 		array_push( $columns, "(SELECT i.IsProjectInfo FROM pm_PublicInfo i WHERE i.Project =  ".$objectPK." ) IsProjectInfo " );
 		array_push( $columns, "(SELECT i.IsKnowledgeBase FROM pm_PublicInfo i WHERE i.Project =  ".$objectPK." ) IsKnowledgeBase " );
 		array_push( $columns, "(SELECT i.IsBlog FROM pm_PublicInfo i WHERE i.Project =  ".$objectPK." ) IsBlog " );
 		array_push( $columns, "(SELECT i.IsParticipants FROM pm_PublicInfo i WHERE i.Project =  ".$objectPK." ) IsParticipants " );
 		array_push( $columns, "(SELECT i.IsReleases FROM pm_PublicInfo i WHERE i.Project =  ".$objectPK." ) IsReleases " );
 		array_push( $columns, "(SELECT i.IsChangeRequests FROM pm_PublicInfo i WHERE i.Project =  ".$objectPK." ) IsChangeRequests " );
 		array_push( $columns, "(SELECT i.IsPublicDocumentation FROM pm_PublicInfo i WHERE i.Project =  ".$objectPK." ) IsPublicDocumentation " );
 		array_push( $columns, "(SELECT i.IsPublicArtefacts FROM pm_PublicInfo i WHERE i.Project =  ".$objectPK." ) IsPublicArtefacts " );

 		return $columns;
 	}
}
