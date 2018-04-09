<?php
include_once SERVER_ROOT_PATH."pm/classes/project/predicates/ProjectAccessibleActiveVpdPredicate.php";

class ProjectLinkedRegistry extends ObjectRegistrySQL
{
	public function getFilters()
	{
		$projects = array_filter(
			preg_split('/,/',
                join(',', array (
                    getSession()->getProjectIt()->get('LinkedProject'),
                    getSession()->getProjectIt()->get('PortfolioProject')
                ))
            ),
			function ($value) { return $value != ''; }
		);

		$className = getFactory()->getClass('ProjectLink');
		if ( class_exists($className) ) {
            $link_it = getFactory()->getObject('ProjectLink')->getRegistry()->Query(
                array (
                    new ProjectLinkProjectsPredicate(
                        join(',',array_merge( $projects, array(getSession()->getProjectIt()->getId()) ))
                    )
                )
            );
            $projects = array_merge($projects, $link_it->fieldToArray('Source'),$link_it->fieldToArray('Target'));
        }
        if ( count($projects) < 1 ) $projects = array(0);

		return array_merge(
			parent::getFilters(),
			array (
				new FilterInPredicate($projects),
				new ProjectAccessibleActiveVpdPredicate()
			)
		);
	}

	public function getSorts()
	{
		return array_merge(
			parent::getSorts(),
			array (
				new SortImportanceClause(),
				new SortAttributeClause('Caption.A')
			)
		);
	}
}