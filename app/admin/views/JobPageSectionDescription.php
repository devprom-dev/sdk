<?php

class JobDescriptionSection extends InfoSection
{
	function getCaption()
	{
		return translate('Дополнительно');
	}

	function drawBody()
	{
		echo text(832);
	}
}
