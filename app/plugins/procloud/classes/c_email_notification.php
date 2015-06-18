<?php
/*
 * Content Management System
 * c_email_notification.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <saveug@mail.ru>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */
 
 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class EmailNotification extends Metaobject
 {
 	function EmailNotification() 
 	{
		parent::Metaobject('cms_EmailNotification');
	}

	function getPage()
	{
		return '/admin/module/procloud/notifications?';
	}
 }

?>