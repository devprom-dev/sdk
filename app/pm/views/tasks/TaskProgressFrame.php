<?php

class TaskProgressFrame
{
 	var $progress;
 	
 	function __construct( $progress )
 	{
 		global $model_factory;
 		
 		$this->progress = $progress;
 	}
 	
 	function draw()
 	{
		$this->drawLine( translate('Оставшаяся трудоемкость'), $this->progress[0],
			$this->progress[1], '#E6B51E', '#F8EABE' );
 	}
 	
 	function drawLine( $stage, $total, $resloved, $color, $background )
 	{
 		if ( $total > 0 )
 		{
 			$progress = round(($total - $resloved) / $total * 100, 0);
 		}
 		else
 		{
 			$progress = 0;
 		}
 		
		echo '<div style="display:table;width:100%;height:8px;" title="'.text(1854).'">';
			echo '<div style="display:table-cell;width:75%;">';
				echo '<table class="progress-table" width="100%" cellpadding=0 cellspacing=1 style="border:1px solid #d0d0e0;">';
					echo '<tr>';
						if ( $progress < 100 )
						{
							echo '<td width="'.(100 - $progress).'%" align=right style="background:#fffff2;">';
								echo '<div style="font-size:1px;height:8px;background:'.$color.';"> </div>';
							echo '</td>';
						}
						if ( $progress > 0 )
						{
							echo '<td width="'.$progress.'%" align=left style="background:#fffff2;">';
								echo '<div style="font-size:1px;height:8px;background:'.$background.';"> </div>';
							echo '</td>';
						}
					echo '</tr>';
				echo '</table>';
			echo '</div>';
			echo '<div style="display:table-cell;" title="'.$stage.'">';
				echo '&nbsp;'.round($total - $resloved, 1);
			echo '</div>';
		echo '</div>';
 	}
}