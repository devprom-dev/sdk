<?php

class FeatureIterator extends OrderedIterator
{
 	var $request;
 	
 	function get( $attr_name ) 
 	{
 		if( $attr_name == 'Responsible') 
 		{
			$responsible = array('ResponsibleAnalyst', 'ResponsibleDesigner', 
				'ResponsibleDeveloper', 'ResponsibleTester', 'ResponsibleDocumenter');
			$caption = array('Анализ', 'Проектирование', 'Разработка', 
				'Тестирование', 'Документирование');
			
			for($i = 0; $i < count($responsible); $i++) {
				if($this->get($responsible[$i]) != '') {
					$part_it = $this->getRef($responsible[$i]);
					$value .= ' <i>'.translate($caption[$i]).'</i>: '.$part_it->getDisplayName().'<br/>';
				}
			}
 			return '<br/>'.$value;
 		}
 	 	
 		return parent::get( $attr_name );
 	}

 	/*
 	 * returns issues of the function
 	 */
 	function getPlannedIssueIt( $iteration_id )
 	{
 		$sql = 
	 	    ' SELECT r.* FROM pm_ChangeRequest r ' .
			'  WHERE r.Function = '.$this->getId().
			'    AND r.Project = '.getSession()->getProjectIt()->getId().
			'    AND EXISTS (SELECT 1 FROM pm_Task t ' .
			'				  WHERE t.ChangeRequest = r.pm_ChangeRequestId ' .
			'                   AND t.Release = ' .$iteration_id.' ) ';
			
 		return getFactory()->getObject('pm_ChangeRequest')->createSQLIterator( $sql );
 	}
} 