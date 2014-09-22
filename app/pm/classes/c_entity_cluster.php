<?php

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class PMEntityClusterIterator extends OrderedIterator
 {
 }

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class PMEntityCluster extends Metaobject
 {
 	function PMEntityCluster() 
 	{
 		parent::Metaobject('cms_EntityCluster');
 	}
 	
 	function createIterator() 
 	{
 		return new PMEntityClusterIterator( $this );
 	}
 	
 	function IsVpdEnabled()
 	{
 		return true;
 	}
 	
  	function getNotificationEnabled()
 	{
 	    return false;
 	}
 	
 	function deleteLatest()
 	{
		$r2 = DAL::Instance()->Query(
				" DELETE FROM cms_EntityCluster ".
				"  WHERE RecordModified BETWEEN ".
				"			DATE_FORMAT(NOW(),'%Y-%m-%d 00:00:00') AND DATE_FORMAT(NOW(),'%Y-%m-%d 23:59:59') ".
				$this->getVpdPredicate('')
		);
 	}
 }
