<?php

 class StatableLifecycleSection extends InfoSection
 {
 	var $object_it;
 	
 	function StatableLifecycleSection( $object_it )
 	{
 		$this->object_it = $object_it;
 		parent::InfoSection();
 	}
 	
 	function getCaption()
 	{
 		return translate('Жизненный цикл');
 	}
 	
 	function getObjectIt()
 	{
 		return $this->object_it;
 	}
 	
 	function getIterator()
 	{
 		return getFactory()->getObject('pm_StateObject')->getRegistry()->Query(
				array(
					new FilterAttributePredicate('ObjectId', $this->object_it->getId()),
					new FilterAttributePredicate('ObjectClass', $this->object_it->object->getStatableClassName()),
					new SortReverseKeyClause()
				)
		);
 	}
 	
 	function getState( $state_it )
 	{
		$base_it = $state_it->getRef('State');
		$transition_it = $state_it->getRef('Transition');

		$state_name = preg_replace('/%1/', $base_it->getDisplayName(), text(905));
		
		if ( $transition_it->count() > 0 ) {
			$transition_name = preg_replace('/%1/', $transition_it->getDisplayName(), text(904));
		}

		if ( $state_it->get('CommentObject') != '' ) {
			$comment_it = $state_it->getRef('CommentObject');
			$comment = $comment_it->getHtmlDecoded('Caption');
		}
		elseif ( $state_it->get('Comment') != '' ) {
			$comment = $state_it->get("Comment");
		}
		
		return array( $state_name, $transition_name, $comment );
 	}
 	
 	function getRenderParms()
	{
		$rows = array();
		$duration = round($this->getObjectIt()->get('StateDuration'), 1);

 		$state_it = $this->getIterator();
 		while ( !$state_it->end() )
 		{
			$duration = $state_it->get('Duration') != ''
					? round($state_it->get('Duration'), 1)
					: $duration;

			list( $state, $transition, $comment) = $this->getState( $state_it );
			
			$rows[] = array(
				'author' => $state_it->getRef('Author')->getDisplayName(),
			 	'datetime' => $state_it->getDateTimeFormat('RecordCreated'),
				'duration' => $duration,
				'state' => $state,
				'transition' => $transition,
				'comment' => $comment,
				'icon' => 'icon-pencil'
			);
			$duration = round((strtotime($state_it->get('RecordCreated')) - strtotime($this->getObjectIt()->get('RecordCreated'))) / (60 * 60), 1);
 			$state_it->moveNext();
 		}

 		$registry = new ObjectRegistrySQL();
 		$registry->setLimit(1);
 		
 		$object = getFactory()->getObject('ObjectChangeLog');
 		$object->setRegistry($registry);
 		
 		$change_it = $object->getRegistry()->Query(
				array (
						new ChangeLogItemDateFilter($this->object_it),
						new SortAttributeClause('RecordCreated')
				)
		);
 		
		if ( $change_it->count() > 0 )
		{
			$rows[] = array(
				'author' => $change_it->get('AuthorName'),
			 	'datetime' => $change_it->getDateTimeFormat('RecordCreated'),
				'duration' => $duration,
				'icon' => 'icon-plus-sign'
			); 
		}

		return array_merge( parent::getRenderParms(), array (
			'section' => $this,
			'rows' => $rows
		));
	}
 	
 	function getTemplate()
	{
		return 'pm/PageSectionLifecycle.php';
	}
}  
