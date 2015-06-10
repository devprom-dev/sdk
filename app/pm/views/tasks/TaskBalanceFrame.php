<?php

class TaskBalanceFrame
{
 	var $planned, $fact;
 	
 	function __construct( $planned, $fact )
 	{
 		$this->hasvalues = getFactory()->getAccessPolicy()->can_read_attribute(getFactory()->getObject('Task'), 'Planned'); 

 		$this->planned = $planned;
 		
 		$this->fact = $fact;
 	}
 	
 	function draw()
 	{
 		if ( $this->hasvalues )
 		{
	 		$title = str_replace('%2', $this->fact, 
	 			str_replace('%1', $this->planned, text(489) ) );
 		}
 		else
 		{
 			$title = text(586);
 		}
 		
		$this->drawLine( $title, $this->planned,
			$this->fact, '#9ADC44', '#D6632A' );
 	}
 	
 	function drawLine( $stage, $planned, $fact, $color1, $color2 )
 	{
 		if ( $planned <= 0 )
 		{
 			$less_percent = 0;
 		}
 		else
 		{
 			$less_percent = round(max($planned - $fact, 0) / $planned * 100);
 		}

 		if ( $fact <= 0 )
 		{
 			$more_percent = 0;
 		}
 		else
 		{
 			$more_percent = round(max($fact - $planned, 0) / $fact * 100);
 		}
 		
 		$diff = $planned - $fact;
 		
		echo '<table class="progress-table" width="100%" cellpadding=0 cellspacing=1 style="min-width:40px;margin-top:2px;border:1px solid #d0d0e0;" title="'.text(1855).': '.round($planned, 1).' / '.round($fact, 1).' '.translate('ч.').'">';
			echo '<tr>';
				echo '<td width="50%" class="progress-left" style="background:#DAF2BA;">';
					echo '<div style="font-size:1px;height:8px;width:'.$less_percent.'%;background:'.$color1.';"> </div>';
				echo '</td>';
				echo '<td width="50%" class="progress-right" style="background:#F2CCBA;">';
					echo '<div style="font-size:1px;height:8px;width:'.$more_percent.'%;background:'.$color2.';"> </div>';
				echo '</td>';
			echo '</tr>';
		echo '</table>';
 	}
}