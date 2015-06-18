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
 		global $model_factory;
 		
 		$state = $model_factory->getObject('pm_StateObject');
 		
 		$state->addSort( new SortReverseKeyClause() );

 		$state_it = $state->getByRefArray(
 			array ( 'ObjectId' => $this->object_it->getId(),
 					'ObjectClass' => $this->object_it->object->getStatableClassName() )
 			);

 		return $state_it;
 	}
 	
 	function getState( $state_it )
 	{
		$base_it = $state_it->getRef('State');
		$transition_it = $state_it->getRef('Transition');

		$state_name = preg_replace('/%1/', $base_it->getDisplayName(), text(905));
		
		if ( $transition_it->count() > 0 )
		{
			$transition_name = preg_replace('/%1/', $transition_it->getDisplayName(), text(904));
		}
		
		if ( $state_it->get('Comment') != '' )
		{
			$comment = $state_it->get("Comment");
		}
		
		return array( $state_name, $transition_name, $comment );
 	}
 	
 	function getRenderParms()
	{
		$rows = array();
		
 		$state_it = $this->getIterator();
 		
 		while ( !$state_it->end() )
 		{
			list( $state, $transition, $comment) = $this->getState( $state_it );
			
			$rows[] = array(
				'author' => $state_it->getRef('Author')->getDisplayName(),
			 	'datetime' => $state_it->getDateTimeFormat('RecordCreated'),
				'state' => $state,
				'transition' => $transition,
				'comment' => $comment,
				'icon' => 'icon-pencil'
			); 

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
