<?php

/////////////////////////////////////////////////////////////////////////////////
class CoLinksPageContent extends CoPageContent
{
	function validate()
	{
		return true;
	}
	
	function getTitle()
	{
		return translate('Ссылки').' - '.parent::getTitle();
	}
	
	function getKeywords()
	{
		return parent::getKeywords().' '.translate('ссылки').' '.
			translate('партнеры').' '.translate('ссылка');
	}

	function draw()
	{
		global $model_factory, $project_it;
		
		$project = $model_factory->getObject('pm_Project');

		$project_it = $project->getByRef('CodeName', 'procloud');
		$session = new PMSession( $project_it );
		
		$this->drawProjectHeader( '<a href="/links">'.translate('Ссылки').'</a>' );

		$this->drawItems();

		echo '<div style="clear:both;">&nbsp;</div>';
	}

	function drawItems()
	{	
	}
}

?>
