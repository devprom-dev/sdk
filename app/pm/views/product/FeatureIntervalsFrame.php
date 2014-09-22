<?php

 define ('INTERVAL_SCALE_MONTH', 'month');
 define ('INTERVAL_SCALE_QUARTER', 'quarter');
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class FeatureIntervalsFrame
 {
 	var $scale, $year, $interval_it;
 	
 	function FeatureIntervalsFrame()
 	{
 		$this->scale = INTERVAL_SCALE_MONTH;
 		$this->year = date('Y');
 	}
 	
 	function setIntervalIt( $interval_it )
 	{
 		$this->interval_it = $interval_it;
 	}
 	
 	function setScale( $scale )
 	{
 		$this->scale = $scale;
 	}
 	
 	function setYear( $year )
 	{
 		$this->year = $year;
 	}

 	function draw( $group_id )
 	{
 		global $model_factory;
 		
		$columns = array();
		$start_field = '';
		$finish_field = '';
		
		switch ( $this->scale )
		{
			case 'month':
				$date = $model_factory->getObject('DateMonth');
				$date_it = $date->getAll();

				while ( !$date_it->end() )
				{
					array_push($columns, $date_it->getId());
					$date_it->moveNext();
				}
				
				$start_field = 'StartMonth';
				$finish_field = 'FinishMonth';
				$begin_offset = 'StartDay';
				$end_offset_field = 'FinishDay';
				$original_finish_field = 'OriginalFinishMonth';
				$current_interval = date('m');
				$milestone_field = 'DateMonth';
				
				break;

			case 'quarter':
			default:
				array_push($columns, '1', '2', '3', '4');
				
				$start_field = 'StartQuarter';
				$finish_field = 'FinishQuarter';
				$original_finish_field = 'OriginalFinishQuarter';
				$current_interval = (int) floor(date('m') / 3.1) + 1;
				$milestone_field = 'DateQuarter';
		}
		
		echo '<div style="padding:0;overflow:hidden;width:100%;">';
			echo '<table class="features-chart-body" align="left" cellpadding=0 cellspacing=0>';

		$this->interval_it->moveTo('GroupId', $group_id);
		while ( !$this->interval_it->end() && $this->interval_it->get('GroupId') == $group_id )
		{
			$finish_year = $this->interval_it->get( 'FinishYear' );
			$start_year = $this->interval_it->get( 'StartYear' );

			if ( $finish_year < $this->year || $start_year > $this->year )
			{
				$this->interval_it->moveNext();
				continue;
			}
			
			$start = $this->interval_it->get( $start_field );
			$finish = $this->interval_it->get( $finish_field );
			$original_finish = $this->interval_it->get( $original_finish_field );

			$start_offset = $this->interval_it->get( $begin_offset );
			$end_offset = $this->interval_it->get( $end_offset_field );

			if ( $finish_year > $this->year )
			{
				$finish = $columns[count($columns) - 1];
				$end_offset = 40;
			}

			if ( $start_year < $this->year )
			{
				$start = 1;
				$start_offset = 0;
			}
			
			$color = "background:#E6B51E;";
			$was_caption = false;

			echo '<tr style="height:8px;">';
				foreach ( $columns as $column )
				{
					if ( $column >= $start && $column <= $finish )
					{
						$background = $color;
						
						if ( $original_finish != '' && $column > $original_finish )
						{
							$background = "background:red;";
						}

						echo '<td width="'.round(100 / count($columns) - 1, 0).'%" style="border:none;padding:0;">';

							$left_pad = 0;
							$mid_pad = 100;
							$right_pad = 0;
							
							if ( $column == $start && $start_offset > 1 )
							{
								$left_pad = min(max(round(($start_offset / 30) * 100, 0) - 1, 0), 90);
								$mid_pad -= $left_pad; 
							}

							if ( $column == $finish && $end_offset < 40 )
							{
								$right_pad = min(max(round(((30 - $end_offset) / 30) * 100, 0) - 1, 0), 90);
								$mid_pad -= $right_pad; 
							}

							if ( $left_pad > 0 )
							{
								$align = 'right';
								echo '<div style="float:left;margin-right:-1px;height:16px;width:'.$left_pad.'%;"></div>';
							}
							
							if ( $right_pad > 0 )
							{
								$align = 'left';
								echo '<div style="float:right;margin-left:-1px;height:16px;width:'.$right_pad.'%;"></div>';
							}

							$name = $column == $start ? "begin" : ($column == $finish ? "end" : "");
							$name = $column == $start && $column == $finish ? "whole" : $name;

							echo '<div class="release_normal" name="'.$name.'" style="height:16px;display:none;'.$background.';width:'.($mid_pad).'%;float:'.$align.';">';
								echo '&nbsp;';
							echo '</div>';

						echo '</td>';
					}
					else
					{
						echo '<td width="'.round(100 / count($columns) - 1, 0).'%" style="padding:0;border:none">&nbsp;</td>';
					}
				}
			echo '</tr>';
			
			$this->interval_it->moveNext();
		}

			echo '</table>';
		echo '</div>';

 		?>
		<script src="/scripts/jquery/jquery.corner.js" type="text/javascript" charset="utf-8"></script>
 		<script type="text/javascript">
			$('.release_normal[name=begin]').each(function(){ $(this).corner("round left 6px"); });
			$('.release_normal[name=end]').each(function(){ $(this).corner("round right 6px"); });
			$('.release_normal[name=whole]').each(function(){ $(this).corner("round 6px"); });
			$('.release_normal').each(function(){ $(this).show(); });
 		</script>
 		<?
 	}
 	
 	function drawListHeader()
 	{
 		global $model_factory;
 		
		$columns = array();
		$column_names = array();

		switch ( $this->scale )
		{
			case 'month':
				$date = $model_factory->getObject('DateMonth');
				$date_it = $date->getAll();

				while ( !$date_it->end() )
				{
					array_push($columns, $date_it->getId());
					$column_names = array_merge( $column_names,
						array(' '.$date_it->getId() => substr($date_it->getDisplayName(), 0, 3) ) );
						
					$date_it->moveNext();
				}
				
				$current_interval = date('m');
				break;

			case 'quarter':
			default:
				array_push($columns, '1', '2', '3', '4');
				$column_names = array( ' 1' => 'Q1', ' 2' => 'Q2', ' 3' => 'Q3', ' 4' => 'Q4');
			}
			
			echo '<table class="features-chart-header" align="left" cellpadding="0" cellspacing="0"><tr>';
				foreach ( $columns as $column )
				{
					echo '<td align="center" width="'.round(100 / count($columns) - 1, 0).'%">';
					if ( $column == $current_interval )
					{
						echo '<b>'.$column_names[' '.$column].'</b>';
					}
					else
					{
						echo $column_names[' '.$column];
					}
					echo '</td>';
				}
			echo '</tr></table>';
 	}
 }

?>