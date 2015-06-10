<?php

 if ( !class_exists('ReleaseIssuesProgressFrame') )
 {
 	include ('c_request_frame.php');
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class IterationProgressFrame
 {
 	var $release_it;
 	var $participant_it;
 	
 	function IterationProgressFrame( $release_it )
 	{
 		global $model_factory;
 		$participant = $model_factory->getObject('pm_Participant');
 		
 		$this->release_it = $release_it;
        $this->participant_it = $participant->getAll();
  	}

 	function draw()
 	{
 		global $model_factory;
 		
 		$task = $model_factory->getObject('pm_Task');
 		if( !is_object($this->participant_it) ) return;
 		
		echo '<div style="width:100%;">';
			echo '<div class="line">';
				$text = translate('Начало').': ';
				$text .= $this->release_it->getDateFormat('StartDate').
					', ';
				$text .= translate('окончание').': '; 
			    $text .= $this->release_it->getDateFormat('FinishDate');
			    
			    echo str_replace(' ', '&nbsp;', $text);

				$offset = $this->release_it->getFinishOffsetDays();
				if ( $offset > 0 )
				{
					echo '&nbsp;<span style="color:red;" title="'.translate('Отклонение от графика').'">+ '.$offset.' '.translate('дн.').'</span>';
				}	

			echo '</div>';

			$frame = new ReleaseIssuesProgressFrame( $this->release_it, $this->release_it->getProgress() );
			$frame->draw();
		echo '</div>';
 	}
 }

?>