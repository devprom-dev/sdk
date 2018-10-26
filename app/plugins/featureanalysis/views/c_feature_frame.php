<?php

 //////////////////////////////////////////////////////////////////////////////////////////////
 class FeatureAnalysisProgressFrame
 {
 	var $progress;
 	
 	function FeatureAnalysisProgressFrame( $progress )
 	{
 		$this->progress = $progress;
 	}
 	
 	function draw()
 	{
		$this->drawLine( text(991), $this->progress[0],
			$this->progress[1], '#E6B51E', '#F8EABE' );
 	}
 	
 	function drawLine( $stage, $total, $resloved, $color, $background )
 	{
 		$progress = floor(($total - $resloved) / $total * 100);
 		
		echo '<div class="line" title="'.$stage.' '.(100 - $progress).'%">';
			echo '<table width="100%" cellpadding=0 cellspacing=1 style="border:1px solid #d0d0e0;font-size:1px;height:8px;">';
				echo '<tr>';
					if ( $progress < 100 )
					{
						echo '<td width="'.(100 - $progress).'%" align=right style="background:#fffff2;">';
							echo '<div style="height:8px;background:'.$color.';font-size:1px;"> </div>';
						echo '</td>';
					}
					if ( $progress > 0 )
					{
						echo '<td width="'.$progress.'%" align=left style="background:#fffff2;">';
							echo '<div style="height:8px;background:'.$background.';font-size:1px;"> </div>';
						echo '</td>';
					}
				echo '</tr>';
			echo '</table>';
		echo '</div>';
 	}
 }

?>