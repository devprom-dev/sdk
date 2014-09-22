<?php

class FormTransitionProjectRoleEmbedded extends PMFormEmbedded
{
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'ProjectRole':
 				return true;

 			default:
 				return false;
 		}
 	}
}
