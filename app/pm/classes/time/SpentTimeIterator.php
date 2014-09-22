<?php

class SpentTimeIterator extends OrderedIterator
{
 	var $activities_map, $days_map, $comments_map;
 	
 	function setDaysMap( $map )
 	{
 		$this->days_map = $map;
 	}
 	
 	function getDaysMap()
	{
		return $this->days_map;
	}
	
 	function setActivitiesMap( $map )
 	{
 		$this->activities_map = $map;
 	}
	
	function getActivitiesMap()
	{
		return $this->activities_map;
	}
	
 	function setCommentsMap( $map )
 	{
 		$this->comments_map = $map;
 	}
	
	function getCommentsMap()
	{
		return $this->comments_map;
	}
}
