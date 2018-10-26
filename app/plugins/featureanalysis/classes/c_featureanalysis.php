<?php
 
 /////////////////////////////////////////////////////////////////////////////////
 class FeatureAnalysisIterator extends OrderedIterator
 {
 	function getDisplayName()
 	{
 		$product_it = $this->getRef('Competitor');
 		$feature_it = $this->getRef('Feature');
 		
 		return $product_it->getDisplayName().': '.$feature_it->getDisplayName();
 	}
 } 
 
 /////////////////////////////////////////////////////////////////////////////////
 class FeatureAnalysis extends Metaobject
 {
 	function FeatureAnalysis() 
 	{
 		parent::Metaobject('pm_FeatureAnalysis');
 	}

	function createIterator() 
	{
		return new FeatureAnalysisIterator( $this );
	}
 }
 
 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 class FeatureAnalysisCompetitorPredicate extends FilterPredicate
 {
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$object = $model_factory->getObject('pm_Competitor');
 		$object_it = $object->getExact($filter);
 		
 		if ( $object_it->count() > 0 )
 		{
			return " AND Competitor = ".$object_it->getId();
 		}
 	}
 }

 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 class FeatureAnalysisFeaturePredicate extends FilterPredicate
 {
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$object = $model_factory->getObject('Feature');
 		$object_it = $object->getExact($filter);
 		
 		if ( $object_it->count() > 0 )
 		{
			return " AND Feature = ".$object_it->getId();
 		}
 	}
 }

?>