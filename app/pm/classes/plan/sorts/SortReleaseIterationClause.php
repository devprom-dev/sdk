<?php

class SortReleaseIterationClause extends SortClauseBase
{
 	function clause()
 	{
 		return " (SELECT v.Caption FROM pm_Version v WHERE v.pm_VersionId = t.Version) ASC ";
 	}
}
