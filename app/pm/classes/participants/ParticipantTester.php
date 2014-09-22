<?php

include_once "Participant.php";

class ParticipantTester extends Participant
{
 	function getAll()
 	{
 		return $this->getTesterIt();
 	}
}
