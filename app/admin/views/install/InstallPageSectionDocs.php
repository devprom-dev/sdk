<?php

class DocumentationInfo extends InfoSection
{
	function getCaption()
	{
		return translate('Документация');
	}

	function drawBody()
	{
		echo '1. '.text(987);
		echo '<br/><br/>';

		echo '2. '.text(988);
		echo '<br/><br/>';
		
		echo '3. '.text(989);
		echo '<br/><br/>';
	}
}