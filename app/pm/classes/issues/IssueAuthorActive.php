<?php
include "IssueAuthorActiveRegistry.php";

class IssueAuthorActive extends IssueAuthor
{
 	function __construct( $registry = null ) {
 		parent::__construct(new IssueAuthorActiveRegistry());
 	}
}