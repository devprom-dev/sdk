<?php
 
 class PriorityFrame
 {
 	var $map;
 	
 	function PriorityFrame()
 	{
 		global $model_factory;
 		
 		$priority = $model_factory->getObject('Priority');
 		$priority_it = $priority->getAll();
 		
 		$this->map = $priority_it->idsToArray();
 		$this->map = array_flip($this->map);
 	}
 	
 	function getIcon( $priority )
 	{ 
		switch ( $this->map[$priority] )
		{
			case '0':
				$icon = 'pcritical.png';
				break;
				
			case '1':
				$icon = 'phigh.png';
				break;
				
			case '2':
				$icon = 'pnormal.png';
				break;
				
			default:
				$icon = 'plow.png';
		}
		
		return $icon;
 	}
 	
 	function getColor( $priority )
 	{
		switch ( $this->map[$priority] )
		{
			case '0':
				$icon = '#F9E9DF';
				break;
				
			case '1':
				$icon = '#F9F5DF';
				break;

			default:
				$icon = '#f0f0f6';
		}
		
		return $icon;
 	}
 }
 
?>