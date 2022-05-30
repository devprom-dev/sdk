<?php

class AutoActionEventRegistry extends ObjectRegistrySQL
{
    const None = 1;
	const CreateAndModify = 2;
	const CreateOnly = 3;
	const ModifyOnly = 4;
	const NewComment = 5;
    const Schedule = 6;

 	function createSQLIterator( $sql )
 	{
 		return $this->createIterator(array(
            array (
                'entityId' => self::None,
                'Caption' => text(2335)
            ),
            array (
                'entityId' => self::CreateAndModify,
                'Caption' => text(2336)
            ),
            array (
                'entityId' => self::CreateOnly,
                'Caption' => text(2337)
            ),
            array (
                'entityId' => self::ModifyOnly,
                'Caption' => text(2338)
            ),
            array (
                'entityId' => self::NewComment,
                'Caption' => text(2339)
            ),
            array (
                'entityId' => self::Schedule,
                'Caption' => text(3105)
            )
 		));
 	}
}