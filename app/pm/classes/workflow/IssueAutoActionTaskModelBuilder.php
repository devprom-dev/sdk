<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class IssueAutoActionTaskModelBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof IssueAutoAction ) return;

        $methodologyIt = getSession()->getProjectIt()->getMethodologyIt();
        if ( !$methodologyIt->HasTasks() ) return;

    	$task = getFactory()->getObject('Task');

    	$attributes = array_diff(
            array_keys($task->getAttributes()),
            $task->getAttributesByGroup('system'),
            $task->getAttributesByGroup('trace'),
            array(
                'OrderNum',
                'ChangeRequest',
                'Fact'
            )
        );

    	foreach( $attributes as $attribute ) {
            $groups = $task->getAttributeGroups($attribute);
            if ( !$task->IsAttributePersisted($attribute) ) continue;
            if ( !$task->IsAttributeVisible($attribute) ) continue;
            if ( in_array('computed', $groups) ) continue;

            $key = 'Task_'.$attribute;
            $object->addAttribute(
                $key,
                $task->getAttributeDbType($attribute),
                $task->getAttributeUserName($attribute),
                true,
                false
            );
            $object->addAttributeGroup($key, 'task');
            $object->setAttributeType('Task_Assignee', 'REF_ProjectUserId');
        }
   }
}