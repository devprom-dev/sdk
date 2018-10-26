<?php

 include ('c_feature_frame.php');
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class CompetitorList extends PMPageList
 {
 	var $progress_it;
 	
	function retrieve()
	{
		parent::retrieve();
		
		$this->progress_it = $this->object->getProgressIt();
	}

 	function getColumns()
 	{
 		$this->object->addAttribute( 'Progress', '', translate('Прогресс'), true );
 		
 		return parent::getColumns();
 	}
 	
	function IsNeedToDisplay( $attr ) 
	{
		switch ( $attr )
		{
			default:
				return parent::IsNeedToDisplay( $attr );
		}
	}
	
	function drawCell( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'Progress':
				$this->progress_it->moveToId( $object_it->getId() );
				
				$frame = new FeatureAnalysisProgressFrame( 
					array( $this->progress_it->get('TotalFeatures'), 
						   $this->progress_it->get('AnalysedFeatures') ) 
					);
				
				$frame->draw();
				break;
				
			default:
				parent::drawCell( $object_it, $attr );
		}
	}
	
	function getColumnWidth( $attr )
	{
		switch ( $attr )
		{
			case 'Progress':
				return '120px';
				
			default:
				return parent::getColumnWidth( $attr );
		}
	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class CompetitorTable extends PMPageTable
 {
	function getObject()
	{
		global $model_factory;
 		return $model_factory->getObject('pm_Competitor');
	}
	
	function getList()
	{
		return new CompetitorList( $this->getObject() );
	}
 } 
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class FeatureAnalysisList extends PMPageList
 {
	function getPredicates( $values )
	{
		return array (
			new FeatureAnalysisCompetitorPredicate($values['competitor']),
			new FeatureAnalysisFeaturePredicate($values['feature'])
		);
	}
	
	function IsNeedToDisplay( $attr ) 
	{
		switch ( $attr )
		{
			case 'Competitor':
				return false;
				
			default:
				return parent::IsNeedToDisplay( $attr );
		}
	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class FeatureAnalysisTable extends PMPageTable
 {
	function getObject()
	{
		global $model_factory;
 		return $model_factory->getObject('pm_FeatureAnalysis');
	}
	
	function getList()
	{
		return new FeatureAnalysisList( $this->getObject() );
	}
	
	function getFilters()
	{
		global $model_factory;
		
		return array(
			new FilterObjectMethod( $model_factory->getObject('pm_Competitor') ),
			new FilterObjectMethod( $model_factory->getObject('Feature') )
			);
	}
	
	function getActions()
	{
		global $model_factory;
		
		$actions = parent::getActions();
		
		$competitor = $model_factory->getObject('pm_Competitor');
		$comp_actions = array(array());
		
		array_push( $comp_actions, 
			array ( 'name' => translate('Добавить продукт'),
					'url' => $competitor->getPageNameObject() ) );
		
		array_push( $comp_actions, 
			array ( 'name' => translate('Все продукты'),
					'url' => '?mode=competitors' ) );

		array_splice( $actions, 1, 0, $comp_actions );

		return $actions;
	}
 } 

 /////////////////////////////////////////////////////////////////////////////////
 class FeatureAnalysisPage extends PMPage
 {
 	function getObject()
 	{
 		global $_REQUEST, $model_factory;
 		
 		if ( $_REQUEST['entity'] != '' )
 		{
 			return $model_factory->getObject($_REQUEST['entity']);
 		}
 		
 		switch ( $_REQUEST['mode'] )
 		{
 			case 'competitors':
 				return $model_factory->getObject('pm_Competitor');
 				
 			default:
 				return $model_factory->getObject('pm_FeatureAnalysis');
 		}
 	}
 	
 	function getTable() 
 	{
 		global $_REQUEST;
 		
 		switch ( $_REQUEST['mode'] )
 		{
 			case 'competitors':
 				return new CompetitorTable();
 				
 			default:
 				return new FeatureAnalysisTable();
 		}
 	}
 	
 	function getForm() 
 	{
		return new PageForm( $this->getObject() );
 	}
 }
 
?>