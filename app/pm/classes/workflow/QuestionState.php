<?php

include_once "StateBase.php";

class QuestionState extends StateBase
{
 	function getObjectClass()
 	{
 		return 'question';
 	}
 	
 	function getDisplayName()
 	{
 		return text(890);
 	}
}
