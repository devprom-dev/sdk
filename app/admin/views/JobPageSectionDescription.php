<?php

class JobDescriptionSection extends InfoSection
{
	function getCaption()
	{
		return translate('�������������');
	}

	function drawBody()
	{
		echo text(832);
	}
}
