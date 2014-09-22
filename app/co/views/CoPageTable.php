<?php
 
 /////////////////////////////////////////////////////////////////////////////////
 class CoPageTable extends PageTable
 {
 	function CoPageTable()
 	{
 		parent::__construct( getFactory()->getObject('entity') );
 	}
}
