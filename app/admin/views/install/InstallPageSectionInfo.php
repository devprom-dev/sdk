<?php

class InstallationInfo extends InfoSection
{
	function getCaption()
	{
		return translate('Шаги установки');
	}

	function drawBody()
	{
		echo '1. '.text(440);
		echo '<br/><br/>';

		echo '2. '.text(441);
		echo '<br/><br/>';
		
		echo '3. '.text(442);
		echo '<br/><br/>';
	}
}
