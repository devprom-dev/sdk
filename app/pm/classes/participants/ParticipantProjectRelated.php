<?php

include_once "Participant.php";
include "ParticipantProjectRelatedRegistry.php";

class ParticipantProjectRelated extends Participant
{
	public function __construct()
	{
		parent::__construct( new ParticipantProjectRelatedRegistry() );
	}
}