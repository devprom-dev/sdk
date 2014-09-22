<?php

include "Participant.php";

class ParticipantDevelopmentTeam extends Participant
{
 	function getAll()
 	{
 		return $this->getDevelopmentTeam();
 	}
}
