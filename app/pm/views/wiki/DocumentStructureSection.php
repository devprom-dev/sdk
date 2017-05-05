<?php
include_once SERVER_ROOT_PATH . "core/views/ButtonInfoSection.php";

class DocumentStructureSection extends ButtonInfoSection
{
	function getId() {
		return 'toggle-structure-panel';
	}

 	function getCaption() {
 		return text(2204);
 	}

	function getIcon() {
		return 'icon-align-left';
	}
}