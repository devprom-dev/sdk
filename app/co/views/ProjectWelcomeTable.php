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
				'custom_template_exists' => false,
				'custom_template_url' => \EnvironmentSettings::getHelpDocsUrl('4652.html')
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
    	$urls = \EnvironmentSettings::getProcessDocsMap();

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
}
