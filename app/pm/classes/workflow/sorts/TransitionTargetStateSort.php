<?php

class TransitionTargetStateSort extends SortClauseBase
{
 	function clause() {
 		return " (SELECT s.OrderNum FROM pm_State s WHERE s.pm_StateId = ".$this->getAlias().".TargetState) ASC ";
 	}
}
