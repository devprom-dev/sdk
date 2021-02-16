<?php

class FieldProjectTemplateDictionary extends FieldDictionary
{
    function __construct() {
        parent::__construct(getFactory()->getObject('pm_ProjectTemplate'));
    }

    function getGroups()
	{
		$groups = array();
		
		$language_it = getFactory()->getObject('cms_Language')->getRegistry()->Query(
			array (
				getSession()->getLanguageUid() == 'RU' ? new SortOrderedClause() : new SortRevOrderedClause()
			)
		);
		while( !$language_it->end() ) {
			$groups[$language_it->getId()] = array (
					'label' => $language_it->getDisplayName()
			);
			$language_it->moveNext();
		}
			
		return $groups;
	}
	
 	function getOptions()
	{
		$groups = array();

		$template_it = $this->getObject()->getRegistry()->Query(
			array (
				new SortAttributeClause('Kind.D'),
				new SortAttributeClause('OrderNum')
			)
		);
		while ( !$template_it->end() )
		{
			$groups[$template_it->get('Language')][] = array (
				'value' => $template_it->getId(),
				'caption' => $template_it->getDisplayName()
			);
			$template_it->moveNext();
		}

		$options = array();
		foreach( $groups as $group => $items ) {
			foreach( $items as $item ) {
				$options[] = array_merge( $item, array('group' => $group) );
			}
		}
		return $options;
	}
}