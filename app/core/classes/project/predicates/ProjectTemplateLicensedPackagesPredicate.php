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
			'testing_ru.xml' => 'qa',
			'ticket_en.xml' => 'support',
			'ticket_ru.xml' => 'support',
			'kanban_ru.xml' => 'agile',
			'kanban_en.xml' => 'agile',
			'scrum_en.xml' => 'agile',
			'scrum_ru.xml' => 'agile',
			'tasks_ru.xml' => 'agile',
			'tracker_ru.xml' => 'agile'
		);

		$options = getModelFactory()->getObject('LicenseState')->getAll()->get('Options');

		$packages = $options['options'];
		if ( $packages == '' ) return " AND 1 = 1 ";
		$packages = preg_split('/,\s?/', $packages);

		$templates = array();
		foreach( $package_map as $template => $package ) {
			if ( !in_array($package, $packages) ) {
				$templates[] = $template;
			}
		}
		if ( count($templates) < 1 ) return " AND 1 = 2 ";

 		return " AND t.FileName NOT IN ('".join("','",$templates)."') ";
 	}
}
