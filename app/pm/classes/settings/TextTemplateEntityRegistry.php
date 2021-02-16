<?php
include "predicates/TextTemplateEntityPredicate.php";

class TextTemplateEntityRegistry extends ObjectRegistrySQL
{
	public function createSQLIterator($sql)
	{
        $data = array();
        foreach( array_keys(self::getEntities()) as $entity ) {
            if ( !class_exists($entity) ) continue;
            $object = getFactory()->getObject($entity);
            $data[] = array (
                'entityId' => $entity,
                'Caption' => $object->getDisplayName()
            );
        }

		return $this->createIterator($data);
	}

	public static function getEntities()
    {
        $values = array(
            'Task' => 'Description',
            'Release' => 'Description',
            'Iteration' => 'Description',
            'Milestone' => 'Description',
            'Build' => 'Description',
            'Feature' => 'Description',
            'Comment' => 'Caption',
            'ProjectPage' => 'Content',
            'Requirement' => 'Content',
            'TestScenario' => 'Content',
            'HelpPage' => 'Content'
        );

        if ( getSession()->IsRDD() )
        {
            $values['Issue'] = 'Description';
            $values['Increment'] = 'Description';
        }
        else {
            $values['Request'] = 'Description';
        }
        return $values;
    }
}