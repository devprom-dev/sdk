<?php
include_once SERVER_ROOT_PATH . "core/views/ButtonInfoSection.php";

class DetailsInfoSection extends ButtonInfoSection
{
	function getId() {
		return 'toggle-detailspanel';
	}

 	function getCaption() {
 		return text(2165);
 	}

	function getIcon() {
		return 'icon-indent-right';
	}
}