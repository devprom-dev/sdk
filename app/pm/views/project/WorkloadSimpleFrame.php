<?php

class WorkloadSimpleFrame
{
 	var $release_it, $participant_it;
 	
 	function WorkloadSimpleFrame( $release_it, $participant_it )
 	{
 		$this->release_it = $release_it;
 		$this->participant_it = $participant_it;
 	}
 	
 	function draw()
 	{
 		global $model_factory;
 		
		$release_left_capacity = $this->release_it->getLeftCapacity();
		
		while ( !$this->participant_it->end() )
		{
			if ( $this->participant_it->get('Capacity') <= 0 )
			{
				$this->participant_it->moveNext();
				continue;
			}
			
			echo '<div class="line" style="float:left;width:30%;padding-right:20px;">';
				echo '<div class="line">';
					echo $this->participant_it->getDisplayName(); 
				echo '</div>';
	
				echo '<div class="line">';
					draw_plan_capacity( $release_left_capacity * $this->participant_it->get('Capacity'), 
						$this->release_it->getLeftWorkParticipant( $this->participant_it ), translate('ч.') );
				echo '</div>';
			echo '</div>';

			$this->participant_it->moveNext();
		}

		echo '<div style="clear:both;">';
		echo '</div>';
	}
}

 function draw_plan_capacity( $full_volume, $used_volume, $measure = 'дн.' )
 {
	$measure = translate($measure);
	$left_volume = $full_volume - $used_volume;
	if ( $full_volume > 0.0 )
	{
		$filled_volume = round(($used_volume / $full_volume) * 100, 0);
	}
	$overload = false;
	if($left_volume < 0) {
		$overload = true;
		if ( $filled_volume > 0.0 )
		{
			$filled_volume = round((100 / $filled_volume) * 100, 0);
		}
		else
		{
			$filled_volume = 0;
		}
	}
	?>
	<table width=100% cellpadding=0 cellspacing=0>
	<tr>	
		<td>
			<table cellpadding=0 cellspacing=0 width=100% style="margin-bottom:-2pt;">
	        	<tr>
	            	<td width=5 >
	                	<img src="/images/<? echo $filled_volume > 0 ? 'line_lf' : ($overload ? 'line_lo' : 'line_l') ?>.png">
	                </td>
	            	<td style="background-image: url(/images/line_bf.png);width:<? echo $filled_volume; ?>%;height:12px;"> </td>
	                <td style="background-image: url(/images/<? echo ($overload ? 'line_of' : 'line_b'); ?>.png);height:12px;width:<? echo (100 - $filled_volume); ?>%;height:12px;">  </td>
	                <td width=5>
					<?
						if($filled_volume > 99 || $overload) {
					?>
	                	<img src="/images/<? echo ($overload ? 'line_o' : 'line_rf'); ?>.png">
					<?
						}
						else {
					?>
	                	<img src="/images/line_r.png">
					<?
						}
					?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="padding-top:4px;">
		<? 
			if ( $left_volume < 0 )
			{
				$left_name = translate('перегрузка');
			}
			else
			{
				$left_name = translate('свободно');
			}
			
			echo round($used_volume, 1).'&nbsp;'.translate('из').'&nbsp;'.round($full_volume, 1).
				',&nbsp;'.$left_name.'&nbsp;'.abs(round($left_volume,2)).
				'&nbsp;'.$measure;
		?>
		</td>
	</tr>
	</table>
	<?
 } 
