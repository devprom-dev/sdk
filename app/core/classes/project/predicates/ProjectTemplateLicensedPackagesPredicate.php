<?php

class ProjectTemplateLicensedPackagesPredicate extends FilterPredicate
{
	function __construct()
	{
		parent::__construct('dummy');
	}

 	function _predicate( $filter )
 	{
		$package_map = array (
			'docs_ru.xml' => 'docs',
			'incidents_en.xml' => 'support',
			'incidents_ru.xml' => 'support',
			'reqs_ru.xml' => 'reqs',
            'spec_ru.xml' => 'reqs',
            'sdlc_ru.xml' => 'reqs',
			'testing_ru.xml' => 'qa',
			'ticket_en.xml' => 'support',
			'ticket_ru.xml' => 'support',
			'kanban_ru.xml' => 'agile,core',
			'kanban_en.xml' => 'agile,core',
			'scrum_en.xml' => 'agile,core',
			'scrum_ru.xml' => 'agile,core',
            'scrumban_ru.xml' => 'agile,core',
			'tasks_ru.xml' => 'agile,core',
			'tracker_ru.xml' => 'qa'
		);

		$options = getFactory()->getObject('LicenseState')->getAll()->get('Options');

		$packages = $options['options'];
		if ( $packages == '' ) $packages = join(',',$this->getLicensedOptions());
		$packages = preg_split('/,\s?/', $packages);
		foreach( $packages as $key => $package ) {
            list($package, $users) = preg_split('/:/', $package);
            $packages[$key] = $package;
        }

		$templates = array();
		foreach( $package_map as $template => $package ) {
			if ( count(array_intersect(preg_split('/,\s?/',$package), $packages)) < 1 ) {
				$templates[] = $template;
			}
		}
 		return count($templates) < 1
				? " AND 1 = 1 "
				: " AND t.FileName NOT IN ('".join("','",$templates)."') ";
 	}

 	function getLicensedOptions()
    {
        $options_map = array (
            'helpdocs' => 'docs',
            'support' => 'support',
            'requirements' => 'reqs',
            'testing' => 'qa'
        );
        $options = array('core');
        foreach( $this->getLicensedPlugins() as $name ) {
            $option = $options_map[$name];
            if ( $option != '' ) $options[] = $option;
        }
        return $options;
    }

 	function getLicensedPlugins()
    {
        $plugins = array_filter(
            getFactory()->getPluginsManager()->getNamespaces(),
            function (PluginBase $plugin) {
                return $plugin->checkLicense();
            }
        );
        $names = array_map(
            function($item) {
                return $item->getNamespace();
            },
            $plugins
        );
        return  $names;
    }
}
