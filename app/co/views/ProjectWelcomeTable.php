<?php

class ProjectWelcomeTable extends CoPageTable
{
    function getTemplate()
    {
		return '../../co/views/templates/ProjectWelcomeTable.tpl.php';
    }
    
    function getRenderParms( $parms )
    {
    	$method = new SettingsWebMethod();
    	
    	return array_merge(
    			parent::getRenderParms($parms),
    			array (
    					'section_class' => 'create-project',
    					'tiles' => 
    							$this->buildTiles(),
    					'solutions' => 
    							$this->buildSolutions(),
    					'javascript_skip' => 
    							$method->getJSCall( 
					    				array('setting' => $skip_setting_name, 'value' => 'off'), 
					    				"function() {} "
					    		),
    					'custom_template_exists' =>
    							getFactory()->getObject('ProjectTemplate')->getRegistry()->Count(
    									array (new FilterAttributePredicate('ProductEdition', 'none'))
    							) > 0,
    					'custom_template_url' => 
    							class_exists('FunctionalAreaMenuProcessBuilder', false)
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
    					new FilterAttributePredicate('Language', getSession()->getLanguage()->getLanguageId()),
    					new SortOrderedClause()
    			)
    		);
    	
    	$template->setRegistry( new ObjectRegistrySQL() );
    	$total_it = $template->getRegistry()->Query(
    			array (
    					new FilterAttributePredicate('Language', getSession()->getLanguage()->getLanguageId()),
    					new SortOrderedClause()
    			)
    		);

    	$tiles = array();
    	$urls = $this->buildTilesUrls();
    	
    	while( !$total_it->end() )
    	{
    		$tiles[] = array (
    				'kind' => $total_it->get('Kind'),
    				'name' => $total_it->getHtml('Caption'),
    				'description' => $total_it->getHtml('Description'),
    				'id' => $total_it->getId(),
    				'active' => $active_it->moveToId($total_it->getId())->getId() > 0,
    				'url' => $urls[$total_it->get('FileName')]
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
    			'scrum_ru.xml' => 'http://devprom.ru/features/%D0%98%D0%BD%D1%81%D1%82%D1%80%D1%83%D0%BC%D0%B5%D0%BD%D1%82-%D1%83%D0%BF%D1%80%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D0%B8%D1%8F-%D0%BF%D1%80%D0%BE%D0%B5%D0%BA%D1%82%D0%B0%D0%BC%D0%B8-Devprom-AgileTeam?lookingforalmfeature',
    			'testing_ru.xml' => 'http://devprom.ru/features/%D0%A1%D0%B8%D1%81%D1%82%D0%B5%D0%BC%D0%B0-%D1%83%D0%BF%D1%80%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D0%B8%D1%8F-%D1%82%D0%B5%D1%81%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5%D0%BC-Devprom-QA?lookingforalmfeature',
    			'sdlc_ru.xml' => 'http://devprom.ru/features/%D0%A1%D0%B8%D1%81%D1%82%D0%B5%D0%BC%D0%B0-%D1%83%D0%BF%D1%80%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D0%B8%D1%8F-%D0%B6%D0%B8%D0%B7%D0%BD%D0%B5%D0%BD%D0%BD%D1%8B%D0%BC-%D1%86%D0%B8%D0%BA%D0%BB%D0%BE%D0%BC-%D1%80%D0%B0%D0%B7%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D0%BA%D0%B8-%D0%9F%D0%9E-Devprom-ALM?lookingforalmfeature',
    			'ba_ru.xml' => 'http://devprom.ru/features/%D0%9A%D0%BE%D0%BC%D0%B0%D0%BD%D0%B4%D0%BD%D1%8B%D0%B9-%D1%81%D0%B1%D0%BE%D1%80-%D1%82%D1%80%D0%B5%D0%B1%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B9-%D1%81-%D0%BF%D0%BE%D0%BC%D0%BE%D1%89%D1%8C%D1%8E-Story-Mapping?lookingforalmfeature',
    			'docs_ru.xml' => 'http://devprom.ru/features/%D0%A0%D0%B0%D0%B7%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D0%BA%D0%B0-%D1%82%D0%B5%D1%85%D0%BD%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%BE%D0%B9-%D0%B4%D0%BE%D0%BA%D1%83%D0%BC%D0%B5%D0%BD%D1%82%D0%B0%D1%86%D0%B8%D0%B8?lookingforalmfeature'
    	);
    }
    
    function buildSolutions()
    {
    	if ( getSession()->getLanguageUid() != 'RU' ) return array();
    	
    	return array (
    			array (
    					'name' => text('co32'),
    					'description' => text('co33'),
    					'url' => 'http://devprom.ru/features/%D0%A0%D0%B5%D1%88%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BE-%D0%BE%D1%80%D0%B3%D0%B0%D0%BD%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8-%D0%BF%D0%BE%D0%B4%D0%B4%D0%B5%D1%80%D0%B6%D0%BA%D0%B8-%D0%BF%D1%80%D0%BE%D0%B3%D1%80%D0%B0%D0%BC%D0%BC%D0%BD%D1%8B%D1%85-%D0%BF%D1%80%D0%BE%D0%B4%D1%83%D0%BA%D1%82%D0%BE%D0%B2?lookingforsolution'
    			),
    			array (
    					'name' => text('co36'),
    					'description' => text('co37'),
    					'url' => 'http://devprom.ru/features/%D0%90%D1%83%D1%82%D1%81%D0%BE%D1%80%D1%81%D0%B8%D0%BD%D0%B3-%D1%80%D0%B0%D0%B7%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D0%BA%D0%B8-%D0%BF%D1%80%D0%BE%D0%B3%D1%80%D0%B0%D0%BC%D0%BC%D0%BD%D0%BE%D0%B3%D0%BE-%D0%BE%D0%B1%D0%B5%D1%81%D0%BF%D0%B5%D1%87%D0%B5%D0%BD%D0%B8%D1%8F?lookingforsolution'
    			),
    			array (
    					'name' => text('co38'),
    					'description' => text('co39'),
    					'url' => 'http://devprom.ru/features/%D0%9A%D0%BE%D0%BE%D1%80%D0%B4%D0%B8%D0%BD%D0%B0%D1%86%D0%B8%D1%8F-%D0%B2%D1%81%D0%B5%D1%85-%D0%B0%D0%BA%D1%82%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D0%B5%D0%B9-%D0%BF%D0%BE-%D0%BA%D0%BE%D0%BC%D0%BF%D0%B0%D0%BD%D0%B8%D0%B8?lookingforsolution'
    			),
    			array (
    					'name' => text('co40'),
    					'description' => text('co41'),
    					'url' => 'http://devprom.ru/features/%D0%9F%D0%BB%D0%B0%D0%BD%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5-%D0%B1%D0%B0%D0%BB%D0%B0%D0%BD%D1%81%D0%B8%D1%80%D0%BE%D0%B2%D0%BA%D0%B0-%D0%B8-%D0%BA%D0%BE%D0%BD%D1%82%D1%80%D0%BE%D0%BB%D1%8C-%D0%B7%D0%B0%D0%B3%D1%80%D1%83%D0%B7%D0%BA%D0%B8-%D1%80%D0%B5%D1%81%D1%83%D1%80%D1%81%D0%BE%D0%B2?lookingforsolution'
    			),
    			array (
    					'name' => text('co46'),
    					'description' => text('co47'),
    					'url' => 'http://devprom.ru/features/%D0%9F%D0%BE%D0%B4%D0%B4%D0%B5%D1%80%D0%B6%D0%BA%D0%B0-%D0%98%D0%A2-%D1%81%D0%B8%D1%81%D1%82%D0%B5%D0%BC-%D0%BD%D0%B0%D0%BF%D1%80%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D0%B8%D1%8F-%D0%B1%D0%B8%D0%B7%D0%BD%D0%B5%D1%81%D0%B0?lookingforsolution'
    			),
    			array (
    					'name' => text('co42'),
    					'description' => text('co43'),
    					'url' => 'http://devprom.ru/features/%D0%9A%D0%B0%D1%81%D1%82%D0%BE%D0%BC%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D1%8F-%D0%BA%D0%BE%D1%80%D0%BE%D0%B1%D0%BE%D1%87%D0%BD%D0%BE%D0%B3%D0%BE-%D0%BF%D1%80%D0%BE%D0%B4%D1%83%D0%BA%D1%82%D0%B0-%D0%B8%D0%BB%D0%B8-%D0%BF%D0%BB%D0%B0%D1%82%D1%84%D0%BE%D1%80%D0%BC%D1%8B?lookingforsolution'
    			),
    			array (
    					'name' => text('co44'),
    					'description' => text('co45'),
    					'url' => 'http://devprom.ru/features/%D0%A0%D0%B0%D0%B7%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D0%BA%D0%B0-%D0%BF%D1%80%D0%BE%D0%B4%D1%83%D0%BA%D1%82%D0%B0-%D0%BF%D0%BE%D0%B4-%D0%BD%D0%B5%D1%81%D0%BA%D0%BE%D0%BB%D1%8C%D0%BA%D0%BE-%D0%BF%D0%BB%D0%B0%D1%82%D1%84%D0%BE%D1%80%D0%BC?lookingforsolution'
    			),
    			array (
    					'name' => text('co48'),
    					'description' => text('co49'),
    					'url' => 'http://devprom.ru/features/%D0%9E%D1%80%D0%B3%D0%B0%D0%BD%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D1%8F-%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D1%8B-%D0%BE%D1%82%D0%B4%D0%B5%D0%BB%D0%B0?lookingforsolution'
    			)
    	);
    }
}
