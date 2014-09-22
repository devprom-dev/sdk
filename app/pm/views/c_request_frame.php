<?php

 //////////////////////////////////////////////////////////////////////////////////////////////
 class IssuesProgressFrame
 {
 	var $progress, $drawtasks;
 	
 	function IssuesProgressFrame( $progress, $drawtasks = true )
 	{
 		$this->progress = $progress;
 		
 		$this->drawtasks = $drawtasks && getSession()->getProjectIt()->getMethodologyIt()->HasTasks();
 	}
 	
 	function draw()
 	{
 		if ( is_array($this->progress['R']) && $this->progress['R'][0] > 0 )
 		{
 			$this->drawLine( translate('Выполнено пожеланий'), $this->progress['R'][0],
 				$this->progress['R'][1], '#E6B51E', '#F8EABE' );
 		}
 		
 		if ( $this->drawtasks )
 		{
	 		if ( is_array($this->progress['A']) && $this->progress['A'][0] > 0 )
	 		{
	 			$this->drawLine( translate('Анализ'), $this->progress['A'][0],
	 				$this->progress['A'][1], '#D6632A', '#F2CCBA' );
	 		}

	 		if ( is_array($this->progress['D']) && $this->progress['D'][0] > 0 )
	 		{
	 			$this->drawLine( translate('Реализация'), $this->progress['D'][0],
	 				$this->progress['D'][1], '#9ADC44', '#DAF2BA' );
	 		}

	 		if ( is_array($this->progress['T']) && $this->progress['T'][0] > 0 )
	 		{
	 			$this->drawLine( translate('Тестирование'), $this->progress['T'][0],
	 				$this->progress['T'][1], '#7D7A79', '#DAD9D8' );
	 		}

	 		if ( is_array($this->progress['H']) && $this->progress['H'][0] > 0 )
	 		{
	 			$this->drawLine( translate('Документирование'), $this->progress['H'][0],
	 				$this->progress['H'][1], '#5C9CCE', '#C6DEF0' );
	 		}
 		}
 	}
 	
 	function drawLine( $stage, $total, $resloved, $color, $background )
 	{
 		$progress = 100 - floor(($total - $resloved) / $total * 100);
 		
 		echo '<div class="progress">';
 		    echo '<div class="bar '.($progress == 100 ? 'bar-success' : 'bar-warning').'" style="width:'.$progress.'%;"></div>';
 		echo '</div>'; 

		if ( $this->drawtasks && $stage == translate('Выполнено пожеланий') )
		{
 			echo '<div style="border-bottom:1px dotted grey;margin-top:3px;margin-bottom:8px;"></div>';
		}
 	}
 }

 //////////////////////////////////////////////////////////////////////////////////////////////
 class ReleaseIssuesProgressFrame extends IssuesProgressFrame
 {
 	var $release_it, $type, $type_it;
 	
 	function ReleaseIssuesProgressFrame ( $release_it, $progress )
 	{
 		global $model_factory;
 		
 		$this->release_it = $release_it;
 		
 		$this->type = $model_factory->getObject('pm_TaskType');
 		$this->type_it = $this->type->getAll();
 		 
 		parent::IssuesProgressFrame( $progress );
 	}
 	
 	function drawLine( $stage, $total, $resloved, $color, $background )
 	{
		switch ( $stage )
		{
			case translate('Анализ'):
				$letter = $stage[0];
				$this->type_it->moveTo('ReferenceName', 'analysis');
				break;

			case translate('Реализация'):
				$letter = $stage[0];
				$this->type_it->moveTo('ReferenceName', 'development');
				break;

			case translate('Тестирование'):
				$letter = $stage[0];
				$this->type_it->moveTo('ReferenceName', 'testing');
				break;

			case translate('Документирование'):
				$letter = $stage[0];
				$this->type_it->moveTo('ReferenceName', 'documenting');
				break;
				
			default:
				parent::drawLine( $stage, $total, $resloved, $color, $background );
				return;
		}

		echo '<div style="float:left;width:100%;">';
			echo '<div style="float:left;width:18px;margin-top:-2px;">';
				echo $letter;
	 		echo '</div>';
	 		
			echo '<div style="float:right;width:90%;">';
				parent::drawLine( $stage, $total, $resloved, $color, $background );
			echo '</div>';
 	 	echo '</div>';
 	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class IssuesGroupFrame
 {
 	var $request_agg_it, $display_total, $url, $display_priority, $issue_type, $notbugs;
 	
 	function IssuesGroupFrame( $agg_it, $display_total = true )
 	{
 		global $model_factory;
 		
		$this->request_agg_it = $agg_it;
 		$this->display_total = $display_total;
 		$this->display_priority = true;
 		
 		$report = $model_factory->getObject('PMReport');
 		$report_it = $report->getExact('allissues');
 		
 		$this->url = $report_it->getUrl().'&kind=submitted';
 		$this->notbugs = array('none');
 		
 		$issuetype = $model_factory->getObject('pm_IssueType');
 		
 		$issuetype_it = $issuetype->getAll();
 		
 		while ( !$issuetype_it->end() )
 		{
 			$this->issue_type[$issuetype_it->get('ReferenceName')] = $issuetype_it->get('ReferenceName');
 			
 			if ( $issuetype_it->get('ReferenceName') != 'bug' )
 			{
 				array_push($this->notbugs, $issuetype_it->get('ReferenceName'));
 			}
 			
 			$issuetype_it->moveNext();
 		}
 		
 		$this->issue_type = array_unique($this->issue_type);
 		
 		$this->notbugs = array_unique($this->notbugs);
 	}
 	
 	function getUrl()
 	{
 	    return $this->url;
 	}
 	
 	function setUrl( $url )
 	{
 		$this->url = $url;
 	}
 	
 	function hidePriority()
 	{
		$this->display_priority = false;
 	}
 	
 	function draw()
 	{
		if ( $this->display_total )
		{
			$this->url .= '&state=all';
		}
		
		$total = 0;
		while ( !$this->request_agg_it->end() )
		{
			if ( $this->display_priority )
			{
				if ( $this->request_agg_it->get('Critical') > 0 )
				{
					echo '<div style="float:left;">';
						echo '<a href="'.$this->url.'&priority=1"><img src="/images/exclamation.png" title="'.translate('Критичные').'" style="margin-bottom:-2px;"></a><sup>'.
							$this->request_agg_it->get('Critical').'</sup>';
					echo '&nbsp;</div>';
				}
					
				if ( $this->request_agg_it->get('High') > 0 )
				{
					echo '<div style="float:left;">';
						echo '<a href="'.$this->url.'&priority=2"><img src="/images/error.png" title="'.translate('С высоким приоритетом').'" style="margin-bottom:-2px;"></a><sup>'.
							$this->request_agg_it->get('High').'</sup>';
					echo '&nbsp;</div>';
				}
			}
	
			if ( $this->request_agg_it->get('Bugs') > 0 )
			{
				echo '<div style="float:left;">';
					echo '<a href="'.$this->url.'&type=bug"><img src="/images/bug.png" title="'.translate('Ошибки').'" style="margin-bottom:-2px;"></a><sup>'.
						$this->request_agg_it->get('Bugs').'</sup>';
				echo '&nbsp;</div>';
				
				$total += $this->request_agg_it->get('Bugs');
			}
				
			if ( $this->request_agg_it->get('Issues') > 0 )
			{
				$filter = 'type='.join($this->notbugs,',');
				
				echo '<div style="float:left;">';
					echo '<a href="'.$this->url.'&'.$filter.'"><img src="/images/layout_add.png" title="'.translate('Пожелания и доработки').'" style="margin-bottom:-2px;"></a><sup>'.
						$this->request_agg_it->get('Issues').'</sup>';
				echo '&nbsp;</div>';

				$total += $this->request_agg_it->get('Issues');
			}
			
			$this->request_agg_it->moveNext();
		}

		if ( $this->display_total )
		{
			echo '<div style="clear:both;"></div>';

			if ( $total > 0 )
			{
				echo '<div class="line"></div>';
				echo '<div>';
					echo '<a href="'.$this->url.'">'.translate('Все пожелания').'</a>: '.$total;
				echo '&nbsp;</div>';
			}
		}
 	}
 }
 
?>