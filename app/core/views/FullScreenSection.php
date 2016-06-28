<?php
include_once "ButtonInfoSection.php";

class FullScreenSection extends ButtonInfoSection
{
	function getId() {
		return 'toggle-fullscreen';
	}

 	function getCaption() {
 		return text(1354);
 	}

	function getIcon() {
		return 'icon-fullscreen';
	}
}