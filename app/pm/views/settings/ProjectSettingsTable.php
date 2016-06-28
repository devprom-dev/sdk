<?php

class ProjectSettingsTable extends PMPageTable
{
    function getTemplate()
    {
		return 'pm/ProjectSettingsTable.tpl.php';
    }
    
    function getRenderParms( $parms )
    {
		getSession()->insertBuilder( new \FunctionalAreaMenuSettingsBuilder() );
		$area_menu = new \FunctionalAreaMenuSet();

    	return array_merge(
			parent::getRenderParms($parms),
			array (
				'sections' => $this->getSettings($area_menu->getAll()->getData())
			)
    	);
    }

	function getSettings( $area )
	{
		$report = getFactory()->getObject('PMReport');
		$module = getFactory()->getObject('Module');
		$resource = getFactory()->getObject('ContextResource');

		$sections = array();
		foreach( $area['items'] as $menuId => $menu ) {
			if ( $menuId == 'quick' ) {
				$menu['name'] = text(2171);
			}
			$items = array();
			foreach( $menu['items'] as $widget ) {
				if ( $widget['report'] != '' ) {
					$widget_it = $report->getExact($widget['report']);
				}
				else if ( $widget['module'] != '' ) {
					$widget_it = $module->getExact($widget['module']);
				}
				else {
					continue;
				}

				$resource_it = $resource->getExact($widget_it->getId());
				$description =  $resource_it->getId() != ''
					? $resource_it->getHtmlDecoded('Caption') :  $widget_it->getHtmlDecoded('Description');

				$totext = new \Html2Text\Html2Text($description);
				$description = $totext->getText();
				if ( mb_strlen($description) > 140 ) $description = mb_substr($description, 0, 140).'...';

				$items[] = array (
					'name' => $widget_it->getDisplayName(),
					'url' => $widget_it->getUrl(),
					'description' => $description
				);
			}
			if ( count($items) > 0 ) {
				$sections[] = array (
					'name' => $menu['name'],
					'items' => $items
				);
			}
		}
		return $sections;
	}
}