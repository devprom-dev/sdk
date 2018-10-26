<?php
/*
 * DEVPROM (http://www.devprom.net)
 * c_cofeedbackask.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */

 require_once (dirname(__FILE__).'/c_feedbackask.php');
 require_once (dirname(__FILE__).'/c_cofeedback_base.php');

 ////////////////////////////////////////////////////////////////////////////
 class CoFeedbackAsk extends FeedbackAsk
 {
 	function getProxy()
 	{
 		return new CoFeedbackBase;
 	}
 }
 
?>