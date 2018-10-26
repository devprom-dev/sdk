<?php

class ProjectWelcomeTable extends CoPageTable
{
    private $solutionsEnabled = false;

    function getTemplate()
    {
		return '../../co/views/templates/ProjectWelcomeTable.tpl.php';
    }

	function getLanguageId() {
		return is_numeric($_REQUEST['language']) ? $_REQUEST['language'] : getSession()->getLanguage()->getLanguageId();
	}

    function getRenderParms( $parms )
    {
		$languages = getFactory()->getObject('ProjectTemplate')->getAll()->fieldToArray('Language');
		if ( count($languages) < 1 ) $languages = array(0);

		$plugins = getFactory()->getPluginsManager()->getNamespaces();
        $processPlugins = array_filter($plugins, function ($plugin) {
            return strtolower($plugin->getNamespace()) == 'process';
        });
        $processManagementEnabled = !empty($processPlugins) && array_shift($processPlugins)->checkLicense();

    	return array_merge(
			parent::getRenderParms($parms),
			array (
				'languages' =>
					getFactory()->getObject('cms_Language')->getRegistry()->Query(
						array(
							new FilterInPredicate($languages)
						)
					)->getRowset(),
				'language_selected' => $this->getLanguageId(),
				'section_class' => 'create-project',
				'tiles' => 
						$this->buildTiles(),
				'solutions' => 
						$this->buildSolutions(),
				'custom_template_exists' => false,
				'custom_template_url' =>
                    $processManagementEnabled
							? 'http://devprom.ru/features/%D0%A3%D0%BD%D0%B8%D1%84%D0%B8%D0%BA%D0%B0%D1%86%D0%B8%D1%8F-%D0%B8-%D0%BF%D0%BE%D0%B4%D0%B4%D0%B5%D1%80%D0%B6%D0%BA%D0%B0-%D0%BF%D1%80%D0%BE%D1%86%D0%B5%D1%81%D1%81%D0%BE%D0%B2-%D0%B2-%D0%BA%D0%BE%D0%BC%D0%BF%D0%B0%D0%BD%D0%B8%D0%B8?lookingforsolution'
							: ''
			)
    	);
    }
    
    function buildTiles()
    {
    	$template = getFactory()->getObject('ProjectTemplate');
    	$active_it = $template->getRegistry()->Query(
    			array (
    					new FilterAttributePredicate('Language', $this->getLanguageId()),
    					new SortOrderedClause()
    			)
    		);
    	
    	$template->setRegistry( new ObjectRegistrySQL() );
    	$total_it = $template->getRegistry()->Query(
    			array (
    					new FilterAttributePredicate('Language', $this->getLanguageId()),
    					new SortOrderedClause()
    			)
    		);

    	$tiles = array();
    	$urls = $this->buildTilesUrls();
    	
    	while( !$total_it->end() )
    	{
			$idQuery = $total_it->getId();

			if ( is_numeric($_REQUEST['portfolio']) ) {
				$idQuery .= '&portfolio='.$_REQUEST['portfolio'];
			}
            if ( is_numeric($_REQUEST['program']) ) {
                $idQuery .= '&program='.$_REQUEST['program'];
            }

			$active = $active_it->moveToId($total_it->getId())->getId() > 0;
			if ( $total_it->get('FileName') == 'reqs_ru.xml' ) {
			    $this->solutionsEnabled = true;
            }

    		$tiles[] = array (
                'kind' => $total_it->get('Kind'),
                'name' => $total_it->getHtml('Caption'),
                'description' => $total_it->getHtml('Description'),
                'id' => $idQuery,
                'active' => $active,
                'url' => $urls[$total_it->get('FileName')],
                'file' => $total_it->get('FileName')
    		);
    		
    		$total_it->moveNext();
    	}
    	
    	return $tiles;
    }
    
    function buildTilesUrls()
    {
    	return array (
			'ticket_ru.xml' => 'http://devprom.ru/features/%D0%9E%D1%80%D0%B3%D0%B0%D0%BD%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D1%8F-%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D1%8B-%D1%81%D0%BB%D1%83%D0%B6%D0%B1%D1%8B-%D1%82%D0%B5%D1%85%D0%BD%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%BE%D0%B9-%D0%BF%D0%BE%D0%B4%D0%B4%D0%B5%D1%80%D0%B6%D0%BA%D0%B8-%D1%81-Devprom-Service-Desk?lookingforalmfeature',
			'reqs_ru.xml' => 'http://devprom.ru/features/%D0%A1%D0%B8%D1%81%D1%82%D0%B5%D0%BC%D0%B0-%D1%83%D0%BF%D1%80%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D0%B8%D1%8F-%D1%82%D1%80%D0%B5%D0%B1%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F%D0%BC%D0%B8-Devprom-Requirements?lookingforalmfeature',
			'scrum_ru.xml' => 'http://devprom.ru/features/Scrum-для-поиска-и-разработки-новых-продуктов',
			'tracker_ru.xml' => 'http://devprom.ru/features/%D0%A1%D0%B8%D1%81%D1%82%D0%B5%D0%BC%D0%B0-%D1%83%D0%BF%D1%80%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D0%B8%D1%8F-%D1%82%D0%B5%D1%81%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5%D0%BC-Devprom-QA?lookingforalmfeature',
			'sdlc_ru.xml' => 'http://devprom.ru/features/Координация-всех-активностей-по-компании?lookingforalmfeature',
			'tasks_ru.xml' => 'http://devprom.ru/features/Организация-работы-отдела',
			'kanban_ru.xml' => 'http://devprom.ru/features/Kanban-для-оптимизации-потока-задач',
            'scrumban_ru.xml' => 'http://devprom.ru/features/Scrumban-для-развития-продуктов'
    	);
    }
    
    function buildSolutions()
    {
        if ( !$this->solutionsEnabled ) return array();
    	if ( getSession()->getLanguageUid() != 'RU' ) return array();
    	
    	return array (
			array (
				'name' => text('co32'),
				'description' => text('co33'),
				'url' => 'http://devprom.ru/features/Разработка-и-развитие-продуктов-или-сервисов?lookingforsolution'
			),
			array (
				'name' => text('co48'),
				'description' => text('co49'),
				'url' => 'http://devprom.ru/features/Внедрение-типовых-решений?lookingforsolution'
			),
			array (
				'name' => text('co42'),
				'description' => text('co43'),
				'url' => 'http://devprom.ru/features/%D0%9A%D0%B0%D1%81%D1%82%D0%BE%D0%BC%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D1%8F-%D0%BA%D0%BE%D1%80%D0%BE%D0%B1%D0%BE%D1%87%D0%BD%D0%BE%D0%B3%D0%BE-%D0%BF%D1%80%D0%BE%D0%B4%D1%83%D0%BA%D1%82%D0%B0-%D0%B8%D0%BB%D0%B8-%D0%BF%D0%BB%D0%B0%D1%82%D1%84%D0%BE%D1%80%D0%BC%D1%8B?lookingforsolution'
			),
			array (
				'name' => text('co36'),
				'description' => text('co37'),
				'url' => 'http://devprom.ru/features/%D0%90%D1%83%D1%82%D1%81%D0%BE%D1%80%D1%81%D0%B8%D0%BD%D0%B3-%D1%80%D0%B0%D0%B7%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D0%BA%D0%B8-%D0%BF%D1%80%D0%BE%D0%B3%D1%80%D0%B0%D0%BC%D0%BC%D0%BD%D0%BE%D0%B3%D0%BE-%D0%BE%D0%B1%D0%B5%D1%81%D0%BF%D0%B5%D1%87%D0%B5%D0%BD%D0%B8%D1%8F?lookingforsolution'
			),
			array (
				'name' => text('co46'),
				'description' => text('co47'),
				'url' => 'http://devprom.ru/features/Поддержка-ИТ-систем-в-бизнесе?lookingforsolution'
			)
    	);
    }
}
