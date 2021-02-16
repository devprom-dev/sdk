<?php

class IssuesProgressFrame
{
 	var $progress, $drawtasks;
 	
 	function __construct( $progress, $drawtasks = true )
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
	 			$this->drawLine( text(2032), $this->progress['D'][0],
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

 		echo '<div style="display: table;">';
 		echo '<div style="display: table-cell;">';
 		echo '<div class="progress">';
 		    echo '<div class="bar '.($progress == 100 ? 'bar-success' : 'bar-warning').'" style="width:'.$progress.'%;"></div>';
 		echo '</div>';
 		echo '</div><div style="display: table-cell;width: 1%;vertical-align: top;padding-left: 4px;">';
 		echo $progress.'%';
 		echo '</div></div>';

		if ( $this->drawtasks && $stage == translate('Выполнено пожеланий') )
		{
 			echo '<div style="border-bottom:1px dotted grey;margin-top:3px;margin-bottom:8px;"></div>';
		}
 	}
}
