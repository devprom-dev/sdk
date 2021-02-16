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

		if ( $state_it->get('CommentObject') != '' ) {
			$comment_it = $state_it->getRef('CommentObject');
			$comment = $comment_it->getHtmlDecoded('Caption');
		}
		elseif ( $state_it->get('Comment') != '' ) {
			$comment = $state_it->get("Comment");
		}
		$sourceStateIt = $transition_it->getRef('SourceState');

		return array( $base_it->getDisplayName(), $transition_it->getDisplayName(), $comment, $base_it->get('ReferenceName'), $sourceStateIt );
 	}
 	
 	function getRenderParms()
	{
		$rows = array();
        $lastDuration = $duration = round($this->getObjectIt()->get('StateDurationRecent'), 1);
        $lastComment = '';
        $timeCreated = strtotime($this->getObjectIt()->get('RecordCreated'));

 		$state_it = $this->getIterator();
 		while ( !$state_it->end() )
 		{
			$duration = $state_it->get('Duration') != ''
					? round($state_it->get('Duration'), 1)
					: $duration;

			list( $state, $transition, $comment, $stateRef, $sourceStateIt ) = $this->getState( $state_it );

			$rows[] = array(
				'author' => $state_it->getRef('Author')->getDisplayName(),
			 	'datetime' => $state_it->getDateTimeFormat('RecordCreated'),
                'date' => $state_it->getDateFormattedShort('RecordCreated'),
				'duration' => getSession()->getLanguage()->getDurationWording($duration),
                'duration-value' => $duration,
				'state' => $state,
                'state-ref' => $stateRef,
				'transition' => $transition,
				'comment' => IteratorBase::getWordsOnlyValue($comment, 35),
				'icon' => 'icon-pencil'
			);

			$lastDuration = (strtotime($state_it->get('RecordCreated')) - $timeCreated) / (60 * 60);
			$lastState = $sourceStateIt->get('ReferenceName');
            if ( $lastComment == '' ) {
                $lastComment = $comment . ' ';
            }

            $state_it->moveNext();
 		}

 		$registry = new ObjectRegistrySQL();
 		$registry->setLimit(1);
 		
 		$object = getFactory()->getObject('ObjectChangeLog');
 		$object->setRegistry($registry);
        $object->setLimit(1);
 		
 		$change_it = $object->getRegistry()->Query(
            array (
                new ChangeLogItemDateFilter($this->object_it),
                new SortAttributeClause('RecordModified')
            )
		);

        $this->getObjectIt()->object->setVpdContext($this->getObjectIt());
        $objectStateIt = WorkflowScheme::Instance()->getStateIt($this->getObjectIt()->object);

		if ( $change_it->count() > 0 )
		{
            if ( $objectStateIt->getId() != '' ) {
                if ( count($rows) < 1 ) {
                    $objectStateIt->moveTo('ReferenceName', $this->getObjectIt()->get('State'));
                }
                else {
                    $objectStateIt->moveTo('ReferenceName', $lastState);
                }
                $rows[] = array(
                    'state' => $objectStateIt->getDisplayName(),
                    'state-ref' => $objectStateIt->get('ReferenceName'),
                    'author' => $change_it->get('AuthorName'),
                    'datetime' => $change_it->getDateTimeFormat('RecordModified'),
                    'date' => $change_it->getDateFormattedShort('RecordModified'),
                    'duration' => getSession()->getLanguage()->getDurationWording($lastDuration),
                    'duration-value' => $lastDuration,
                    'icon' => 'icon-plus-sign'
                );
            }
            $objectStateIt->moveFirst();
		}

		return array_merge( parent::getRenderParms(), array (
			'section' => $this,
			'rows' => $rows,
            'lifecycle' => getSession()->getLanguage()->getDurationWording($this->getObjectIt()->get('LeadTime')),
            'stateIt' => $objectStateIt,
            'placement' => $this->getPlacement(),
            'lastComment' => trim($lastComment),
            'stateUrl' => $objectStateIt->object->getPage(),
            'createDate' => $this->getObjectIt()->getDateTimeFormat('RecordCreated'),
            'startDate' => $this->getObjectIt()->getDateTimeFormat('StartDate'),
            'finishDate' => $this->getObjectIt()->getDateTimeFormat('FinishDate')
        ));
	}
 	
 	function getTemplate()
	{
		return 'pm/PageSectionLifecycle.php';
	}

	function getPlacement()
    {
        return 'bottom';
    }
 }
