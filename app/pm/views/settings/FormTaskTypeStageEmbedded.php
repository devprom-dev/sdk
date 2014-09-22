<?php

class FormTaskTypeStageEmbedded extends PMFormEmbedded
{
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'ProjectStage':
 				return true;

 			default:
 				return false;
 		}
 	}
}
