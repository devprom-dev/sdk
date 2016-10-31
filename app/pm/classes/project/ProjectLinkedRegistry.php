<?php
include_once SERVER_ROOT_PATH."pm/classes/project/predicates/ProjectAccessibleVpdPredicate.php";

class ProjectLinkedRegistry extends ObjectRegistrySQL
{
	public function getFilters()
	{
		$projects = array_filter(
			preg_split('/,/', getSession()->getProjectIt()->get('LinkedProject')),
			function ($value) { return $value != ''; }
		);
		$link_it = getFactory()->getObject('ProjectLink')->getRegistry()->Query(
			array (
				new ProjectLinkProjectsPredicate(
					join(',',array_merge( $projects, array(getSession()->getProjectIt()->getId()) ))
				)
			)
		);
		$ids = array_merge($projects, $link_it->fieldToArray('Source'),$link_it->fieldToArray('Target'));
		if ( count($ids) < 1 ) $ids = array(0);

		return array_merge(
			parent::getFilters(),
			array (
				new FilterInPredicate($ids),
				new ProjectAccessibleVpdPredicate()
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